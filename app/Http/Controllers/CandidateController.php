<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    /**
     * Vérifier que l'utilisateur est manager ou admin
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
     * Liste des candidats
     */
    public function index(Request $request)
    {
        $this->checkAccess();

        $query = Candidate::with(['creator', 'recruiter']);

        // Filtres
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

        // Statistiques
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
     * Formulaire de création
     */
    public function create()
    {
        $this->checkAccess();

        $recruiters = User::whereIn('role', ['manager', 'administrateur'])
                          ->where('is_active', true)
                          ->orderBy('first_name')
                          ->get();

        $departementsFrancais = $this->getDepartementsFrancais();

        return view('recruitment.create', compact('recruiters', 'departementsFrancais'));
    }

    /**
     * Enregistrer un nouveau candidat
     */
    public function store(Request $request)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position_applied' => 'nullable|string|max:255',
            'desired_location' => 'nullable|string|max:255',
            'available_from' => 'nullable|date',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'source' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'notes' => 'nullable|string|max:5000',
        ]);

        // Gestion du CV
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('candidates/cv', 'public');
            $validated['cv_path'] = $cvPath;
        }

        // Gestion de la lettre de motivation
        if ($request->hasFile('cover_letter')) {
            $coverPath = $request->file('cover_letter')->store('candidates/cover_letters', 'public');
            $validated['cover_letter_path'] = $coverPath;
        }

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'new';

        unset($validated['cv'], $validated['cover_letter']);

        Candidate::create($validated);

        return redirect()->route('recruitment.index')->with('success', 'Candidat ajouté avec succès ! 🎉');
    }

    /**
     * Afficher un candidat
     */
    public function show(Candidate $candidate)
    {
        $this->checkAccess();

        $candidate->load(['creator', 'recruiter', 'convertedUser']);

        return view('recruitment.show', compact('candidate'));
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Candidate $candidate)
    {
        $this->checkAccess();

        $recruiters = User::whereIn('role', ['manager', 'administrateur'])
                          ->where('is_active', true)
                          ->orderBy('first_name')
                          ->get();

        $departementsFrancais = $this->getDepartementsFrancais();

        return view('recruitment.edit', compact('candidate', 'recruiters', 'departementsFrancais'));
    }

    /**
     * Mettre à jour un candidat
     */
    public function update(Request $request, Candidate $candidate)
    {
        $this->checkAccess();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'position_applied' => 'nullable|string|max:255',
            'desired_location' => 'nullable|string|max:255',
            'available_from' => 'nullable|date',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
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
        ]);

        // Gestion du CV
        if ($request->hasFile('cv')) {
            if ($candidate->cv_path) {
                Storage::disk('public')->delete($candidate->cv_path);
            }
            $cvPath = $request->file('cv')->store('candidates/cv', 'public');
            $validated['cv_path'] = $cvPath;
        }

        // Gestion de la lettre de motivation
        if ($request->hasFile('cover_letter')) {
            if ($candidate->cover_letter_path) {
                Storage::disk('public')->delete($candidate->cover_letter_path);
            }
            $coverPath = $request->file('cover_letter')->store('candidates/cover_letters', 'public');
            $validated['cover_letter_path'] = $coverPath;
        }

        unset($validated['cv'], $validated['cover_letter']);

        $candidate->update($validated);

        return redirect()->route('recruitment.show', $candidate)->with('success', 'Candidat mis à jour avec succès !');
    }

    /**
     * Supprimer un candidat
     */
    public function destroy(Candidate $candidate)
    {
        $this->checkAccess();

        if ($candidate->cv_path) {
            Storage::disk('public')->delete($candidate->cv_path);
        }
        if ($candidate->cover_letter_path) {
            Storage::disk('public')->delete($candidate->cover_letter_path);
        }

        $candidate->delete();

        return redirect()->route('recruitment.index')->with('success', 'Candidat supprimé avec succès.');
    }

    /**
     * Mettre à jour le statut rapidement
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
     * Liste des départements français
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