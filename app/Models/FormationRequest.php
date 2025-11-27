<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;


class FormationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_id',
        'user_id',
        'status',
        'motivation',
        'manager_comments',
        'priority',
        'approved_by',
        'requested_at',
        'approved_at',
        'completed_at',
        'final_cost',
        'hours_completed',
        'feedback',
        'rating'
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'final_cost' => 'decimal:2',
    ];

    /**
     * Récupère la formation associée à la demande.
     */
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * Récupère l'utilisateur ayant fait la demande.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère l'approbateur (manager ou admin).
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope : filtre les demandes accessibles par l'utilisateur (admin, manager, employé).
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdministrateur()) {
            return $query;
        }

        if ($user->isManager()) {
            return $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('approved_by', $user->id)
                  ->orWhereHas('user', function ($subQ) use ($user) {
                      $subQ->where('manager_id', $user->id);
                  });
            });
        }

        return $query->where('user_id', $user->id);
    }

    /**
     * Scope : ne récupérer que les demandes en attente.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'en_attente');
    }

    /**
     * Scope : ne récupérer que les demandes approuvées.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approuve');
    }

    /**
     * Scope : ne récupérer que les demandes terminées.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'termine');
    }

    /**
     * Scope : ne récupérer que les demandes de l'année en cours.
     */
    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereYear('requested_at', now()->year);
    }

    /**
     * Indique si la demande est en attente.
     */
    public function isPending(): bool
    {
        return $this->status === 'en_attente';
    }

    /**
     * Indique si la demande est approuvée.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approuve';
    }

    /**
     * Indique si la demande est terminée.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'termine';
    }

    /**
     * Indique si la demande peut être approuvée (seulement si en attente).
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'en_attente';
    }

    /**
     * Vérifie si l'utilisateur donné peut approuver cette demande.
     */
    public function canBeApprovedBy(User $user): bool
    {
        // La demande doit être en attente
        if (!$this->canBeApproved()) {
            return false;
        }

        // On ne peut pas approuver sa propre demande
        if ($this->user_id === $user->id) {
            return false;
        }

        // Admin peut approuver toutes les demandes (sauf les siennes)
        if ($user->isAdministrateur()) {
            return true;
        }

        // Manager peut approuver les demandes de son équipe uniquement
        if ($user->isManager()) {
            return $this->user->manager_id === $user->id;
        }

        return false;
    }

    /**
     * Retourne le nombre de jours depuis la création de la demande.
     */
    public function getDaysWaiting(): int
    {
        return $this->requested_at->diffInDays(now());
    }

    /**
     * Accesseur : retourne le label humain du statut.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'En attente',
            'approuve' => 'Approuvé',
            'refuse' => 'Refusé',
            'termine' => 'Terminé',
            default => 'Inconnu'
        };
    }

    /**
     * Accesseur : retourne la couleur Tailwind correspondant au statut.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'yellow',
            'approuve' => 'green',
            'refuse' => 'red',
            'termine' => 'blue',
            default => 'gray'
        };
    }

    /**
     * Accesseur : retourne le label humain de la priorité.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'haute' => 'Haute',
            'normale' => 'Normale',
            'basse' => 'Basse',
            default => 'Normale'
        };
    }

    /**
     * Accesseur : retourne la couleur Tailwind correspondant à la priorité.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'haute' => 'red',
            'normale' => 'orange',
            'basse' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Approuve la demande (si possible).
     */
    public function approve(User $approver, ?string $comments = null): bool
    {
        if (!$this->canBeApprovedBy($approver)) {
            return false;
        }

        $this->update([
            'status' => 'approuve',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'manager_comments' => $comments
        ]);

        return true;
    }

    /**
     * Refuse la demande (si possible).
     */
    public function reject(User $approver, string $comments): bool
    {
        if (!$this->canBeApprovedBy($approver)) {
            return false;
        }

        $this->update([
            'status' => 'refuse',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'manager_comments' => $comments
        ]);

        return true;
    }

    /**
     * Termine la demande (si approuvée).
     */
    public function complete(int $hoursCompleted, ?string $feedback = null, ?int $rating = null): bool
    {
        if (!$this->isApproved()) {
            return false;
        }

        $this->update([
            'status' => 'termine',
            'completed_at' => now(),
            'hours_completed' => $hoursCompleted,
            'feedback' => $feedback,
            'rating' => $rating
        ]);

        return true;
    }

    /**
     * Boot : définir la date de demande si absente à la création.
     */
    protected static function booted()
    {
        static::creating(function ($request) {
            if (!$request->requested_at) {
                $request->requested_at = now();
            }
        });
    }
}