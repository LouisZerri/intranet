<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    /**
     * Liste des départements français pour le formulaire
     */
    private function getDepartementsFrancais(): array
    {
        return [
            'Ain', 'Aisne', 'Allier', 'Alpes-de-Haute-Provence', 'Hautes-Alpes', 'Alpes-Maritimes',
            'Ardèche', 'Ardennes', 'Ariège', 'Aube', 'Aude', 'Aveyron', 'Bouches-du-Rhône',
            'Calvados', 'Cantal', 'Charente', 'Charente-Maritime', 'Cher', 'Corrèze',
            'Corse-du-Sud', 'Haute-Corse', 'Côte-d\'Or', 'Côtes-d\'Armor', 'Creuse', 'Dordogne',
            'Doubs', 'Drôme', 'Eure', 'Eure-et-Loir', 'Finistère', 'Gard', 'Haute-Garonne',
            'Gers', 'Gironde', 'Hérault', 'Ille-et-Vilaine', 'Indre', 'Indre-et-Loire', 'Isère',
            'Jura', 'Landes', 'Loir-et-Cher', 'Loire', 'Haute-Loire', 'Loire-Atlantique',
            'Loiret', 'Lot', 'Lot-et-Garonne', 'Lozère', 'Maine-et-Loire', 'Manche', 'Marne',
            'Haute-Marne', 'Mayenne', 'Meurthe-et-Moselle', 'Meuse', 'Morbihan', 'Moselle',
            'Nièvre', 'Nord', 'Oise', 'Orne', 'Pas-de-Calais', 'Puy-de-Dôme',
            'Pyrénées-Atlantiques', 'Hautes-Pyrénées', 'Pyrénées-Orientales', 'Bas-Rhin',
            'Haut-Rhin', 'Rhône', 'Haute-Saône', 'Saône-et-Loire', 'Sarthe', 'Savoie',
            'Haute-Savoie', 'Paris', 'Seine-Maritime', 'Seine-et-Marne', 'Yvelines',
            'Deux-Sèvres', 'Somme', 'Tarn', 'Tarn-et-Garonne', 'Var', 'Vaucluse', 'Vendée',
            'Vienne', 'Haute-Vienne', 'Vosges', 'Yonne', 'Territoire de Belfort', 'Essonne',
            'Hauts-de-Seine', 'Seine-Saint-Denis', 'Val-de-Marne', 'Val-d\'Oise',
            'Guadeloupe', 'Martinique', 'Guyane', 'La Réunion', 'Mayotte'
        ];
    }

    /**
     * Vue d'ensemble de l'équipe
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Base query selon permissions
        if ($user->isAdministrateur()) {
            // Les admins voient tous les utilisateurs
            $query = User::with('manager');
        } elseif ($user->isManager()) {
            // Les managers ne voient QUE leur équipe directe (subordinates)
            $query = User::where('manager_id', $user->id)->with('manager');
        } else {
            abort(403);
        }

        // Filtres
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('localisation')) {
            $query->where('localisation', $request->localisation);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } else {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            if (!empty($searchTerm)) {
                $query->where(function($q) use ($searchTerm) {
                    $q->where('first_name', 'like', "%{$searchTerm}%")
                      ->orWhere('last_name', 'like', "%{$searchTerm}%")
                      ->orWhere('email', 'like', "%{$searchTerm}%");
                });
            }
        }

        $users = $query->orderBy('first_name')->paginate(15);
        $users->appends($request->all());

        // Statistiques ajustées selon les permissions
        if ($user->isAdministrateur()) {
            $stats = [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'managers' => User::where('role', 'manager')->where('is_active', true)->count(),
                'collaborateurs' => User::where('role', 'collaborateur')->where('is_active', true)->count(),
            ];
        } else {
            // Pour les managers, stats basées uniquement sur leur équipe
            $teamQuery = User::where('manager_id', $user->id);
            $stats = [
                'total_users' => $teamQuery->count(),
                'active_users' => $teamQuery->where('is_active', true)->count(),
                'managers' => 0, // Un manager ne gère pas d'autres managers
                'collaborateurs' => $teamQuery->where('role', 'collaborateur')->where('is_active', true)->count(),
            ];
        }

        // Options pour les filtres - ajustées selon permissions
        if ($user->isAdministrateur()) {
            $departments = User::whereNotNull('department')->distinct()->pluck('department');
            $localisations = User::whereNotNull('localisation')->distinct()->orderBy('localisation')->pluck('localisation');
            $managers = User::where('role', 'manager')->where('is_active', true)->get();
        } else {
            // Pour les managers, uniquement les départements de leur équipe
            $departments = User::where('manager_id', $user->id)
                             ->whereNotNull('department')
                             ->distinct()
                             ->pluck('department');
            $localisations = User::where('manager_id', $user->id)
                               ->whereNotNull('localisation')
                               ->distinct()
                               ->orderBy('localisation')
                               ->pluck('localisation');
            $managers = collect([$user]); // Le manager ne voit que lui-même comme manager
        }

        return view('team.index', compact('users', 'stats', 'departments', 'localisations', 'managers', 'request'));
    }

    /**
     * Formulaire de création d'utilisateur
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdministrateur()) {
            abort(403);
        }

        $managers = User::where('role', 'manager')->where('is_active', true)->get();
        $departments = User::whereNotNull('department')->distinct()->pluck('department');
        $departementsFrancais = $this->getDepartementsFrancais();

        return view('team.create', compact('managers', 'departments', 'departementsFrancais'));
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user->isAdministrateur()) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:collaborateur,manager,administrateur',
            'phone' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'localisation' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'revenue_target' => 'nullable|numeric|min:0',
        ]);

        $userData = array_merge($validated, [
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $newUser = User::create($userData);

        return redirect()->route('team.show', $newUser)
                        ->with('success', 'Utilisateur créé avec succès !');
    }

    /**
     * Afficher un utilisateur
     */
    public function show(User $teamMember)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Vérifier permissions
        if (!$this->canUserView($user, $teamMember)) {
            abort(403, 'Vous n\'avez pas accès à ce profil utilisateur.');
        }

        $teamMember->load(['manager', 'subordinates']);

        // Statistiques utilisateur
        $userStats = [
            'missions_en_cours' => $teamMember->assignedMissions()->inProgress()->count(),
            'missions_terminees_mois' => $teamMember->getCompletedMissionsThisMonth(),
            'ca_mois' => $teamMember->getCurrentMonthRevenue(),
            'missions_en_retard' => $teamMember->getOverdueMissions(),
            'heures_formation_annee' => $teamMember->getFormationHoursThisYear(),
            'demandes_en_attente' => $teamMember->getPendingInternalRequests(),
        ];

        // Missions récentes
        $recentMissions = $teamMember->assignedMissions()
                                   ->with(['creator'])
                                   ->orderBy('updated_at', 'desc')
                                   ->take(5)
                                   ->get();

        return view('team.show', compact('teamMember', 'userStats', 'recentMissions'));
    }

    /**
     * Formulaire d'édition d'utilisateur
     */
    public function edit(User $teamMember)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEdit($user, $teamMember)) {
            abort(403);
        }

        $managers = User::where('role', 'manager')
                       ->where('is_active', true)
                       ->where('id', '!=', $teamMember->id)
                       ->get();
                       
        $departments = User::whereNotNull('department')->distinct()->pluck('department');
        $departementsFrancais = $this->getDepartementsFrancais();

        return view('team.edit', compact('teamMember', 'managers', 'departments', 'departementsFrancais'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, User $teamMember)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$this->canUserEdit($user, $teamMember)) {
            abort(403);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($teamMember->id)],
            'role' => 'required|in:collaborateur,manager,administrateur',
            'phone' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'localisation' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'revenue_target' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        // Mettre à jour le champ name
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $teamMember->update($validated);

        return redirect()->route('team.show', $teamMember)
                        ->with('success', 'Utilisateur mis à jour avec succès !');
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     */
    public function resetPassword(Request $request, User $teamMember)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $teamMember->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Mot de passe réinitialisé avec succès !');
    }

    /**
     * Désactiver un utilisateur
     */
    public function deactivate(User $teamMember)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        if ($teamMember->id === $user->id) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $teamMember->update(['is_active' => false]);

        return back()->with('success', 'Utilisateur désactivé avec succès !');
    }

    /**
     * Réactiver un utilisateur
     */
    public function activate(User $teamMember)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        $teamMember->update(['is_active' => true]);

        return back()->with('success', 'Utilisateur réactivé avec succès !');
    }

    /**
     * Supprimer définitivement un utilisateur
     */
    public function destroy(User $teamMember)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        if ($teamMember->id === $user->id) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        // Vérifier s'il a des missions ou demandes en cours
        $hasPendingItems = $teamMember->assignedMissions()->inProgress()->exists() ||
                          $teamMember->internalRequests()->pending()->exists();

        if ($hasPendingItems) {
            return back()->withErrors(['error' => 'Impossible de supprimer cet utilisateur car il a des missions ou demandes en cours.']);
        }

        $name = $teamMember->full_name;
        $teamMember->delete();

        return redirect()->route('team.index')
                        ->with('success', "Utilisateur {$name} supprimé définitivement.");
    }

    /**
     * Vérifier si l'utilisateur peut voir ce membre d'équipe
     * LOGIQUE MISE À JOUR : Accès exclusif des managers à leur équipe
     */
    private function canUserView($user, $teamMember): bool
    {
        // Les admins voient tout
        if ($user->isAdministrateur()) {
            return true;
        }

        // Les managers ne voient QUE les membres de leur équipe directe
        if ($user->isManager()) {
            return $teamMember->manager_id === $user->id;
        }

        // Les collaborateurs ne peuvent pas voir les profils (sauf via autres fonctionnalités)
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut modifier ce membre d'équipe
     */
    private function canUserEdit($user, $teamMember): bool
    {
        return $user->isAdministrateur();
    }
}