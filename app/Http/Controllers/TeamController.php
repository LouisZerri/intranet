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
     * Vue d'ensemble de l'équipe
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Base query selon permissions
        if ($user->isAdministrateur()) {
            $query = User::with('manager');
        } elseif ($user->isManager()) {
            $query = User::where(function($q) use ($user) {
                $q->where('manager_id', $user->id)
                  ->orWhere('id', $user->id);
            })->with('manager');
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

        // Statistiques
        $stats = [
            'total_users' => $query->count(),
            'active_users' => User::where('is_active', true)->count(),
            'managers' => User::where('role', 'manager')->where('is_active', true)->count(),
            'collaborateurs' => User::where('role', 'collaborateur')->where('is_active', true)->count(),
        ];

        // Options pour les filtres
        $departments = User::whereNotNull('department')->distinct()->pluck('department');
        $managers = User::where('role', 'manager')->where('is_active', true)->get();

        return view('team.index', compact('users', 'stats', 'departments', 'managers', 'request'));
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

        return view('team.create', compact('managers', 'departments'));
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
            'position' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'revenue_target' => 'nullable|numeric|min:0',
        ]);

        $userData = array_merge($validated, [
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
            abort(403);
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

        return view('team.edit', compact('teamMember', 'managers', 'departments'));
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
            'position' => 'nullable|string|max:255',
            'manager_id' => 'nullable|exists:users,id',
            'revenue_target' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

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
     */
    private function canUserView($user, $teamMember): bool
    {
        if ($user->isAdministrateur()) {
            return true;
        }

        if ($user->isManager()) {
            return $teamMember->manager_id === $user->id || $teamMember->id === $user->id;
        }

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