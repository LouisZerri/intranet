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
        'siret',
        'urssaf_fixed_charges',
        'department',
        'localisation',
        'position',
        'avatar',
        'manager_id',
        'revenue_target',
        'is_active',
        'last_login_at',
         'rsac_number',
        'professional_address',
        'professional_city',
        'professional_postal_code',
        'professional_email',
        'professional_phone',
        'legal_mentions',
        'footer_text',
        'signature_image',
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
        'urssaf_fixed_charges' => 'array'
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

    /**
     * Obtenir le CA net URSSAF (avec déduction des charges forfaitaires)
     * CDC Section D - Option bonus
     */
    public function getNetURSSAFRevenue(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): array
    {
        $data = \App\Models\Invoice::getURSSAFRevenue($this, $startDate, $endDate);

        // Charges forfaitaires (communication, RC pro, etc.)
        $fixedCharges = $this->urssaf_fixed_charges ?? [];
        $totalCharges = collect($fixedCharges)->sum('amount');

        // Calcul du CA net déclarable URSSAF
        $data['total_charges'] = $totalCharges;
        $data['net_ht'] = $data['total_ht'] - $totalCharges;
        $data['net_ttc'] = $data['total_ttc'] - $totalCharges;
        $data['charges_detail'] = $fixedCharges;

        return $data;
    }

    /**
     * Obtenir l'adresse professionnelle complète formatée
     */
    public function getFormattedProfessionalAddressAttribute(): ?string
    {
        if (!$this->professional_address) {
            return null;
        }

        $address = $this->professional_address;

        if ($this->professional_postal_code || $this->professional_city) {
            $address .= "\n";
            if ($this->professional_postal_code) {
                $address .= $this->professional_postal_code . ' ';
            }
            if ($this->professional_city) {
                $address .= $this->professional_city;
            }
        }

        return $address;
    }

    /**
     * Obtenir l'email à utiliser (professionnel ou personnel)
     */
    public function getEffectiveEmailAttribute(): string
    {
        return $this->professional_email ?? $this->email;
    }

    /**
     * Obtenir le téléphone à utiliser (professionnel ou personnel)
     */
    public function getEffectivePhoneAttribute(): ?string
    {
        return $this->professional_phone ?? $this->phone;
    }

    /**
     * Vérifier si l'utilisateur a configuré ses infos professionnelles
     */
    public function hasProfessionalInfoComplete(): bool
    {
        return !empty($this->rsac_number)
            && !empty($this->professional_address)
            && !empty($this->professional_city)
            && !empty($this->professional_postal_code);
    }

    /**
     * Obtenir l'URL de la signature
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if (empty($this->signature_image)) {
            return null;
        }

        return asset('storage/signatures/' . $this->signature_image);
    }
}
