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
        'position',
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

    // Relations existantes
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
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

    public function internalRequests(): HasMany
    {
        return $this->hasMany(InternalRequest::class, 'requested_by');
    }

    public function approvedRequests(): HasMany
    {
        return $this->hasMany(InternalRequest::class, 'approved_by');
    }

    public function createdNews(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }

    // Relations formations (NOUVELLES)
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

    // Méthodes utilitaires existantes
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

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
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

    // Calculs KPI existants
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

    public function getPendingInternalRequests(): int
    {
        return $this->internalRequests()
            ->where('status', 'en_attente')
            ->count();
    }

    // Calculs KPI formations (NOUVEAUX)
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

    // Méthode pour vérifier si l'utilisateur est formé cette année (pour KPI manager)
    public function isTrainedThisYear(): bool
    {
        return $this->formationRequests()
                    ->completed()
                    ->thisYear()
                    ->exists();
    }
}