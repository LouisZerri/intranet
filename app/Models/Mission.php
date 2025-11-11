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
        'quote_id', // NOUVEAU : Lien vers le devis d'origine
        'title',
        'description',
        'status',
        'priority',
        'category',
        'subcategory',
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

    // Catégories et sous-catégories de missions
    public static function getCategories(): array
    {
        return [
            'location' => 'Location',
            'syndic' => 'Syndic',
            'autres' => 'Autres'
        ];
    }

    public static function getSubcategories(): array
    {
        return [
            'location' => [
                'visite_locataire' => 'Visite avec locataire potentiel',
                'etat_lieux_entree' => 'État des lieux d\'entrée',
                'etat_lieux_sortie' => 'État des lieux de sortie',
                'gestion_charges' => 'Gestion des charges locatives',
                'revision_loyer' => 'Révision de loyer',
                'recouvrement' => 'Recouvrement de loyers',
                'travaux_locatif' => 'Suivi travaux locatifs',
                'regularisation_charges' => 'Régularisation des charges',
            ],
            'syndic' => [
                'ag_copropriete' => 'Assemblée générale de copropriété',
                'conseil_syndical' => 'Réunion conseil syndical',
                'devis_travaux' => 'Demande de devis travaux',
                'suivi_travaux' => 'Suivi de travaux copropriété',
                'gestion_sinistre' => 'Gestion de sinistre',
                'budget_previsionnel' => 'Élaboration budget prévisionnel',
                'appels_fonds' => 'Gestion appels de fonds',
                'comptabilite_syndic' => 'Comptabilité de copropriété',
            ],
            'autres' => [
                'prospection_commerciale' => 'Prospection commerciale',
                'formation_interne' => 'Formation interne',
                'reporting_direction' => 'Reporting direction',
                'veille_juridique' => 'Veille juridique',
                'relation_client' => 'Relation client',
                'administration' => 'Tâches administratives',
                'projet_special' => 'Projet spécial',
                'audit_interne' => 'Audit interne',
            ]
        ];
    }

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

    /**
     * NOUVEAU : Relation avec le devis d'origine
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
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
                  ->orWhere('created_by', $user->id)
                  ->orWhere('manager_id', $user->id)
                  ->orWhereHas('assignedUser', function ($subQ) use ($user) {
                      $subQ->where('manager_id', $user->id);
                  });
            });
        }

        return $query->where(function ($q) use ($user) {
            $q->where('assigned_to', $user->id)
              ->orWhere('created_by', $user->id);
        });
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

    public function scopeByCategory(Builder $query, string $category = null): Builder
    {
        if ($category) {
            return $query->where('category', $category);
        }
        return $query;
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

    // Accesseurs pour les catégories
    public function getCategoryLabelAttribute(): string
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? 'Non défini';
    }

    public function getSubcategoryLabelAttribute(): string
    {
        $subcategories = self::getSubcategories();
        return $subcategories[$this->category][$this->subcategory] ?? 'Non défini';
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

    public function getDaysUntilDue(): int
    {
        if (!$this->due_date) {
            return 0;
        }

        $days = now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);
        return (int) round($days);
    }

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

    public function getDueColorAttribute(): string
    {
        if (!$this->due_date) {
            return 'gray';
        }

        $days = $this->getDaysUntilDue();
        
        if ($days < 0) {
            return 'red';
        } elseif ($days <= 1) {
            return 'orange';
        } elseif ($days <= 3) {
            return 'yellow';
        } else {
            return 'green';
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
            if ($mission->due_date && 
                $mission->due_date->isPast() && 
                !in_array($mission->status, ['termine', 'annule'])) {
                $mission->status = 'en_retard';
            }

            if ($mission->status === 'termine' && !$mission->completed_at) {
                $mission->completed_at = now();
            }
        });
    }
}