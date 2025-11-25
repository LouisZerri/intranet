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
        'managed_departments',
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
        'urssaf_fixed_charges' => 'array',
        'managed_departments' => 'array',
    ];

    // =====================================
    // RELATIONS
    // =====================================

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    public function assignedMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'assigned_to');
    }

    public function createdMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'created_by');
    }

    public function managedMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'manager_id');
    }

    public function createdNews(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    public function formationRequests(): HasMany
    {
        return $this->hasMany(FormationRequest::class);
    }

    public function approvedFormations(): HasMany
    {
        return $this->hasMany(FormationRequest::class, 'approved_by');
    }

    public function createdFormations(): HasMany
    {
        return $this->hasMany(Formation::class, 'created_by');
    }

    public function communicationOrders(): HasMany
    {
        return $this->hasMany(CommunicationOrder::class, 'user_id');
    }

    // =====================================
    // ACCESSEURS (GETTERS)
    // =====================================

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(storage_path('app/public/avatars/' . $this->avatar))) {
            return asset('storage/avatars/' . $this->avatar);
        }

        return $this->getDefaultAvatarUrl();
    }

    public function getDefaultAvatarUrl(): string
    {
        $initials = substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1);
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&color=6366f1&background=e0e7ff&bold=true";
    }

    // =====================================
    // MÉTHODES DE RÔLE
    // =====================================

    public function isCollaborateur(): bool
    {
        return $this->role === 'collaborateur';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isAdministrateur(): bool
    {
        return $this->role === 'administrateur';
    }

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
    // GESTION DES DÉPARTEMENTS
    // =====================================

    /**
     * Vérifier si le manager/admin gère tous les départements
     */
    public function managesAllDepartments(): bool
    {
        if (!$this->isManager() && !$this->isAdministrateur()) {
            return false;
        }

        return $this->managed_departments && in_array('*', $this->managed_departments);
    }

    /**
     * Obtenir la liste des départements gérés
     */
    public function getManagedDepartments(): array
    {
        if (!$this->isManager() && !$this->isAdministrateur()) {
            return [];
        }

        if ($this->managesAllDepartments()) {
            return ['*'];
        }

        return $this->managed_departments ?? [];
    }

    /**
     * Vérifier si le manager/admin peut voir un utilisateur d'un département donné
     */
    public function canManageDepartment(?string $department): bool
    {
        if (!$this->isManager() && !$this->isAdministrateur()) {
            return false;
        }

        // Si pas de département spécifié
        if (empty($department)) {
            return false;
        }

        // Si gère tous les départements
        if ($this->managesAllDepartments()) {
            return true;
        }

        // Sinon vérifier si le département est dans la liste
        $managedDepts = $this->getManagedDepartments();
        return in_array($department, $managedDepts);
    }

    // =====================================
    // KPI - MISSIONS
    // =====================================

    public function getCurrentMonthRevenue(): float
    {
        return $this->assignedMissions()
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->where('status', 'termine')
            ->sum('revenue') ?? 0;
    }

    public function getCompletedMissionsThisMonth(): int
    {
        return $this->assignedMissions()
            ->whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->where('status', 'termine')
            ->count();
    }

    public function getOverdueMissions(): int
    {
        return $this->assignedMissions()
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['termine', 'annule'])
            ->count();
    }

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

    public function getFormationHoursThisYear(): int
    {
        return $this->formationRequests()
            ->completed()
            ->thisYear()
            ->sum('hours_completed') ?? 0;
    }

    public function getCompletedFormationsCount(): int
    {
        return $this->formationRequests()->completed()->count();
    }

    public function getPendingFormationRequests(): int
    {
        return $this->formationRequests()->pending()->count();
    }

    public function getApprovedFormationRequests(): int
    {
        return $this->formationRequests()->approved()->count();
    }

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

    public function getTotalOrdersCount(): int
    {
        return $this->communicationOrders()->count();
    }

    public function getTotalOrdersAmount(): float
    {
        return $this->communicationOrders()->sum('total_amount') ?? 0;
    }

    public function getPendingOrdersCount(): int
    {
        return $this->communicationOrders()
            ->where('status', 'en_attente')
            ->count();
    }

    public function getOrdersThisMonth(): int
    {
        return $this->communicationOrders()
            ->whereMonth('ordered_at', now()->month)
            ->whereYear('ordered_at', now()->year)
            ->count();
    }

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

    public function getTeamRevenueThisMonth(): float
    {
        if (!$this->isManager() && !$this->isAdministrateur()) {
            return 0;
        }

        return $this->subordinates()
            ->get()
            ->sum(fn($user) => $user->getCurrentMonthRevenue());
    }

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