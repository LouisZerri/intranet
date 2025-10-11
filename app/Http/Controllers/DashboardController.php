<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mission;
use App\Models\CommunicationOrder;
use App\Models\News;
use App\Models\Formation;
use App\Models\FormationRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Données communes à tous les rôles
        $dashboardData = [
            'user' => $user,
            'currentMonth' => now()->format('F Y'),
            'todayDate' => now()->format('d/m/Y'),
        ];

        // Chargement des actualités personnalisées
        $dashboardData['news'] = $this->getPersonalizedNews($user);

        // Données spécifiques selon le rôle
        switch ($user->role) {
            case 'collaborateur':
                $dashboardData = array_merge($dashboardData, $this->getCollaborateurData($user));
                break;

            case 'manager':
                $dashboardData = array_merge($dashboardData, $this->getManagerData($user));
                break;

            case 'administrateur':
                $dashboardData = array_merge($dashboardData, $this->getAdministrateurData($user));
                break;
        }

        return view('dashboard.index', $dashboardData);
    }

    /**
     * Données KPI pour un collaborateur
     */
    private function getCollaborateurData(User $user): array
    {
        return [
            'role_label' => 'Collaborateur',
            'kpis' => [
                'missions_en_cours' => $user->assignedMissions()->inProgress()->count(),
                'missions_terminees_mois' => $user->getCompletedMissionsThisMonth(),
                'chiffre_affaires' => $user->getCurrentMonthRevenue(),
                'missions_en_retard' => $user->getOverdueMissions(),
                // Communication (remplace demandes internes)
                'commandes_ce_mois' => $user->getOrdersThisMonth(),
                'montant_commandes_mois' => $user->getOrdersAmountThisMonth(),
                // Formations
                'heures_formation_annee' => $user->getFormationHoursThisYear(),
                'formations_terminees' => $user->getCompletedFormationsCount(),
                'demandes_formation_attente' => $user->getPendingFormationRequests(),
            ],
            'recent_missions' => $user->assignedMissions()
                ->with(['creator', 'manager'])
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get(),
            'upcoming_deadlines' => $user->assignedMissions()
                ->inProgress()
                ->where('due_date', '>=', now())
                ->where('due_date', '<=', now()->addDays(7))
                ->orderBy('due_date')
                ->take(5)
                ->get(),
            // Formations récentes
            'recent_formations' => $user->formationRequests()
                ->with(['formation'])
                ->orderBy('requested_at', 'desc')
                ->take(3)
                ->get(),
            // Commandes récentes (NOUVEAU)
            'recent_orders' => $user->communicationOrders()
                ->with(['items.product'])
                ->orderBy('ordered_at', 'desc')
                ->take(3)
                ->get(),
        ];
    }

    /**
     * Données KPI pour un manager
     */
    private function getManagerData(User $user): array
    {
        $teamMembers = $user->subordinates()->where('is_active', true)->get();
        $teamIds = $teamMembers->pluck('id')->toArray();

        return [
            'role_label' => 'Manager',
            'kpis' => [
                // KPI personnels
                'missions_en_cours' => $user->assignedMissions()->inProgress()->count(),
                'missions_terminees_mois' => $user->getCompletedMissionsThisMonth(),
                'chiffre_affaires_perso' => $user->getCurrentMonthRevenue(),
                'missions_en_retard' => $user->getOverdueMissions(),
                'heures_formation_annee' => $user->getFormationHoursThisYear(),

                // KPI équipe
                'chiffre_affaires_equipe' => Mission::getTeamRevenue($user, now()->startOfMonth(), now()->endOfMonth()),
                'equipe_size' => $teamMembers->count(),
                'missions_equipe_en_retard' => Mission::whereIn('assigned_to', $teamIds)
                    ->overdue()
                    ->count(),
                // Communication équipe (remplace demandes)
                'commandes_equipe_mois' => CommunicationOrder::whereIn('user_id', $teamIds)
                    ->whereMonth('ordered_at', now()->month)
                    ->whereYear('ordered_at', now()->year)
                    ->count(),
                // Formations équipe
                'taux_collaborateurs_formes' => $this->getTeamTrainingRate($teamMembers),
                'formations_equipe_en_attente' => FormationRequest::whereIn('user_id', $teamIds)
                    ->pending()
                    ->count(),
                'heures_formation_equipe' => $this->getTeamTrainingHours($teamMembers),
            ],
            'team_members' => $teamMembers,
            'team_performance' => $this->getTeamPerformance($teamMembers),
            // Demandes de formation à approuver
            'pending_formation_requests' => FormationRequest::whereIn('user_id', $teamIds)
                ->pending()
                ->with(['user', 'formation'])
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Données KPI pour un administrateur
     */
    private function getAdministrateurData(User $user): array
    {
        $thisMonth = now()->startOfMonth();

        return [
            'role_label' => 'Administrateur',
            'kpis' => [
                'utilisateurs_actifs' => User::where('is_active', true)->count(),
                'missions_ouvertes_mois' => Mission::thisMonth()->count(),
                'ca_total_mois' => Mission::completedThisMonth()->sum('revenue') ?: 0,
                // Communication (remplace taux validation demandes)
                'commandes_ce_mois' => CommunicationOrder::whereMonth('ordered_at', now()->month)
                    ->whereYear('ordered_at', now()->year)
                    ->count(),
                'ca_commandes_mois' => CommunicationOrder::whereMonth('ordered_at', now()->month)
                    ->whereYear('ordered_at', now()->year)
                    ->sum('total_amount') ?: 0,
                // Formations
                'formations_actives' => Formation::active()->count(),
                'demandes_formation_mois' => FormationRequest::whereMonth('requested_at', now()->month)
                    ->whereYear('requested_at', now()->year)
                    ->count(),
                'heures_formation_annee' => FormationRequest::completed()
                    ->thisYear()
                    ->sum('hours_completed'),
                'taux_completion_formations' => $this->getGlobalFormationCompletionRate(),
            ],
            'recent_activities' => $this->getRecentActivities(),
            'localisation_stats' => $this->getLocalisationStats(),
            // Commandes récentes (remplace pending_requests)
            'recent_orders' => CommunicationOrder::with(['user', 'items.product'])
                ->orderBy('ordered_at', 'desc')
                ->take(10)
                ->get(),
            // Formations populaires
            'popular_formations' => Formation::getPopularFormations(5),
            'formation_categories_stats' => Formation::getCategoriesStats(),
        ];
    }

    /**
     * Actualités personnalisées
     */
    private function getPersonalizedNews(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return News::published()
            ->forUser($user)
            ->byPriority()
            ->with('author')
            ->take(5)
            ->get();
    }

    /**
     * Performance de l'équipe
     */
    private function getTeamPerformance(\Illuminate\Database\Eloquent\Collection $teamMembers): array
    {
        $performance = [];

        foreach ($teamMembers as $member) {
            $performance[] = [
                'user' => $member,
                'missions_terminees' => $member->getCompletedMissionsThisMonth(),
                'ca_mois' => $member->getCurrentMonthRevenue(),
                'missions_en_retard' => $member->getOverdueMissions(),
                'completion_rate' => Mission::getCompletionRate($member, now()->startOfMonth(), now()->endOfMonth()),
                // Formations
                'heures_formation_annee' => $member->getFormationHoursThisYear(),
                'formations_terminees' => $member->getCompletedFormationsCount(),
                'est_forme_cette_annee' => $member->isTrainedThisYear(),
                // Communication (NOUVEAU)
                'commandes_mois' => $member->getOrdersThisMonth(),
            ];
        }

        return $performance;
    }

    /**
     * Activités récentes pour l'administrateur
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Missions créées
        $recentMissions = Mission::with(['assignedUser', 'creator'])
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentMissions as $mission) {
            $activities[] = [
                'type' => 'Mission créée',
                'date' => $mission->created_at,
                'description' => "Nouvelle mission '{$mission->title}' assignée à {$mission->assignedUser->full_name}",
                'icon' => '📁',
                'color' => 'blue'
            ];
        }

        // Commandes de communication (REMPLACE demandes internes)
        $recentOrders = CommunicationOrder::with('user')
            ->where('ordered_at', '>=', now()->subDays(7))
            ->orderBy('ordered_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentOrders as $order) {
            $activities[] = [
                'type' => 'Commande passée',
                'date' => $order->ordered_at,
                'description' => "Commande {$order->order_number} de {$order->user->full_name} ({$order->items->count()} article(s))",
                'icon' => '📦',
                'color' => 'green'
            ];
        }

        // Formations créées
        $recentFormations = Formation::with('creator')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentFormations as $formation) {
            $activities[] = [
                'type' => 'Formation créée',
                'date' => $formation->created_at,
                'description' => "Nouvelle formation '{$formation->title}' ajoutée au catalogue",
                'icon' => '📚',
                'color' => 'purple'
            ];
        }

        // Demandes de formations
        $recentFormationRequests = FormationRequest::with(['user', 'formation'])
            ->where('requested_at', '>=', now()->subDays(7))
            ->orderBy('requested_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentFormationRequests as $request) {
            $activities[] = [
                'type' => 'Demande formation',
                'date' => $request->requested_at,
                'description' => "{$request->user->full_name} a demandé la formation '{$request->formation->title}'",
                'icon' => '🎓',
                'color' => 'indigo'
            ];
        }

        // Trier par date décroissante
        usort($activities, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Statistiques par localisation
     */
    private function getLocalisationStats(): array
    {
        $localisations = User::where('is_active', true)
            ->whereNotNull('localisation')
            ->groupBy('localisation')
            ->selectRaw('localisation, count(*) as user_count')
            ->get();

        $stats = [];

        foreach ($localisations as $loc) {
            $locUsers = User::where('localisation', $loc->localisation)->pluck('id');

            $stats[] = [
                'name' => $loc->localisation,
                'users' => $loc->user_count,
                'missions_mois' => Mission::whereIn('assigned_to', $locUsers)
                    ->thisMonth()
                    ->count(),
                'ca_mois' => Mission::whereIn('assigned_to', $locUsers)
                    ->completedThisMonth()
                    ->sum('revenue') ?: 0,
                // Formations
                'heures_formation_annee' => FormationRequest::whereIn('user_id', $locUsers)
                    ->completed()
                    ->thisYear()
                    ->sum('hours_completed'),
                'taux_formes_annee' => $this->getLocalisationTrainingRate($locUsers),
                // Communication (NOUVEAU)
                'commandes_mois' => CommunicationOrder::whereIn('user_id', $locUsers)
                    ->whereMonth('ordered_at', now()->month)
                    ->whereYear('ordered_at', now()->year)
                    ->count(),
            ];
        }

        return $stats;
    }

    /**
     * Calculer le taux de formation d'une équipe
     */
    private function getTeamTrainingRate(\Illuminate\Database\Eloquent\Collection $teamMembers): float
    {
        if ($teamMembers->isEmpty()) {
            return 0;
        }

        $trainedCount = $teamMembers->filter(function ($member) {
            return $member->isTrainedThisYear();
        })->count();

        return round(($trainedCount / $teamMembers->count()) * 100, 1);
    }

    /**
     * Calculer les heures de formation d'une équipe
     */
    private function getTeamTrainingHours(\Illuminate\Database\Eloquent\Collection $teamMembers): int
    {
        return $teamMembers->sum(function ($member) {
            return $member->getFormationHoursThisYear();
        });
    }

    /**
     * Taux de completion global des formations
     */
    private function getGlobalFormationCompletionRate(): float
    {
        $totalRequests = FormationRequest::thisYear()->whereIn('status', ['approuve', 'termine'])->count();
        $completedRequests = FormationRequest::thisYear()->completed()->count();

        if ($totalRequests == 0) {
            return 0;
        }

        return round(($completedRequests / $totalRequests) * 100, 1);
    }

    /**
     * Taux de formation par localisation
     */
    private function getLocalisationTrainingRate($userIds): float
    {
        if ($userIds->isEmpty()) {
            return 0;
        }

        $totalUsers = $userIds->count();
        $trainedUsers = User::whereIn('id', $userIds)
            ->whereHas('formationRequests', function ($q) {
                $q->completed()->thisYear();
            })
            ->count();

        return $totalUsers > 0 ? round(($trainedUsers / $totalUsers) * 100, 1) : 0;
    }

    /**
     * Vue rapide des échéances urgentes
     */
    public function getUpcomingDeadlines()
    {
        $user = Auth::user();

        $missions = Mission::forUser($user)
            ->inProgress()
            ->whereNotNull('due_date')
            ->where('due_date', '>=', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->with(['assignedUser', 'creator'])
            ->orderBy('due_date')
            ->get();

        $deadlinesData = $missions->map(function ($mission) {
            return [
                'id' => $mission->id,
                'title' => $mission->title,
                'assigned_user' => $mission->assignedUser->full_name,
                'due_date' => $mission->due_date->format('d/m/Y'),
                'days_until_due' => $mission->getDaysUntilDue(),
                'due_status' => $mission->due_status,
                'due_color' => $mission->due_color,
                'priority' => $mission->priority_label,
                'priority_color' => $mission->priority_color,
            ];
        });

        return response()->json($deadlinesData);
    }

    /**
     * API pour récupérer les stats formations
     */
    public function getFormationStats()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        return response()->json([
            'formations_actives' => Formation::active()->count(),
            'demandes_ce_mois' => FormationRequest::whereMonth('requested_at', now()->month)->count(),
            'heures_delivrees_annee' => FormationRequest::completed()->thisYear()->sum('hours_completed'),
            'taux_completion' => $this->getGlobalFormationCompletionRate(),
            'formations_populaires' => Formation::getPopularFormations(10),
            'demandes_par_mois' => FormationRequest::selectRaw('MONTH(requested_at) as month, count(*) as count')
                ->whereYear('requested_at', now()->year)
                ->groupBy('month')
                ->get(),
        ]);
    }

    /**
     * API pour récupérer les stats communication (NOUVEAU)
     */
    public function getCommunicationStats()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->isAdministrateur()) {
            abort(403);
        }

        return response()->json([
            'commandes_ce_mois' => CommunicationOrder::whereMonth('ordered_at', now()->month)->count(),
            'ca_ce_mois' => CommunicationOrder::whereMonth('ordered_at', now()->month)->sum('total_amount'),
            'commandes_par_statut' => CommunicationOrder::selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->get(),
            'commandes_par_mois' => CommunicationOrder::selectRaw('MONTH(ordered_at) as month, count(*) as count, sum(total_amount) as total')
                ->whereYear('ordered_at', now()->year)
                ->groupBy('month')
                ->get(),
        ]);
    }
}