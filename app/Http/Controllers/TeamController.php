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
            'Ain',
            'Aisne',
            'Allier',
            'Alpes-de-Haute-Provence',
            'Hautes-Alpes',
            'Alpes-Maritimes',
            'Ardèche',
            'Ardennes',
            'Ariège',
            'Aube',
            'Aude',
            'Aveyron',
            'Bouches-du-Rhône',
            'Calvados',
            'Cantal',
            'Charente',
            'Charente-Maritime',
            'Cher',
            'Corrèze',
            'Corse-du-Sud',
            'Haute-Corse',
            'Côte-d\'Or',
            'Côtes-d\'Armor',
            'Creuse',
            'Dordogne',
            'Doubs',
            'Drôme',
            'Eure',
            'Eure-et-Loir',
            'Finistère',
            'Gard',
            'Haute-Garonne',
            'Gers',
            'Gironde',
            'Hérault',
            'Ille-et-Vilaine',
            'Indre',
            'Indre-et-Loire',
            'Isère',
            'Jura',
            'Landes',
            'Loir-et-Cher',
            'Loire',
            'Haute-Loire',
            'Loire-Atlantique',
            'Loiret',
            'Lot',
            'Lot-et-Garonne',
            'Lozère',
            'Maine-et-Loire',
            'Manche',
            'Marne',
            'Haute-Marne',
            'Mayenne',
            'Meurthe-et-Moselle',
            'Meuse',
            'Morbihan',
            'Moselle',
            'Nièvre',
            'Nord',
            'Oise',
            'Orne',
            'Pas-de-Calais',
            'Puy-de-Dôme',
            'Pyrénées-Atlantiques',
            'Hautes-Pyrénées',
            'Pyrénées-Orientales',
            'Bas-Rhin',
            'Haut-Rhin',
            'Rhône',
            'Haute-Saône',
            'Saône-et-Loire',
            'Sarthe',
            'Savoie',
            'Haute-Savoie',
            'Paris',
            'Seine-Maritime',
            'Seine-et-Marne',
            'Yvelines',
            'Deux-Sèvres',
            'Somme',
            'Tarn',
            'Tarn-et-Garonne',
            'Var',
            'Vaucluse',
            'Vendée',
            'Vienne',
            'Haute-Vienne',
            'Vosges',
            'Yonne',
            'Territoire de Belfort',
            'Essonne',
            'Hauts-de-Seine',
            'Seine-Saint-Denis',
            'Val-de-Marne',
            'Val-d\'Oise',
            'Guadeloupe',
            'Martinique',
            'Guyane',
            'La Réunion',
            'Mayotte'
        ];
    }

    /**
     * Vue d'ensemble de l'équipe
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Construire la requête de base selon le rôle
        if ($user->isAdministrateur()) {
            // L'administrateur voit tout
            $baseQuery = User::query();

            $stats = [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
                'managers' => User::where('role', 'manager')->count(),
                'collaborateurs' => User::where('role', 'collaborateur')->count(),
            ];
        } elseif ($user->isManager()) {
            // Le manager voit son équipe directe + tous les utilisateurs des départements gérés
            $baseQuery = User::where(function ($query) use ($user) {
                // Équipe directe (tous ceux dont il est le manager)
                $query->where('manager_id', $user->id);

                // + Utilisateurs des départements gérés
                if ($user->managesAllDepartments()) {
                    // Si il gère tous les départements : tous les autres utilisateurs
                    $query->orWhere('id', '!=', $user->id);
                } elseif ($user->managed_departments && count($user->managed_departments) > 0) {
                    // Si gère des départements spécifiques : utilisateurs de ces départements
                    $query->orWhereIn('localisation', $user->managed_departments);
                }
            });

            // Statistiques pour le manager
            $totalQuery = clone $baseQuery;
            $activeQuery = clone $baseQuery;
            $inactiveQuery = clone $baseQuery;
            $managersQuery = clone $baseQuery;
            $collaborateursQuery = clone $baseQuery;

            $stats = [
                'total' => $totalQuery->count(),
                'active' => $activeQuery->where('is_active', true)->count(),
                'inactive' => $inactiveQuery->where('is_active', false)->count(),
                'managers' => $managersQuery->where('role', 'manager')->count(),
                'collaborateurs' => $collaborateursQuery->where('role', 'collaborateur')->count(),
            ];
        } else {
            // Le collaborateur voit uniquement son équipe (ses collègues avec le même manager)
            $baseQuery = User::where('manager_id', $user->manager_id)
                ->where('id', '!=', $user->id);

            $stats = [
                'total' => User::where('manager_id', $user->manager_id)->count(),
                'active' => User::where('manager_id', $user->manager_id)->where('is_active', true)->count(),
                'inactive' => User::where('manager_id', $user->manager_id)->where('is_active', false)->count(),
                'managers' => 0,
                'collaborateurs' => User::where('manager_id', $user->manager_id)->where('role', 'collaborateur')->count(),
            ];
        }

        // Appliquer les filtres de recherche
        $teamMembers = $baseQuery;

        if ($request->filled('search')) {
            $search = $request->search;
            $teamMembers->where(function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $teamMembers->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $teamMembers->where('is_active', $isActive);
        }

        // Ordre et pagination
        $teamMembers = $teamMembers->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Liste des managers pour les formulaires
        $managers = User::whereIn('role', ['manager', 'administrateur'])
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        // Liste des départements français
        $departementsFrancais = $this->getDepartementsFrancais();

        return view('team.index', compact('teamMembers', 'stats', 'managers', 'departementsFrancais'));
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
            'managed_departments' => 'nullable|array',
            'managed_departments.*' => 'string',
            // Informations professionnelles
            'rsac_number' => 'nullable|string|max:255',
            'professional_address' => 'nullable|string|max:500',
            'professional_city' => 'nullable|string|max:255',
            'professional_postal_code' => 'nullable|string|max:10',
            'professional_email' => 'nullable|email|max:255',
            'professional_phone' => 'nullable|string|max:255',
            'legal_mentions' => 'nullable|string|max:2000',
            'footer_text' => 'nullable|string|max:1000',
        ]);

        // Gestion des départements gérés (uniquement pour managers et admins)
        if (in_array($validated['role'], ['manager', 'administrateur'])) {
            if ($request->has('managed_departments')) {
                $validated['managed_departments'] = $request->managed_departments;
            }
        } else {
            // Les collaborateurs ne gèrent pas de départements
            $validated['managed_departments'] = null;
        }

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
            'commandes_en_attente' => $teamMember->getPendingOrdersCount(),
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
            'managed_departments' => 'nullable|array',
            'managed_departments.*' => 'string',
            // Informations professionnelles
            'rsac_number' => 'nullable|string|max:255',
            'professional_address' => 'nullable|string|max:500',
            'professional_city' => 'nullable|string|max:255',
            'professional_postal_code' => 'nullable|string|max:10',
            'professional_email' => 'nullable|email|max:255',
            'professional_phone' => 'nullable|string|max:255',
            'legal_mentions' => 'nullable|string|max:2000',
            'footer_text' => 'nullable|string|max:1000',
        ]);

        // Gestion des départements gérés (uniquement pour managers et admins)
        if (in_array($validated['role'], ['manager', 'administrateur'])) {
            if ($request->has('managed_departments')) {
                $validated['managed_departments'] = $request->managed_departments;
            }
        } else {
            // Les collaborateurs ne gèrent pas de départements
            $validated['managed_departments'] = null;
        }

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

        // Vérifier s'il a des missions en cours
        $hasPendingMissions = $teamMember->assignedMissions()->inProgress()->exists();

        if ($hasPendingMissions) {
            return back()->withErrors(['error' => 'Impossible de supprimer cet utilisateur car il a des missions en cours.']);
        }

        $name = $teamMember->full_name;
        $teamMember->delete();

        return redirect()->route('team.index')
            ->with('success', "Utilisateur {$name} supprimé définitivement.");
    }

    /**
     * Vérifier si l'utilisateur peut voir ce membre d'équipe
     */
    private function canUserView($user, $teamMember): bool
    {
        // Les admins voient tout
        if ($user->isAdministrateur()) {
            return true;
        }

        // Les managers voient :
        // 1. Les membres de leur équipe directe
        // 2. Les collaborateurs des départements qu'ils gèrent
        if ($user->isManager()) {
            // Équipe directe
            if ($teamMember->manager_id === $user->id) {
                return true;
            }

            // Département géré
            if ($teamMember->role === 'collaborateur' && $teamMember->department) {
                return $user->canManageDepartment($teamMember->department);
            }
        }

        // Les collaborateurs ne peuvent pas voir les profils
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut modifier ce membre d'équipe
     */
    private function canUserEdit($user): bool
    {
        return $user->isAdministrateur();
    }
}
