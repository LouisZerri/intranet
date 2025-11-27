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
        'quote_id',
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

    /**
     * Retourne la liste des catégories disponibles pour les missions.
     */
    public static function getCategories(): array
    {
        return [
            'location' => 'Location',
            'syndic' => 'Syndic',
            'autres' => 'Autres'
        ];
    }

    /**
     * Retourne la liste des sous-catégories, classées par catégorie.
     */
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

    /**
     * Relation avec l'utilisateur assigné à la mission.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Relation avec le créateur de la mission.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation avec le manager de la mission.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Relation avec le devis lié à la mission.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Scope : filtre la requête pour afficher les missions accessibles à l'utilisateur donné.
     */
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

    /**
     * Scope : missions en cours (en attente ou en cours).
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->whereIn('status', ['en_attente', 'en_cours']);
    }

    /**
     * Scope : missions terminées.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'termine');
    }

    /**
     * Scope : missions en retard (échéance dépassée et non terminée/annulée).
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['termine', 'annule']);
    }

    /**
     * Scope : filtre par catégorie si $category n'est pas null.
     */
    public function scopeByCategory(Builder $query, ?string $category = null): Builder
    {
        if ($category !== null) {
            return $query->where('category', $category);
        }
        return $query;
    }

    /**
     * Scope : missions créées ce mois-ci.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope : missions terminées ce mois-ci.
     */
    public function scopeCompletedThisMonth(Builder $query): Builder
    {
        return $query->where('status', 'termine')
                    ->whereMonth('completed_at', now()->month)
                    ->whereYear('completed_at', now()->year);
    }

    /**
     * Scope : filtre par priorité, sinon retourne le trié par priorité.
     */
    public function scopeByPriority(Builder $query, ?string $priority = null): Builder
    {
        if ($priority !== null) {
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

    /**
     * Retourne le label de la catégorie.
     */
    public function getCategoryLabelAttribute(): string
    {
        $categories = self::getCategories();
        return $categories[$this->category] ?? 'Non défini';
    }

    /**
     * Retourne le label de la sous-catégorie.
     */
    public function getSubcategoryLabelAttribute(): string
    {
        $subcategories = self::getSubcategories();
        return $subcategories[$this->category][$this->subcategory] ?? 'Non défini';
    }

    /**
     * Indique si la mission est en retard (échéance passée et pas terminée/annulée).
     */
    public function isOverdue(): bool
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !in_array($this->status, ['termine', 'annule']);
    }

    /**
     * Indique si la mission est terminée.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'termine';
    }

    /**
     * Retourne le nombre de jours restant jusqu'à l'échéance.
     */
    public function getDaysUntilDue(): int
    {
        if (!$this->due_date) {
            return 0;
        }

        $days = now()->startOfDay()->diffInDays($this->due_date->startOfDay(), false);
        return (int) round($days);
    }

    /**
     * Retourne un texte lisible sur le temps restant/retard avant l'échéance.
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
     * Code couleur de l'échéance en fonction de l'urgence/retard.
     */
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

    /**
     * Retourne le pourcentage d'avancement selon le status.
     */
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

    /**
     * Retourne le label du status de la mission.
     */
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

    /**
     * Retourne la couleur associée au status de la mission.
     */
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

    /**
     * Retourne le label de la priorité.
     */
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

    /**
     * Retourne la couleur associée à la priorité.
     */
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

    /**
     * Calcule la somme totale du chiffre d'affaires de l'équipe manager sur la période donnée.
     */
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

    /**
     * Retourne le taux de missions terminées pour un utilisateur sur une période.
     */
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

    /**
     * Hook Eloquent : met à jour les statuts automatiquement lors d'une modification.
     */
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