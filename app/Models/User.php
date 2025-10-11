<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'phone',
        'department',
        'localisation',
        'position',
        'avatar',
        'manager_id',
        'revenue_target',
        'is_active',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'revenue_target' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // =====================================
    // RELATIONS
    // =====================================

    /**
     * Relation avec le manager (utilisateur parent)
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Relation avec les collaborateurs (utilisateurs enfants)
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /**
     * Missions assignées à l'utilisateur
     */
    public function assignedMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'assigned_to');
    }

    /**
     * Missions créées par l'utilisateur
     */
    public function createdMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'created_by');
    }

    /**
     * Missions gérées par l'utilisateur (en tant que manager)
     */
    public function managedMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'manager_id');
    }

    /**
     * Actualités créées par l'utilisateur
     */
    public function createdNews(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    /**
     * Demandes de formation de l'utilisateur
     */
    public function formationRequests(): HasMany
    {
        return $this->hasMany(FormationRequest::class);
    }

    /**
     * Demandes de formation approuvées par l'utilisateur
     */
    public function approvedFormations(): HasMany
    {
        return $this->hasMany(FormationRequest::class, 'approved_by');
    }

    /**
     * Formations créées par l'utilisateur
     */
    public function createdFormations(): HasMany
    {
        return $this->hasMany(Formation::class, 'created_by');
    }

    /**
     * Commandes de communication passées par l'utilisateur
     */
    public function communicationOrders(): HasMany
    {
        return $this->hasMany(CommunicationOrder::class, 'user_id');
    }

    // =====================================
    // ACCESSEURS (GETTERS)
    // =====================================

    /**
     * Obtenir le nom complet de l'utilisateur
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Obtenir l'URL de l'avatar
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(storage_path('app/public/avatars/' . $this->avatar))) {
            return asset('storage/avatars/' . $this->avatar);
        }
        
        return $this->getDefaultAvatarUrl();
    }

    /**
     * Générer une URL d'avatar par défaut avec les initiales
     */
    public function getDefaultAvatarUrl(): string
    {
        $initials = substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1);
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&color=6366f1&background=e0e7ff&bold=true";
    }

    // =====================================
    // MÉTHODES DE RÔLE
    // =====================================

    /**
     * Vérifier si l'utilisateur est collaborateur
     */
    public function isCollaborateur(): bool
    {
        return $this->role === 'collaborateur';
    }

    /**
     * Vérifier si l'utilisateur est manager
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Vérifier si l'utilisateur est administrateur
     */
    public function isAdministrateur(): bool
    {
        return $this->role === 'administrateur';
    }

    /**
     * Vérifier si l'utilisateur a une permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = [
            'collaborateur' => ['view_own_data', 'create_requests'],
            'manager' => ['view_own_data', 'create_requests', 'view_team_data', 'manage_team'],
            'administrateur' => ['*']
        ];

        if ($this->role === 'administrateur') {
            return true;
        }

        return in_array($permission, $permissions[$this->role] ?? []);
    }

    // =====================================
    // KPI - MISSIONS
    // =====================================

    /**
     * Obtenir le chiffre d'affaires du mois en cours
     */
    public function getCurrentMonthRevenue(): float
    {
        return $this->assignedMissions()
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->where('status', 'termine')
            ->sum('revenue') ?? 0;
    }

    /**
     * Obtenir le nombre de missions terminées ce mois
     */
    public function getCompletedMissionsThisMonth(): int
    {
        return $this->assignedMissions()
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->where('status', 'termine')
            ->count();
    }

    /**
     * Obtenir le nombre de missions en retard
     */
    public function getOverdueMissions(): int
    {
        return $this->assignedMissions()
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['termine', 'annule'])
            ->count();
    }

    /**
     * Obtenir le taux de réalisation de l'objectif de CA
     */
    public function getRevenueAchievementRate(): float
    {
        if (!$this->revenue_target || $this->revenue_target == 0) {
            return 0;
        }

        $currentRevenue = $this->getCurrentMonthRevenue();
        return round(($currentRevenue / $this->revenue_target) * 100, 2);
    }

    // =====================================
    // KPI - FORMATIONS
    // =====================================

    /**
     * Obtenir le nombre d'heures de formation cette année
     */
    public function getFormationHoursThisYear(): int
    {
        return $this->formationRequests()
            ->completed()
            ->thisYear()
            ->sum('hours_completed') ?? 0;
    }

    /**
     * Obtenir le nombre de formations terminées
     */
    public function getCompletedFormationsCount(): int
    {
        return $this->formationRequests()->completed()->count();
    }

    /**
     * Obtenir le nombre de demandes de formation en attente
     */
    public function getPendingFormationRequests(): int
    {
        return $this->formationRequests()->pending()->count();
    }

    /**
     * Obtenir le nombre de demandes de formation approuvées
     */
    public function getApprovedFormationRequests(): int
    {
        return $this->formationRequests()->approved()->count();
    }

    /**
     * Vérifier si l'utilisateur a été formé cette année
     */
    public function isTrainedThisYear(): bool
    {
        return $this->formationRequests()
            ->completed()
            ->thisYear()
            ->exists();
    }

    // =====================================
    // KPI - COMMUNICATION
    // =====================================

    /**
     * Obtenir le nombre total de commandes
     */
    public function getTotalOrdersCount(): int
    {
        return $this->communicationOrders()->count();
    }

    /**
     * Obtenir le montant total des commandes
     */
    public function getTotalOrdersAmount(): float
    {
        return $this->communicationOrders()->sum('total_amount') ?? 0;
    }

    /**
     * Obtenir le nombre de commandes en attente
     */
    public function getPendingOrdersCount(): int
    {
        return $this->communicationOrders()
            ->where('status', 'en_attente')
            ->count();
    }

    /**
     * Obtenir le nombre de commandes ce mois
     */
    public function getOrdersThisMonth(): int
    {
        return $this->communicationOrders()
            ->whereMonth('ordered_at', now()->month)
            ->whereYear('ordered_at', now()->year)
            ->count();
    }

    /**
     * Obtenir le montant des commandes ce mois
     */
    public function getOrdersAmountThisMonth(): float
    {
        return $this->communicationOrders()
            ->whereMonth('ordered_at', now()->month)
            ->whereYear('ordered_at', now()->year)
            ->sum('total_amount') ?? 0;
    }

    // =====================================
    // KPI - ÉQUIPE (POUR MANAGERS)
    // =====================================

    /**
     * Obtenir le CA total de l'équipe ce mois
     */
    public function getTeamRevenueThisMonth(): float
    {
        if (!$this->isManager() && !$this->isAdministrateur()) {
            return 0;
        }

        return $this->subordinates()
            ->get()
            ->sum(fn($user) => $user->getCurrentMonthRevenue());
    }

    /**
     * Obtenir le nombre de collaborateurs formés cette année
     */
    public function getTrainedTeamMembersCount(): int
    {
        if (!$this->isManager() && !$this->isAdministrateur()) {
            return 0;
        }

        return $this->subordinates()
            ->get()
            ->filter(fn($user) => $user->isTrainedThisYear())
            ->count();
    }

    /**
     * Obtenir le taux de formation de l'équipe
     */
    public function getTeamTrainingRate(): float
    {
        if (!$this->isManager() && !$this->isAdministrateur()) {
            return 0;
        }

        $totalMembers = $this->subordinates()->count();
        
        if ($totalMembers == 0) {
            return 0;
        }

        $trainedMembers = $this->getTrainedTeamMembersCount();
        
        return round(($trainedMembers / $totalMembers) * 100, 2);
    }

    // =====================================
    // ÉVÉNEMENTS DU MODÈLE
    // =====================================

    protected static function booted()
    {
        static::creating(function ($user) {
            // Actions lors de la création d'un utilisateur
        });

        static::updating(function ($user) {
            // Actions lors de la mise à jour d'un utilisateur
        });
    }
}