<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Formation;
use App\Models\FormationRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FormationController extends Controller
{
    /**
     * Catalogue des formations disponibles
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Base query
        $query = Formation::available()->with(['creator']);

        // Filtres
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('level')) {
            $query->byLevel($request->level);
        }

        if ($request->filled('format')) {
            $query->where('format', $request->format);
        }

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            if (!empty($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('provider', 'like', "%{$searchTerm}%");
                });
            }
        }

        // Tri
        $sortBy = $request->get('sort', 'title');
        $sortOrder = $request->get('order', 'asc');
        
        if (in_array($sortBy, ['title', 'duration_hours', 'cost', 'start_date'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('title');
        }

        $formations = $query->paginate(12);
        $formations->appends($request->all());

        // Statistiques pour l'utilisateur
        $userStats = [
            'total_requests' => FormationRequest::where('user_id', $user->id)->count(),
            'pending_requests' => FormationRequest::where('user_id', $user->id)->pending()->count(),
            'completed_formations' => FormationRequest::where('user_id', $user->id)->completed()->count(),
            'hours_completed' => FormationRequest::where('user_id', $user->id)->completed()->sum('hours_completed'),
        ];

        // Options pour les filtres
        $categories = Formation::active()->whereNotNull('category')->distinct()->pluck('category');
        $levels = ['debutant', 'intermediaire', 'avance'];
        $formats = ['presentiel', 'distanciel', 'hybride'];

        return view('formations.index', compact('formations', 'userStats', 'categories', 'levels', 'formats', 'request'));
    }

    /**
     * Détail d'une formation
     */
    public function show(Formation $formation)
    {
        $user = Auth::user();
        
        $formation->load(['creator']);
        
        // Vérifier si l'utilisateur a déjà fait une demande
        $userRequest = FormationRequest::where('formation_id', $formation->id)
                                      ->where('user_id', $user->id)
                                      ->first();

        // Statistiques de la formation
        $stats = [
            'total_requests' => $formation->requests()->count(),
            'approved_requests' => $formation->requests()->approved()->count(),
            'completed_participants' => $formation->participants()->count(),
            'average_rating' => $formation->participants()->whereNotNull('rating')->avg('rating'),
            'available_places' => $formation->getAvailablePlaces(),
        ];

        return view('formations.show', compact('formation', 'userRequest', 'stats'));
    }

    /**
     * Formulaire de création de formation (managers/admin)
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdministrateur()) {
            abort(403);
        }

        $categories = Formation::active()->whereNotNull('category')->distinct()->pluck('category');
        $levels = ['debutant', 'intermediaire', 'avance'];
        $formats = ['presentiel', 'distanciel', 'hybride'];

        return view('formations.create', compact('categories', 'levels', 'formats'));
    }

    /**
     * Enregistrer une nouvelle formation
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdministrateur()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'nullable|string|max:100',
            'level' => 'required|in:debutant,intermediaire,avance',
            'duration_hours' => 'required|integer|min:1|max:1000',
            'cost' => 'nullable|numeric|min:0',
            'provider' => 'nullable|string|max:255',
            'format' => 'required|in:presentiel,distanciel,hybride',
            'max_participants' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date|after:today',
            'end_date' => 'nullable|date|after:start_date',
            'location' => 'nullable|string|max:255',
            'prerequisites' => 'nullable|array',
            'objectives' => 'nullable|array',
        ]);

        $formationData = array_merge($validated, [
            'created_by' => $user->id,
            'is_active' => true,
        ]);

        $formation = Formation::create($formationData);

        return redirect()->route('formations.show', $formation)
                        ->with('success', 'Formation créée avec succès !');
    }

    /**
     * Mes demandes de formation
     */
    public function myRequests()
    {
        $user = Auth::user();
        
        $requests = FormationRequest::where('user_id', $user->id)
                                   ->with(['formation', 'approver'])
                                   ->orderBy('requested_at', 'desc')
                                   ->paginate(10);

        $stats = [
            'total' => $requests->total(),
            'pending' => FormationRequest::where('user_id', $user->id)->pending()->count(),
            'approved' => FormationRequest::where('user_id', $user->id)->approved()->count(),
            'completed' => FormationRequest::where('user_id', $user->id)->completed()->count(),
            'hours_total' => FormationRequest::where('user_id', $user->id)->completed()->sum('hours_completed'),
        ];

        return view('formations.my-requests', compact('requests', 'stats'));
    }

    /**
     * Demander une participation à une formation
     */
    public function requestParticipation(Request $request, Formation $formation)
    {
        $user = Auth::user();

        // Vérifier si la formation est disponible
        if (!$formation->isAvailable()) {
            return back()->withErrors(['error' => 'Cette formation n\'est plus disponible.']);
        }

        // Vérifier si l'utilisateur a déjà fait une demande
        $existingRequest = FormationRequest::where('formation_id', $formation->id)
                                          ->where('user_id', $user->id)
                                          ->first();

        if ($existingRequest) {
            return back()->withErrors(['error' => 'Vous avez déjà fait une demande pour cette formation.']);
        }

        $validated = $request->validate([
            'motivation' => 'required|string|max:1000',
            'priority' => 'required|in:basse,normale,haute',
        ]);

        FormationRequest::create([
            'formation_id' => $formation->id,
            'user_id' => $user->id,
            'motivation' => $validated['motivation'],
            'priority' => $validated['priority'],
            'status' => 'en_attente',
            'requested_at' => now(),
        ]);

        return redirect()->route('formations.show', $formation)
                        ->with('success', 'Votre demande de participation a été envoyée avec succès !');
    }

    /**
     * Gestion des demandes (pour managers/admin)
     */
    public function manageRequests(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdministrateur()) {
            abort(403);
        }

        // Base query selon permissions
        $query = FormationRequest::forUser($user)->with(['formation', 'user', 'approver']);

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->formation_id);
        }

        $requests = $query->orderBy('requested_at', 'desc')->paginate(15);
        $requests->appends($request->all());

        // Options pour les filtres
        $formations = Formation::active()->orderBy('title')->get();
        
        $stats = [
            'total' => FormationRequest::forUser($user)->count(),
            'pending' => FormationRequest::forUser($user)->pending()->count(),
            'approved' => FormationRequest::forUser($user)->approved()->count(),
            'completed' => FormationRequest::forUser($user)->completed()->count(),
        ];

        return view('formations.manage-requests', compact('requests', 'formations', 'stats', 'request'));
    }

    /**
     * Approuver une demande de formation
     */
    public function approveRequest(Request $request, FormationRequest $formationRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdministrateur()) {
            abort(403);
        }

        $validated = $request->validate([
            'manager_comments' => 'nullable|string|max:500',
        ]);

        if ($formationRequest->approve($user)) {
            if ($validated['manager_comments']) {
                $formationRequest->update(['manager_comments' => $validated['manager_comments']]);
            }
            return back()->with('success', 'Demande approuvée avec succès !');
        }

        return back()->withErrors(['error' => 'Impossible d\'approuver cette demande.']);
    }

    /**
     * Rejeter une demande de formation
     */
    public function rejectRequest(Request $request, FormationRequest $formationRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isManager() && !$user->isAdministrateur()) {
            abort(403);
        }

        $validated = $request->validate([
            'manager_comments' => 'required|string|max:500',
        ]);

        if ($formationRequest->reject($user, $validated['manager_comments'])) {
            return back()->with('success', 'Demande rejetée.');
        }

        return back()->withErrors(['error' => 'Impossible de rejeter cette demande.']);
    }

    /**
     * Marquer une formation comme terminée
     */
    public function completeRequest(Request $request, FormationRequest $formationRequest)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // Vérifier permissions
        if (!$user->isManager() && !$user->isAdministrateur() && $formationRequest->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'hours_completed' => 'required|integer|min:1|max:1000',
            'feedback' => 'nullable|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        if ($formationRequest->complete(
            $validated['hours_completed'],
            $validated['feedback'] ?? null,
            $validated['rating'] ?? null
        )) {
            return back()->with('success', 'Formation marquée comme terminée !');
        }

        return back()->withErrors(['error' => 'Impossible de terminer cette formation.']);
    }

    /**
     * Statistiques formations (pour admin)
     */
    public function stats()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdministrateur()) {
            abort(403);
        }

        $stats = [
            'total_formations' => Formation::active()->count(),
            'total_requests' => FormationRequest::count(),
            'this_year_requests' => FormationRequest::thisYear()->count(),
            'completion_rate' => $this->getCompletionRate(),
            'average_rating' => FormationRequest::completed()->whereNotNull('rating')->avg('rating'),
            'total_hours_delivered' => FormationRequest::completed()->sum('hours_completed'),
        ];

        $popularFormations = Formation::getPopularFormations();
        $categoriesStats = Formation::getCategoriesStats();
        
        return view('formations.stats', compact('stats', 'popularFormations', 'categoriesStats'));
    }

    /**
     * Calculer le taux de completion
     */
    private function getCompletionRate(): float
    {
        $total = FormationRequest::whereIn('status', ['approuve', 'termine'])->count();
        $completed = FormationRequest::completed()->count();
        
        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }
}