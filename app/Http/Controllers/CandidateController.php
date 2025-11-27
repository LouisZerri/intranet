<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\User;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CandidateController extends Controller
{
    protected GoogleDriveService $googleDrive;

    public function __construct(GoogleDriveService $googleDrive)
    {
        $this->googleDrive = $googleDrive;
    }

    /**
     * Vérifie que l'utilisateur est manager ou administrateur, sinon 403
     */
    private function checkAccess()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdministrateur()) {
            abort(403, 'Accès non autorisé. Seuls les managers et administrateurs peuvent accéder au recrutement.');
        }
    }

    /**
     * Retourne les règles de validation pour les documents
     */
    private function getDocumentValidationRules(): array
    {
        return [
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'identity_card' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'proof_of_address' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'legal_status' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'rcp_insurance' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'signed_contract' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'criminal_record' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'rib' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'training_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ];
    }

    /**
     * Construit le nom du fichier envoyé sur Google Drive
     */
    private function getDocumentFileName(string $type, Candidate|array $candidate, string $extension): string
    {
        $firstName = is_array($candidate) ? $candidate['first_name'] : $candidate->first_name;
        $lastName = is_array($candidate) ? $candidate['last_name'] : $candidate->last_name;

        $prefixes = [
            'cv' => 'CV',
            'cover_letter' => 'LM',
            'identity_card' => 'CNI',
            'proof_of_address' => 'Justificatif_Domicile',
            'legal_status' => 'Statut_Juridique',
            'rcp_insurance' => 'RCP',
            'signed_contract' => 'Contrat',
            'criminal_record' => 'Casier_Judiciaire',
            'rib' => 'RIB',
            'training_certificate' => 'Attestation_Formation',
        ];

        $prefix = $prefixes[$type] ?? $type;
        return "{$prefix}_{$firstName}_{$lastName}.{$extension}";
    }

    /**
     * Gère l'upload des documents d'un candidat sur Google Drive
     */
    private function uploadDocuments(Request $request, array &$validated, ?Candidate $candidate = null): void
    {
        $documentTypes = array_keys($this->getDocumentValidationRules());
        $hasFiles = false;

        // Détecte si au moins un fichier est à uploader
        foreach ($documentTypes as $type) {
            if ($request->hasFile($type)) {
                $hasFiles = true;
                break;
            }
        }

        if (!$hasFiles) {
            return;
        }

        $candidateName = $validated['first_name'] . ' ' . $validated['last_name'];

        // Récupère ou crée le dossier Drive du candidat
        $folderId = $candidate?->google_drive_folder_id 
            ?? $this->googleDrive->getOrCreateCandidateFolder($candidateName);
        
        $validated['google_drive_folder_id'] = $folderId;

        foreach ($documentTypes as $type) {
            if ($request->hasFile($type)) {
                $file = $request->file($type);
                $docTypes = Candidate::getDocumentTypes();
                
                if (!isset($docTypes[$type])) {
                    continue;
                }

                $pathField = $docTypes[$type]['path_field'];
                $linkField = $docTypes[$type]['link_field'];

                // Supprime l'ancien fichier le cas échéant
                if ($candidate && $candidate->$pathField) {
                    try {
                        $this->googleDrive->deleteFile($candidate->$pathField);
                    } catch (\Exception $e) {
                        Log::warning("Impossible de supprimer l'ancien fichier: " . $e->getMessage());
                    }
                }

                $fileName = $this->getDocumentFileName(
                    $type, 
                    $validated, 
                    $file->getClientOriginalExtension()
                );

                $driveFile = $this->googleDrive->uploadFile($folderId, $file, $fileName);
                
                $validated[$pathField] = $driveFile->id;
                $validated[$linkField] = $driveFile->webViewLink;
            }
        }
    }

    /**
     * Affiche la liste des candidats, avec filtres et stats
     */
    public function index(Request $request)
    {
        $this->checkAccess();

        $query = Candidate::with(['creator', 'recruiter']);

        // Gestion des filtres de recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Calcul rapide des statistiques principales
        $stats = [
            'total' => Candidate::count(),
            'new' => Candidate::where('status', 'new')->count(),
            'in_progress' => Candidate::whereIn('status', ['in_review', 'interview'])->count(),
            'recruited' => Candidate::whereIn('status', ['recruited', 'integrated'])->count(),
            'refused' => Candidate::where('status', 'refused')->count(),
        ];

        $candidates = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $recruiters = User::whereIn('role', ['manager', 'administrateur'])
                          ->where('is_active', true)
                          ->orderBy('first_name')
                          ->get();

        $departementsFrancais = $this->getDepartementsFrancais();

        return view('recruitment.index', compact('candidates', 'stats', 'recruiters', 'departementsFrancais'));
    }

    /**
     * Page de formulaire de création
     */
    public function create()
    {
        $this->checkAccess();

        $recruiters = User::whereIn('role', ['manager', 'administrateur'])
                          ->where('is_active', true)
                          ->orderBy('first_name')
                          ->get();

        $departementsFrancais = $this->getDepartementsFrancais();
        $documentTypes = Candidate::getDocumentTypes();

        return view('recruitment.create', compact('recruiters', 'departementsFrancais', 'documentTypes'));
    }

    /**
     * Traite la création d'un candidat
     */
    public function store(Request $request)
    {
        $this->checkAccess();

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position_applied' => 'nullable|string|max:255',
            'desired_location' => 'nullable|string|max:255',
            'available_from' => 'nullable|date',
            'source' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:5000',
        ];

        // Ajoute règles documents
        $rules = array_merge($rules, $this->getDocumentValidationRules());

        $validated = $request->validate($rules);

        // Gère upload Google Drive
        try {
            $this->uploadDocuments($request, $validated);
        } catch (\Exception $e) {
            Log::error('Erreur Google Drive lors de la création du candidat: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erreur lors de l\'upload des fichiers sur Google Drive: ' . $e->getMessage());
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'new';

        // Supprime les fichiers du tableau validated (ne vont pas en bdd)
        foreach (array_keys($this->getDocumentValidationRules()) as $type) {
            unset($validated[$type]);
        }

        Candidate::create($validated);

        return redirect()->route('recruitment.index')->with('success', 'Candidat ajouté avec succès ! 🎉');
    }

    /**
     * Affiche le détail d'un candidat
     */
    public function show(Candidate $candidate)
    {
        $this->checkAccess();

        $candidate->load(['creator', 'recruiter', 'convertedUser']);
        $documentTypes = Candidate::getDocumentTypes();

        return view('recruitment.show', compact('candidate', 'documentTypes'));
    }

    /**
     * Page de formulaire d'édition
     */
    public function edit(Candidate $candidate)
    {
        $this->checkAccess();

        $recruiters = User::whereIn('role', ['manager', 'administrateur'])
                          ->where('is_active', true)
                          ->orderBy('first_name')
                          ->get();

        $departementsFrancais = $this->getDepartementsFrancais();
        $documentTypes = Candidate::getDocumentTypes();

        return view('recruitment.edit', compact('candidate', 'recruiters', 'departementsFrancais', 'documentTypes'));
    }

    /**
     * Traite la mise à jour d'un candidat
     */
    public function update(Request $request, Candidate $candidate)
    {
        $this->checkAccess();

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position_applied' => 'nullable|string|max:255',
            'desired_location' => 'nullable|string|max:255',
            'available_from' => 'nullable|date',
            'rating_motivation' => 'nullable|integer|min:1|max:5',
            'rating_seriousness' => 'nullable|integer|min:1|max:5',
            'rating_experience' => 'nullable|integer|min:1|max:5',
            'rating_commercial_skills' => 'nullable|integer|min:1|max:5',
            'notes' => 'nullable|string|max:5000',
            'interview_notes' => 'nullable|string|max:5000',
            'status' => 'required|in:new,in_review,interview,recruited,integrated,refused',
            'source' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'interview_date' => 'nullable|date',
            'decision_date' => 'nullable|date',
        ];

        // Ajoute règles documents
        $rules = array_merge($rules, $this->getDocumentValidationRules());

        $validated = $request->validate($rules);

        // Gère upload Google Drive
        try {
            $this->uploadDocuments($request, $validated, $candidate);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Erreur lors de l\'upload des fichiers sur Google Drive: ' . $e->getMessage());
        }

        // Supprime les fichiers du tableau validated
        foreach (array_keys($this->getDocumentValidationRules()) as $type) {
            unset($validated[$type]);
        }

        $candidate->update($validated);

        return redirect()->route('recruitment.show', $candidate)->with('success', 'Candidat mis à jour avec succès !');
    }

    /**
     * Supprime un candidat et son dossier Google Drive
     */
    public function destroy(Candidate $candidate)
    {
        $this->checkAccess();

        // Supprime le dossier Google Drive du candidat si présent
        try {
            if ($candidate->google_drive_folder_id) {
                $this->googleDrive->deleteFolder($candidate->google_drive_folder_id);
            }
        } catch (\Exception $e) {
            Log::warning('Impossible de supprimer le dossier Google Drive: ' . $e->getMessage());
        }

        $candidate->delete();

        return redirect()->route('recruitment.index')->with('success', 'Candidat supprimé avec succès.');
    }

    /**
     * Mise à jour rapide du statut (ajax ou non)
     */
    public function updateStatus(Request $request, Candidate $candidate)
    {
        $this->checkAccess();

        $request->validate([
            'status' => 'required|in:new,in_review,interview,recruited,integrated,refused',
        ]);

        $candidate->update(['status' => $request->status]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status_label' => $candidate->status_label,
                'status_color' => $candidate->status_color,
            ]);
        }

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    /**
     * Retourne la liste des départements français
     */
    private function getDepartementsFrancais(): array
    {
        return [
            'Ain', 'Aisne', 'Allier', 'Alpes-de-Haute-Provence', 'Hautes-Alpes',
            'Alpes-Maritimes', 'Ardèche', 'Ardennes', 'Ariège', 'Aube', 'Aude',
            'Aveyron', 'Bouches-du-Rhône', 'Calvados', 'Cantal', 'Charente',
            'Charente-Maritime', 'Cher', 'Corrèze', 'Corse-du-Sud', 'Haute-Corse',
            'Côte-d\'Or', 'Côtes-d\'Armor', 'Creuse', 'Dordogne', 'Doubs', 'Drôme',
            'Eure', 'Eure-et-Loir', 'Finistère', 'Gard', 'Haute-Garonne', 'Gers',
            'Gironde', 'Hérault', 'Ille-et-Vilaine', 'Indre', 'Indre-et-Loire',
            'Isère', 'Jura', 'Landes', 'Loir-et-Cher', 'Loire', 'Haute-Loire',
            'Loire-Atlantique', 'Loiret', 'Lot', 'Lot-et-Garonne', 'Lozère',
            'Maine-et-Loire', 'Manche', 'Marne', 'Haute-Marne', 'Mayenne',
            'Meurthe-et-Moselle', 'Meuse', 'Morbihan', 'Moselle', 'Nièvre', 'Nord',
            'Oise', 'Orne', 'Pas-de-Calais', 'Puy-de-Dôme', 'Pyrénées-Atlantiques',
            'Hautes-Pyrénées', 'Pyrénées-Orientales', 'Bas-Rhin', 'Haut-Rhin', 'Rhône',
            'Haute-Saône', 'Saône-et-Loire', 'Sarthe', 'Savoie', 'Haute-Savoie',
            'Paris', 'Seine-Maritime', 'Seine-et-Marne', 'Yvelines', 'Deux-Sèvres',
            'Somme', 'Tarn', 'Tarn-et-Garonne', 'Var', 'Vaucluse', 'Vendée', 'Vienne',
            'Haute-Vienne', 'Vosges', 'Yonne', 'Territoire de Belfort', 'Essonne',
            'Hauts-de-Seine', 'Seine-Saint-Denis', 'Val-de-Marne', 'Val-d\'Oise',
            'Guadeloupe', 'Martinique', 'Guyane', 'La Réunion', 'Mayotte'
        ];
    }
}