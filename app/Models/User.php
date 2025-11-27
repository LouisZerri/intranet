<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;


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

    /**
     * Relation vers le manager de l'utilisateur.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Retourne les clients liés à cet utilisateur.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Retourne les subordonnés (collaborateurs managés) par cet utilisateur.
     */
    public function subordinates(): HasMany
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /**
     * Récupère les missions assignées à l'utilisateur.
     */
    public function assignedMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'assigned_to');
    }

    /**
     * Récupère les missions créées par cet utilisateur.
     */
    public function createdMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'created_by');
    }

    /**
     * Retourne les missions managées par cet utilisateur.
     */
    public function managedMissions(): HasMany
    {
        return $this->hasMany(Mission::class, 'manager_id');
    }

    /**
     * Retourne les actualités créées par l'utilisateur.
     */
    public function createdNews(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    /**
     * Les demandes de formation faites par l'utilisateur.
     */
    public function formationRequests(): HasMany
    {
        return $this->hasMany(FormationRequest::class);
    }

    /**
     * Les formations approuvées par cet utilisateur.
     */
    public function approvedFormations(): HasMany
    {
        return $this->hasMany(FormationRequest::class, 'approved_by');
    }

    /**
     * Les formations créées par cet utilisateur.
     */
    public function createdFormations(): HasMany
    {
        return $this->hasMany(Formation::class, 'created_by');
    }

    /**
     * Les commandes de communication faites par l'utilisateur.
     */
    public function communicationOrders(): HasMany
    {
        return $this->hasMany(CommunicationOrder::class, 'user_id');
    }

    /**
     * Retourne le nom complet de l'utilisateur.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * URL de l'avatar de l'utilisateur.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && file_exists(storage_path('app/public/avatars/' . $this->avatar))) {
            return asset('storage/avatars/' . $this->avatar);
        }

        return $this->getDefaultAvatarUrl();
    }

    /**
     * Génère une URL d'avatar par défaut utilisant les initiales.
     */
    public function getDefaultAvatarUrl(): string
    {
        $initials = substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1);
        return "https://ui-avatars.com/api/?name=" . urlencode($initials) . "&color=6366f1&background=e0e7ff&bold=true";
    }

    /**
     * Vérifie si l'utilisateur a le rôle collaborateur.
     */
    public function isCollaborateur(): bool
    {
        return $this->role === 'collaborateur';
    }

    /**
     * Vérifie si l'utilisateur a le rôle manager.
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Vérifie si l'utilisateur a le rôle administrateur.
     */
    public function isAdministrateur(): bool
    {
        return $this->role === 'administrateur';
    }

    /**
     * Vérifie si l'utilisateur a la permission demandée.
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

    /**
     * Détermine si l'utilisateur gère tous les départements.
     */
    public function managesAllDepartments(): bool
    {
        if (!$this->isManager() && !$this->isAdministrateur()) {
            return false;
        }

        return $this->managed_departments && in_array('*', $this->managed_departments);
    }

    /**
     * Retourne la liste des départements gérés par l'utilisateur.
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
     * Indique si l'utilisateur peut gérer un département donné.
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

    /**
     * Chiffre d'affaires HT sur les missions terminées ce mois-ci.
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
     * Nombre de missions terminées ce mois-ci.
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
     * Nombre de missions en retard pour l'utilisateur.
     */
    public function getOverdueMissions(): int
    {
        return $this->assignedMissions()
            ->where('due_date', '<', now())
            ->whereNotIn('status', ['termine', 'annule'])
            ->count();
    }

    /**
     * Taux d'atteinte du CA (chiffre d'affaires) cible ce mois-ci.
     */
    public function getRevenueAchievementRate(): float
    {
        if (!$this->revenue_target || $this->revenue_target == 0) {
            return 0;
        }

        $currentRevenue = $this->getCurrentMonthRevenue();
        return round(($currentRevenue / $this->revenue_target) * 100, 2);
    }

    /**
     * Nombre d'heures de formation complétées cette année.
     */
    public function getFormationHoursThisYear(): int
    {
        return $this->formationRequests()
            ->completed()
            ->thisYear()
            ->sum('hours_completed') ?? 0;
    }

    /**
     * Nombre de formations terminées.
     */
    public function getCompletedFormationsCount(): int
    {
        return $this->formationRequests()->completed()->count();
    }

    /**
     * Nombre de demandes de formation en attente.
     */
    public function getPendingFormationRequests(): int
    {
        return $this->formationRequests()->pending()->count();
    }

    /**
     * Nombre de demandes de formation approuvées.
     */
    public function getApprovedFormationRequests(): int
    {
        return $this->formationRequests()->approved()->count();
    }

    /**
     * Indique si l'utilisateur a complété une formation cette année.
     */
    public function isTrainedThisYear(): bool
    {
        return $this->formationRequests()
            ->completed()
            ->thisYear()
            ->exists();
    }

    /**
     * Nombre total de commandes de communication.
     */
    public function getTotalOrdersCount(): int
    {
        return $this->communicationOrders()->count();
    }

    /**
     * Montant total des commandes de communication.
     */
    public function getTotalOrdersAmount(): float
    {
        return $this->communicationOrders()->sum('total_amount') ?? 0;
    }

    /**
     * Nombre de commandes de communication en attente.
     */
    public function getPendingOrdersCount(): int
    {
        return $this->communicationOrders()
            ->where('status', 'en_attente')
            ->count();
    }

    /**
     * Nombre de commandes faites ce mois-ci.
     */
    public function getOrdersThisMonth(): int
    {
        return $this->communicationOrders()
            ->whereMonth('ordered_at', now()->month)
            ->whereYear('ordered_at', now()->year)
            ->count();
    }

    /**
     * Montant total des commandes sur le mois en cours.
     */
    public function getOrdersAmountThisMonth(): float
    {
        return $this->communicationOrders()
            ->whereMonth('ordered_at', now()->month)
            ->whereYear('ordered_at', now()->year)
            ->sum('total_amount') ?? 0;
    }

    /**
     * Chiffre d'affaires du mois de toute l'équipe gérée.
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
     * Nombre de membres de l'équipe ayant suivi une formation cette année.
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
     * Taux de formation de l'équipe ce mois-ci.
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

    /**
     * Calcule le CA net URSSAF sur une période avec les charges retraitées.
     */
    public function getNetURSSAFRevenue(Carbon $startDate, Carbon $endDate): array
    {
        $data = Invoice::getURSSAFRevenue($this, $startDate, $endDate);

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
     * Adresse professionnelle formatée avec la ville et le code postal.
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
     * Retourne l'email professionnel s'il existe, sinon l'email principal.
     */
    public function getEffectiveEmailAttribute(): string
    {
        return $this->professional_email ?? $this->email;
    }

    /**
     * Retourne le téléphone professionnel ou le principal.
     */
    public function getEffectivePhoneAttribute(): ?string
    {
        return $this->professional_phone ?? $this->phone;
    }

    /**
     * Vérifie si toutes les infos professionnelles obligatoires sont renseignées.
     */
    public function hasProfessionalInfoComplete(): bool
    {
        return !empty($this->rsac_number)
            && !empty($this->professional_address)
            && !empty($this->professional_city)
            && !empty($this->professional_postal_code);
    }

    /**
     * Retourne l'URL de la signature si renseignée.
     */
    public function getSignatureUrlAttribute(): ?string
    {
        if (empty($this->signature_image)) {
            return null;
        }

        return asset('storage/signatures/' . $this->signature_image);
    }
}