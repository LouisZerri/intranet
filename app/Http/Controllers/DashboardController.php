<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mission;
use App\Models\CommunicationOrder;
use App\Models\News;
use App\Models\Formation;
use App\Models\FormationRequest;
use App\Models\Quote;
use App\Models\Invoice;
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
        // KPI Commerciaux
        $devisCeMois = Quote::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $caFactureMois = Invoice::where('user_id', $user->id)
            ->where('status', '!=', 'brouillon')
            ->whereMonth('issued_at', now()->month)
            ->whereYear('issued_at', now()->year)
            ->sum('total_ht');

        $caPayeMois = Invoice::where('user_id', $user->id)
            ->where('status', 'payee')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_ht');

        $caEnAttente = Invoice::where('user_id', $user->id)
            ->where('status', 'emise')
            ->sum('total_ht');

        return [
            'role_label' => 'Collaborateur',
            'kpis' => [
                'missions_en_cours' => $user->assignedMissions()->inProgress()->count(),
                'missions_terminees_mois' => $user->getCompletedMissionsThisMonth(),
                'chiffre_affaires' => $user->getCurrentMonthRevenue(),
                'missions_en_retard' => $user->getOverdueMissions(),
                
                // KPI Commerciaux
                'devis_ce_mois' => $devisCeMois,
                'ca_facture_mois' => $caFactureMois,
                'ca_paye_mois' => $caPayeMois,
                'ca_en_attente' => $caEnAttente,
                
                // Communication
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
            
            // Données commerciales
            'recent_quotes' => Quote::where('user_id', $user->id)
                ->with('client')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'recent_invoices' => Invoice::where('user_id', $user->id)
                ->with(['client', 'payments'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            
            // Formations récentes
            'recent_formations' => $user->formationRequests()
                ->with(['formation'])
                ->orderBy('requested_at', 'desc')
                ->take(3)
                ->get(),
            
            // Commandes récentes
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

        // KPI commerciaux équipe
        $devisEquipeMois = Quote::whereIn('user_id', $teamIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $caFactureEquipe = Invoice::whereIn('user_id', $teamIds)
            ->where('status', '!=', 'brouillon')
            ->whereMonth('issued_at', now()->month)
            ->whereYear('issued_at', now()->year)
            ->sum('total_ht');

        $caPayeEquipe = Invoice::whereIn('user_id', $teamIds)
            ->where('status', 'payee')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_ht');

        $tauxTransformation = $this->calculateConversionRate($teamIds);

        return [
            'role_label' => 'Manager',
            'kpis' => [
                // KPI personnels
                'missions_en_cours' => $user->assignedMissions()->inProgress()->count(),
                'missions_terminees_mois' => $user->getCompletedMissionsThisMonth(),
                'chiffre_affaires_perso' => $user->getCurrentMonthRevenue(),
                'missions_en_retard' => $user->getOverdueMissions(),
                'heures_formation_annee' => $user->getFormationHoursThisYear(),

                // KPI équipe missions
                'chiffre_affaires_equipe' => Mission::getTeamRevenue($user, now()->startOfMonth(), now()->endOfMonth()),
                'equipe_size' => $teamMembers->count(),
                'missions_equipe_en_retard' => Mission::whereIn('assigned_to', $teamIds)->overdue()->count(),
                
                // KPI commerciaux équipe
                'devis_equipe_mois' => $devisEquipeMois,
                'ca_facture_equipe' => $caFactureEquipe,
                'ca_paye_equipe' => $caPayeEquipe,
                'ca_commercial_equipe' => $caPayeEquipe,
                'taux_transformation' => $tauxTransformation,
                
                // Communication équipe
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
            
            // Devis récents équipe
            'recent_team_quotes' => Quote::whereIn('user_id', $teamIds)
                ->with(['user', 'client'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
            
            // Factures en attente équipe
            'pending_team_invoices' => Invoice::whereIn('user_id', $teamIds)
                ->where('status', 'emise')
                ->with(['user', 'client'])
                ->orderBy('due_date')
                ->take(10)
                ->get(),
        ];
    }

    /**
     * Données KPI pour un administrateur
     */
    private function getAdministrateurData(User $user): array
    {
        // KPI commerciaux globaux
        $devisCeMois = Quote::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $caFactureMois = Invoice::where('status', '!=', 'brouillon')
            ->whereMonth('issued_at', now()->month)
            ->whereYear('issued_at', now()->year)
            ->sum('total_ht');

        $caPayeMois = Invoice::where('status', 'payee')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('total_ht');

        $tauxTransformationGlobal = $this->calculateGlobalConversionRate();

        return [
            'role_label' => 'Administrateur',
            'kpis' => [
                'utilisateurs_actifs' => User::where('is_active', true)->count(),
                'missions_ouvertes_mois' => Mission::thisMonth()->count(),
                'ca_total_mois' => Mission::completedThisMonth()->sum('revenue') ?: 0,
                
                // KPI commerciaux
                'devis_ce_mois' => $devisCeMois,
                'ca_facture_mois' => $caFactureMois,
                'ca_paye_mois' => $caPayeMois,
                'taux_transformation_global' => $tauxTransformationGlobal,
                'factures_en_attente' => Invoice::where('status', 'emise')->count(),
                'ca_en_attente' => Invoice::where('status', 'emise')->sum('total_ht'),
                
                // Communication
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
            
            // Commandes récentes
            'recent_orders' => CommunicationOrder::with(['user', 'items.product'])
                ->orderBy('ordered_at', 'desc')
                ->take(10)
                ->get(),
            
            // Formations populaires
            'popular_formations' => Formation::getPopularFormations(5),
            'formation_categories_stats' => Formation::getCategoriesStats(),
            
            // Pipeline commercial
            'pipeline_stats' => $this->getPipelineStats(),
            'top_performers' => $this->getTopPerformers(),
            'recent_quotes' => Quote::with(['user', 'client'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
            'overdue_invoices' => Invoice::where('status', 'en_retard')
                ->with(['user', 'client'])
                ->orderBy('due_date')
                ->take(10)
                ->get(),
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
            // KPI commerciaux membre
            $devisMembre = Quote::where('user_id', $member->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            $caFactureMembre = Invoice::where('user_id', $member->id)
                ->where('status', '!=', 'brouillon')
                ->whereMonth('issued_at', now()->month)
                ->whereYear('issued_at', now()->year)
                ->sum('total_ht');

            $performance[] = [
                'user' => $member,
                'missions_terminees' => $member->getCompletedMissionsThisMonth(),
                'ca_mois' => $member->getCurrentMonthRevenue(),
                'missions_en_retard' => $member->getOverdueMissions(),
                'completion_rate' => Mission::getCompletionRate($member, now()->startOfMonth(), now()->endOfMonth()),
                
                // KPI commerciaux
                'devis_mois' => $devisMembre,
                'ca_facture_mois' => $caFactureMembre,
                
                // Formations
                'heures_formation_annee' => $member->getFormationHoursThisYear(),
                'formations_terminees' => $member->getCompletedFormationsCount(),
                'est_forme_cette_annee' => $member->isTrainedThisYear(),
                
                // Communication
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

        // Devis créés
        $recentQuotes = Quote::with(['user', 'client'])
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentQuotes as $quote) {
            $activities[] = [
                'type' => 'Devis créé',
                'date' => $quote->created_at,
                'description' => "{$quote->user->full_name} a créé le devis {$quote->quote_number} pour {$quote->client->display_name}",
                'icon' => '📄',
                'color' => 'indigo'
            ];
        }

        // Factures payées
        $recentPaidInvoices = Invoice::with(['user', 'client'])
            ->where('status', 'payee')
            ->where('paid_at', '>=', now()->subDays(7))
            ->orderBy('paid_at', 'desc')
            ->take(3)
            ->get();

        foreach ($recentPaidInvoices as $invoice) {
            $activities[] = [
                'type' => 'Facture payée',
                'date' => $invoice->paid_at,
                'description' => "Facture {$invoice->invoice_number} payée - {$invoice->client->display_name} ({$invoice->formatted_total_ttc})",
                'icon' => '💰',
                'color' => 'green'
            ];
        }

        // Commandes de communication
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
                'color' => 'cyan'
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

        // Trier par date décroissante
        usort($activities, function ($a, $b) {
            return $b['date'] <=> $a['date'];
        });

        return array_slice($activities, 0, 15);
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

            // KPI commerciaux par localisation
            $caFactureLoc = Invoice::whereIn('user_id', $locUsers)
                ->where('status', '!=', 'brouillon')
                ->whereMonth('issued_at', now()->month)
                ->whereYear('issued_at', now()->year)
                ->sum('total_ht');

            $stats[] = [
                'name' => $loc->localisation,
                'users' => $loc->user_count,
                'missions_mois' => Mission::whereIn('assigned_to', $locUsers)
                    ->thisMonth()
                    ->count(),
                'ca_mois' => Mission::whereIn('assigned_to', $locUsers)
                    ->completedThisMonth()
                    ->sum('revenue') ?: 0,
                'ca_facture_mois' => $caFactureLoc,
                
                // Formations
                'heures_formation_annee' => FormationRequest::whereIn('user_id', $locUsers)
                    ->completed()
                    ->thisYear()
                    ->sum('hours_completed'),
                'taux_formes_annee' => $this->getLocalisationTrainingRate($locUsers),
                
                // Communication
                'commandes_mois' => CommunicationOrder::whereIn('user_id', $locUsers)
                    ->whereMonth('ordered_at', now()->month)
                    ->whereYear('ordered_at', now()->year)
                    ->count(),
            ];
        }

        return $stats;
    }

    /**
     * Pipeline commercial (devis → facture → paiement)
     */
    private function getPipelineStats(): array
    {
        return [
            'devis_brouillon' => Quote::where('status', 'brouillon')->count(),
            'devis_envoyes' => Quote::where('status', 'envoye')->count(),
            'devis_acceptes' => Quote::where('status', 'accepte')->count(),
            'factures_brouillon' => Invoice::where('status', 'brouillon')->count(),
            'factures_emises' => Invoice::where('status', 'emise')->count(),
            'factures_payees' => Invoice::where('status', 'payee')->count(),
            'factures_en_retard' => Invoice::where('status', 'en_retard')->count(),
        ];
    }

    /**
     * Top performers
     */
    private function getTopPerformers(): array
    {
        $users = User::where('is_active', true)
            ->whereIn('role', ['collaborateur', 'manager'])
            ->get();

        $performers = [];

        foreach ($users as $user) {
            $caFacture = Invoice::where('user_id', $user->id)
                ->where('status', '!=', 'brouillon')
                ->whereMonth('issued_at', now()->month)
                ->whereYear('issued_at', now()->year)
                ->sum('total_ht');

            if ($caFacture > 0) {
                $performers[] = [
                    'user' => $user,
                    'ca_facture' => $caFacture,
                    'devis_count' => Quote::where('user_id', $user->id)
                        ->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year)
                        ->count(),
                ];
            }
        }

        // Trier par CA décroissant
        usort($performers, function($a, $b) {
            return $b['ca_facture'] <=> $a['ca_facture'];
        });

        return array_slice($performers, 0, 10);
    }

    /**
     * Calculer le taux de transformation (devis acceptés / devis envoyés)
     */
    private function calculateConversionRate(array $userIds): float
    {
        $devisEnvoyes = Quote::whereIn('user_id', $userIds)
            ->whereIn('status', ['envoye', 'accepte', 'refuse'])
            ->whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();

        $devisAcceptes = Quote::whereIn('user_id', $userIds)
            ->where('status', 'accepte')
            ->whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();

        if ($devisEnvoyes == 0) {
            return 0;
        }

        return round(($devisAcceptes / $devisEnvoyes) * 100, 1);
    }

    /**
     * Taux de transformation global
     */
    private function calculateGlobalConversionRate(): float
    {
        $devisEnvoyes = Quote::whereIn('status', ['envoye', 'accepte', 'refuse'])
            ->whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();

        $devisAcceptes = Quote::where('status', 'accepte')
            ->whereMonth('sent_at', now()->month)
            ->whereYear('sent_at', now()->year)
            ->count();

        if ($devisEnvoyes == 0) {
            return 0;
        }

        return round(($devisAcceptes / $devisEnvoyes) * 100, 1);
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
}