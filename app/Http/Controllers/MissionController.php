<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mission;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MissionController extends Controller
{
    /**
     * Afficher la liste des missions avec filtres par catégorie
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $query = Mission::forUser($user)
            ->with(['assignedUser', 'creator', 'manager']);

        // Filtres existants
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // NOUVEAU: Filtre par catégorie
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // NOUVEAU: Filtre par sous-catégorie
        if ($request->filled('subcategory')) {
            $query->where('subcategory', $request->subcategory);
        }

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            if (!empty($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }
        }

        if ($request->filled('assigned_to') && ($user->isManager() || $user->isAdministrateur())) {
            $query->where('assigned_to', $request->assigned_to);
        }

        $missions = $query->orderByRaw("
                CASE priority 
                    WHEN 'urgente' THEN 1 
                    WHEN 'haute' THEN 2 
                    WHEN 'normale' THEN 3 
                    WHEN 'basse' THEN 4 
                END
            ")
            ->orderBy('due_date', 'asc')
            ->paginate(15);

        $missions->appends($request->all());

        // Statistiques
        $stats = [
            'total' => Mission::forUser($user)->count(),
            'en_cours' => Mission::forUser($user)->inProgress()->count(),
            'termine' => Mission::forUser($user)->completed()->count(),
            'en_retard' => Mission::forUser($user)->overdue()->count(),
        ];

        // Liste des collaborateurs pour les filtres
        $collaborateurs = collect();
        if ($user->isManager()) {
            $collaborateurs = $user->subordinates()->where('is_active', true)->get();
        } elseif ($user->isAdministrateur()) {
            $collaborateurs = User::where('is_active', true)->where('role', '!=', 'administrateur')->get();
        }

        // NOUVEAU: Données pour les filtres de catégorie
        $categories = Mission::getCategories();
        $subcategories = Mission::getSubcategories();

        return view('missions.index', compact(
            'missions', 
            'stats', 
            'collaborateurs', 
            'request', 
            'categories', 
            'subcategories'
        ));
    }

    public function show(Mission $mission)
    {
        $user = Auth::user();

        if (!$this->canUserViewMission($user, $mission)) {
            abort(403, 'Vous n\'avez pas accès à cette mission.');
        }

        $mission->load(['assignedUser', 'creator', 'manager']);

        return view('missions.show', compact('mission'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $priorities = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];

        $statuses = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours'
        ];

        // NOUVEAU: Ajout des catégories
        $categories = Mission::getCategories();
        $subcategories = Mission::getSubcategories();

        $collaborateurs = $this->getAssignableUsers($user);

        return view('missions.create', compact(
            'priorities', 
            'statuses', 
            'collaborateurs',
            'categories',
            'subcategories'
        ));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:basse,normale,haute,urgente',
            'status' => 'required|in:en_attente,en_cours',
            'category' => 'required|in:location,syndic,autres',
            'subcategory' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after:today',
            'start_date' => 'nullable|date',
            'revenue' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ], [
            'title.required' => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'category.required' => 'La catégorie est obligatoire.',
            'subcategory.required' => 'La sous-catégorie est obligatoire.',
            'assigned_to.required' => 'Vous devez assigner la mission à quelqu\'un.',
            'due_date.after' => 'L\'échéance doit être dans le futur.',
        ]);

        // Validation personnalisée de la sous-catégorie
        $subcategories = Mission::getSubcategories();
        if (!isset($subcategories[$validated['category']][$validated['subcategory']])) {
            return back()->withErrors(['subcategory' => 'La sous-catégorie sélectionnée n\'est pas valide.']);
        }

        $assignedUser = User::findOrFail($validated['assigned_to']);
        if (!$this->canAssignToUser($user, $assignedUser)) {
            return back()->withErrors(['assigned_to' => 'Vous ne pouvez pas assigner de missions à cet utilisateur.']);
        }

        $missionData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'category' => $validated['category'],
            'subcategory' => $validated['subcategory'],
            'assigned_to' => $validated['assigned_to'],
            'created_by' => $user->id,
            'manager_id' => $assignedUser->manager_id ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'revenue' => $validated['revenue'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        $mission = Mission::create($missionData);

        return redirect()->route('missions.show', $mission)->with('success', 'Mission créée avec succès !');
    }

    public function edit(Mission $mission)
    {
        $user = Auth::user();
        
        if (!$this->canUserEditMission($user, $mission)) {
            abort(403, 'Vous ne pouvez pas modifier cette mission.');
        }

        $priorities = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];

        $statuses = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'annule' => 'Annulé'
        ];

        // NOUVEAU: Ajout des catégories pour l'édition
        $categories = Mission::getCategories();
        $subcategories = Mission::getSubcategories();

        $collaborateurs = $this->getAssignableUsers($user);

        return view('missions.edit', compact(
            'mission', 
            'priorities', 
            'statuses', 
            'collaborateurs',
            'categories',
            'subcategories'
        ));
    }

    public function update(Request $request, Mission $mission)
    {
        $user = Auth::user();
        
        if (!$this->canUserEditMission($user, $mission)) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:basse,normale,haute,urgente',
            'status' => 'required|in:en_attente,en_cours,termine,annule',
            'category' => 'required|in:location,syndic,autres',
            'subcategory' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'revenue' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        // Validation personnalisée de la sous-catégorie
        $subcategories = Mission::getSubcategories();
        if (!isset($subcategories[$validated['category']][$validated['subcategory']])) {
            return back()->withErrors(['subcategory' => 'La sous-catégorie sélectionnée n\'est pas valide.']);
        }

        $assignedUser = User::findOrFail($validated['assigned_to']);
        if (!$this->canAssignToUser($user, $assignedUser)) {
            return back()->withErrors(['assigned_to' => 'Vous ne pouvez pas assigner de missions à cet utilisateur.']);
        }

        $updateData = [
            'title' => $validated['title'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'category' => $validated['category'],
            'subcategory' => $validated['subcategory'],
            'assigned_to' => $validated['assigned_to'],
            'due_date' => $validated['due_date'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'revenue' => $validated['revenue'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ];

        if ($validated['status'] === 'termine' && $mission->status !== 'termine') {
            $updateData['completed_at'] = now();
        } elseif ($validated['status'] !== 'termine') {
            $updateData['completed_at'] = null;
        }

        $mission->update($updateData);

        return redirect()->route('missions.show', $mission)->with('success', 'Mission mise à jour avec succès !');
    }

    public function destroy(Mission $mission)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdministrateur() && $mission->created_by !== $user->id) {
            abort(403);
        }

        $mission->delete();

        return redirect()->route('missions.index')->with('success', 'Mission supprimée avec succès !');
    }

    /**
     * NOUVELLE MÉTHODE: API pour récupérer les sous-catégories
     */
    public function getSubcategories(Request $request)
    {
        $category = $request->get('category');
        $subcategories = Mission::getSubcategories();
        
        if (isset($subcategories[$category])) {
            return response()->json($subcategories[$category]);
        }
        
        return response()->json([]);
    }

    // Méthodes de permissions (inchangées)
    private function canUserViewMission($user, $mission): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $mission->assigned_to === $user->id 
                || $mission->created_by === $user->id 
                || $mission->manager_id === $user->id
                || $mission->assignedUser->manager_id === $user->id;
        }

        return $mission->assigned_to === $user->id || $mission->created_by === $user->id;
    }

    private function canUserEditMission($user, $mission): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $mission->created_by === $user->id 
                || $mission->manager_id === $user->id
                || $mission->assignedUser->manager_id === $user->id;
        }

        return $mission->assigned_to === $user->id || $mission->created_by === $user->id;
    }

    private function canAssignToUser($user, $targetUser): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $targetUser->manager_id === $user->id || $targetUser->id === $user->id;
        }

        if ($targetUser->id === $user->id) {
            return true;
        }

        if ($user->manager_id && $user->manager_id === $targetUser->manager_id) {
            return true;
        }

        return false;
    }

    private function getAssignableUsers($user)
    {
        if ($user->isAdministrateur()) {
            return User::where('is_active', true)->orderBy('first_name')->get();
        }

        if ($user->isManager()) {
            return User::where('is_active', true)
                ->where(function($q) use ($user) {
                    $q->where('manager_id', $user->id)
                      ->orWhere('id', $user->id);
                })
                ->orderBy('first_name')
                ->get();
        }

        $assignableUsers = collect([$user]);

        if ($user->manager_id) {
            $colleagues = User::where('is_active', true)
                ->where('manager_id', $user->manager_id)
                ->where('id', '!=', $user->id)
                ->orderBy('first_name')
                ->get();
            
            $assignableUsers = $assignableUsers->merge($colleagues);
        }

        return $assignableUsers->sortBy('first_name');
    }
}