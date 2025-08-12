<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InternalRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class InternalRequestController extends Controller
{
    /**
     * Afficher la liste des demandes selon le cahier des charges
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Base query selon les permissions utilisateur
        $query = InternalRequest::forUser($user)
            ->with(['requester', 'approver', 'assignedUser']);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            if (!empty($searchTerm)) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }
        }

        // Tri par priorité (urgent en premier) puis par date
        $requests = $query->orderByRaw("
                CASE status 
                    WHEN 'en_attente' THEN 1 
                    WHEN 'valide' THEN 2 
                    WHEN 'en_cours' THEN 3 
                    WHEN 'termine' THEN 4 
                    WHEN 'rejete' THEN 5 
                END
            ")
            ->orderBy('requested_at', 'desc')
            ->paginate(15);

        $requests->appends($request->all());

        // Statistiques pour le dashboard
        $stats = [
            'total' => InternalRequest::forUser($user)->count(),
            'en_attente' => InternalRequest::forUser($user)->pending()->count(),
            'valide' => InternalRequest::forUser($user)->approved()->count(),
            'rejete' => InternalRequest::forUser($user)->rejected()->count(),
        ];

        return view('requests.index', compact('requests', 'stats', 'request'));
    }

    /**
     * Formulaire de création de demande
     */
    public function create()
    {
        $types = [
            'achat_produit_communication' => 'Achat produit communication',
            'documentation_manager' => 'Documentation manager',
            'prestation' => 'Prestation',
        ];

        $prestationTypes = [
            'location' => 'Location',
            'syndic' => 'Syndic',
            'menage' => 'Ménage',
            'travaux' => 'Travaux',
            'autres_administratifs' => 'Autres administratifs',
        ];

        return view('requests.create', compact('types', 'prestationTypes'));
    }

    /**
     * Enregistrer une nouvelle demande
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'type' => 'required|in:achat_produit_communication,documentation_manager,prestation',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'prestation_type' => 'nullable|required_if:type,prestation|in:location,syndic,menage,travaux,autres_administratifs',
            'comments' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
        ], [
            'type.required' => 'Le type de demande est obligatoire.',
            'title.required' => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'prestation_type.required_if' => 'Le type de prestation est obligatoire pour les demandes de prestation.',
            'estimated_cost.numeric' => 'Le coût estimé doit être un nombre.',
        ]);

        $requestData = [
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'comments' => $validated['comments'] ?? null,
            'prestation_type' => $validated['prestation_type'] ?? null,
            'estimated_cost' => $validated['estimated_cost'] ?? null,
            'status' => 'en_attente',
            'requested_by' => $user->id,
            'requested_at' => now(),
        ];

        $internalRequest = InternalRequest::create($requestData);

        return redirect()->route('requests.show', $internalRequest)
            ->with('success', 'Demande créée avec succès ! Elle est maintenant en attente de validation.');
    }

    /**
     * Afficher une demande complète
     */
    public function show(InternalRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier les permissions
        if (!$this->canUserViewRequest($user, $request)) {
            abort(403, 'Vous n\'avez pas accès à cette demande.');
        }

        $request->load(['requester', 'approver', 'assignedUser']);

        // Utilisateurs pouvant approuver (pour managers/admin)
        $approvers = collect();
        if ($user->isManager() || $user->isAdministrateur()) {
            $approvers = User::where('is_active', true)
                ->whereIn('role', ['manager', 'administrateur'])
                ->orderBy('first_name')
                ->get();
        }

        return view('requests.show', compact('request', 'approvers'));
    }

    /**
     * Formulaire d'édition de demande
     */
    public function edit(InternalRequest $request)
    {
        $user = Auth::user();

        if (!$this->canUserEditRequest($user, $request)) {
            abort(403, 'Vous ne pouvez pas modifier cette demande.');
        }

        $types = [
            'achat_produit_communication' => 'Achat produit communication',
            'documentation_manager' => 'Documentation manager',
            'prestation' => 'Prestation',
        ];

        $prestationTypes = [
            'location' => 'Location',
            'syndic' => 'Syndic',
            'menage' => 'Ménage',
            'travaux' => 'Travaux',
            'autres_administratifs' => 'Autres administratifs',
        ];

        return view('requests.edit', compact('request', 'types', 'prestationTypes'));
    }

    /**
     * Mettre à jour une demande
     */
    public function update(Request $httpRequest, InternalRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérification que l'objet est bien chargé
        if (!$request || !$request->exists) {
            abort(404, 'Demande non trouvée');
        }

        if (!$this->canUserEditRequest($user, $request)) {
            abort(403, 'Vous ne pouvez pas modifier cette demande. Statut: ' . ($request->status ?? 'non défini'));
        }

        $validated = $httpRequest->validate([
            'type' => 'required|in:achat_produit_communication,documentation_manager,prestation',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'prestation_type' => 'nullable|required_if:type,prestation|in:location,syndic,menage,travaux,autres_administratifs',
            'comments' => 'nullable|string',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        $updateData = [
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'comments' => $validated['comments'] ?? null,
            'prestation_type' => $validated['prestation_type'] ?? null,
            'estimated_cost' => $validated['estimated_cost'] ?? null,
        ];

        $request->update($updateData);

        return redirect()->route('requests.show', $request)
            ->with('success', 'Demande mise à jour avec succès !');
    }

    /**
     * Approuver une demande (managers/admin)
     */
    public function approve(Request $httpRequest, InternalRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isManager() && !$user->isAdministrateur()) {
            abort(403);
        }

        $validated = $httpRequest->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'comments' => 'nullable|string',
        ]);

        $assignedTo = isset($validated['assigned_to']) && $validated['assigned_to'] ? User::find($validated['assigned_to']) : null;

        if ($request->approve($user, $assignedTo)) {
            if (!empty($validated['comments'])) {
                $request->update(['comments' => $validated['comments']]);
            }

            return back()->with('success', 'Demande approuvée avec succès !');
        }

        return back()->withErrors(['error' => 'Impossible d\'approuver cette demande.']);
    }

    /**
     * Rejeter une demande (managers/admin)
     */
    public function reject(Request $httpRequest, InternalRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isManager() && !$user->isAdministrateur()) {
            abort(403);
        }

        $validated = $httpRequest->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Une raison de rejet est obligatoire.',
        ]);

        if ($request->reject($user, $validated['rejection_reason'])) {
            return back()->with('success', 'Demande rejetée.');
        }

        return back()->withErrors(['error' => 'Impossible de rejeter cette demande.']);
    }

    /**
     * Marquer une demande comme terminée
     */
    public function complete(InternalRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isManager() && !$user->isAdministrateur() && $request->assigned_to !== $user->id) {
            abort(403);
        }

        if ($request->complete()) {
            return back()->with('success', 'Demande marquée comme terminée !');
        }

        return back()->withErrors(['error' => 'Impossible de terminer cette demande.']);
    }

    /**
     * Supprimer une demande
     */
    public function destroy(InternalRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur() && $request->requested_by !== $user->id) {
            abort(403);
        }

        $request->delete();

        return redirect()->route('requests.index')
            ->with('success', 'Demande supprimée avec succès !');
    }

    /**
     * Vue admin des demandes
     */
    public function adminIndex()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        // Statistiques globales
        $stats = [
            'total' => InternalRequest::count(),
            'pending' => InternalRequest::pending()->count(),
            'approved' => InternalRequest::approved()->count(),
            'rejected' => InternalRequest::rejected()->count(),
            'completed' => InternalRequest::completed()->count(),
        ];

        // Demandes récentes
        $recentRequests = InternalRequest::with(['requester', 'approver'])
            ->orderBy('requested_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.requests.index', compact('stats', 'recentRequests'));
    }

    /**
     * API pour statistiques admin
     */
    public function getStats()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        return response()->json([
            'approval_rate' => InternalRequest::getApprovalRate(),
            'average_processing_time' => InternalRequest::getAverageProcessingTime(),
            'requests_by_type' => InternalRequest::selectRaw('type, count(*) as count')
                ->groupBy('type')
                ->get(),
            'requests_by_month' => InternalRequest::selectRaw('MONTH(requested_at) as month, count(*) as count')
                ->whereYear('requested_at', now()->year)
                ->groupBy('month')
                ->get(),
        ]);
    }

    /**
     * Vérifier si un utilisateur peut voir une demande
     */
    private function canUserViewRequest($user, $request): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $request->requested_by === $user->id
                || $request->approved_by === $user->id
                || $request->assigned_to === $user->id
                || $request->requester->manager_id === $user->id;
        }

        return $request->requested_by === $user->id;
    }

    /**
     * Vérifier si un utilisateur peut modifier une demande
     */
    private function canUserEditRequest($user, $request): bool
    {

        // L'administrateur peut toujours modifier
        if ($user->isAdministrateur()) {
            return true;
        }

        // Le demandeur peut modifier sa propre demande si elle n'est pas terminée ou rejetée
        if ($request->requested_by === $user->id) {
            $allowedStatuses = ['en_attente', 'valide', 'en_cours'];
            $canEdit = in_array($request->status, $allowedStatuses);

            return $canEdit;
        }

        // Les managers peuvent modifier les demandes de leur équipe si pas terminées/rejetées
        if ($user->isManager() && $request->requester && $request->requester->manager_id === $user->id) {
            $canEdit = in_array($request->status, ['en_attente', 'valide', 'en_cours']);
            return $canEdit;
        }

        return false;
    }
}
