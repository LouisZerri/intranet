<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Mission extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'assigned_to',
        'created_by',
        'manager_id',
        'revenue',
        'start_date',
        'due_date',
        'completed_at',
        'notes',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
        'revenue' => 'decimal:2',
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    // Relations
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // Scopes
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdministrateur()) {
            return $query;
        }

        if ($user->isManager()) {
            return $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                  ->orWhere('manager_id', $user->id)
                  ->orWhereHas('assignedUser', function ($subQ) use ($user) {
                      $subQ->where('manager_id', $user->id);
                  });
            });
        }

        return $query->where('assigned_to', $user->id);
    }

    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereIn('status', ['en_attente', 'en_cours']);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'termine');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['termine', 'annule']);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeCompletedThisMonth(Builder $query): Builder
    {
        return $query->where('status', 'termine')
                    ->whereMonth('completed_at', now()->month)
                    ->whereYear('completed_at', now()->year);
    }

    public function scopeByPriority(Builder $query, string $priority = null): Builder
    {
        if ($priority) {
            return $query->where('priority', $priority);
        }
        
        return $query->orderByRaw("
            CASE priority 
                WHEN 'urgente' THEN 1 
                WHEN 'haute' THEN 2 
                WHEN 'normale' THEN 3 
                WHEN 'basse' THEN 4 
            END
        ");
    }

    // Méthodes utilitaires
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !in_array($this->status, ['termine', 'annule']);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'termine';
    }

    /**
     * Calcule le nombre de jours jusqu'à l'échéance (CORRECTION)
     * Retourne un entier pour éviter les décimales
     */
    public function getDaysUntilDue(): int
    {
        if (!$this->due_date) {
            return 0;
        }

        // CORRECTION: Utiliser diffInDays avec false pour obtenir un nombre signé
        // et arrondir à l'entier le plus proche
        $days = now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);
        return (int) round($days);
    }

    /**
     * Retourne un texte formaté pour l'affichage de l'échéance
     */
    public function getDueStatusAttribute(): string
    {
        if (!$this->due_date) {
            return 'Aucune échéance';
        }

        $days = $this->getDaysUntilDue();
        
        if ($days < 0) {
            $absdays = abs($days);
            return $absdays === 1 ? "En retard de 1 jour" : "En retard de {$absdays} jours";
        } elseif ($days === 0) {
            return "Échéance aujourd'hui";
        } elseif ($days === 1) {
            return "Échéance demain";
        } else {
            return "Dans {$days} jours";
        }
    }

    /**
     * Couleur pour l'affichage de l'échéance
     */
    public function getDueColorAttribute(): string
    {
        if (!$this->due_date) {
            return 'gray';
        }

        $days = $this->getDaysUntilDue();
        
        if ($days < 0) {
            return 'red';     // En retard
        } elseif ($days <= 1) {
            return 'orange';  // Urgent (aujourd'hui ou demain)
        } elseif ($days <= 3) {
            return 'yellow';  // Proche
        } else {
            return 'green';   // Temps suffisant
        }
    }

    public function getProgressPercentage(): int
    {
        return match($this->status) {
            'en_attente' => 0,
            'en_cours' => 50,
            'termine' => 100,
            'annule' => 0,
            'en_retard' => 25,
            default => 0
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminée',
            'annule' => 'Annulée',
            'en_retard' => 'En retard',
            default => 'Inconnu'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'yellow',
            'en_cours' => 'blue',
            'termine' => 'green',
            'annule' => 'gray',
            'en_retard' => 'red',
            default => 'gray'
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'urgente' => 'Urgente',
            'haute' => 'Haute',
            'normale' => 'Normale',
            'basse' => 'Basse',
            default => 'Normale'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgente' => 'red',
            'haute' => 'orange',
            'normale' => 'blue',
            'basse' => 'gray',
            default => 'blue'
        };
    }

    // Méthodes pour les KPI
    public static function getTeamRevenue(User $manager, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = self::whereHas('assignedUser', function ($q) use ($manager) {
            $q->where('manager_id', $manager->id);
        })
        ->where('status', 'termine')
        ->whereNotNull('revenue');

        if ($startDate) {
            $query->where('completed_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('completed_at', '<=', $endDate);
        }

        return $query->sum('revenue') ?? 0;
    }

    public static function getCompletionRate(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $baseQuery = self::where('assigned_to', $user->id);

        if ($startDate) {
            $baseQuery->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $baseQuery->where('created_at', '<=', $endDate);
        }

        $total = $baseQuery->count();
        $completed = (clone $baseQuery)->where('status', 'termine')->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    // Événements du modèle
    protected static function booted()
    {
        static::updating(function ($mission) {
            // Marquer automatiquement les missions en retard
            if ($mission->due_date && 
                $mission->due_date->isPast() && 
                !in_array($mission->status, ['termine', 'annule'])) {
                $mission->status = 'en_retard';
            }

            // Définir la date de completion
            if ($mission->status === 'termine' && !$mission->completed_at) {
                $mission->completed_at = now();
            }
        });
    }
}