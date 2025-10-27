<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

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

    // Relations
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
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

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'en_attente');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approuve');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'termine');
    }

    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereYear('requested_at', now()->year);
    }

    // Méthodes utilitaires
    public function isPending(): bool
    {
        return $this->status === 'en_attente';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approuve';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'termine';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'en_attente';
    }

    /**
     * Vérifie si un utilisateur peut approuver cette demande
     * Règles :
     * - La demande doit être en attente
     * - L'utilisateur ne peut pas approuver sa propre demande
     * - Admin peut approuver toutes les demandes (sauf les siennes)
     * - Manager peut approuver les demandes de son équipe uniquement
     */
    public function canBeApprovedBy(User $user): bool
    {
        // La demande doit être en attente
        if (!$this->canBeApproved()) {
            return false;
        }

        // RÈGLE CRITIQUE : On ne peut pas approuver sa propre demande
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

    public function getDaysWaiting(): int
    {
        return $this->requested_at->diffInDays(now());
    }

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

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'haute' => 'Haute',
            'normale' => 'Normale',
            'basse' => 'Basse',
            default => 'Normale'
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'haute' => 'red',
            'normale' => 'orange',
            'basse' => 'gray',
            default => 'gray'
        };
    }

    // Méthodes pour workflow
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

    // Événements du modèle
    protected static function booted()
    {
        static::creating(function ($request) {
            if (!$request->requested_at) {
                $request->requested_at = now();
            }
        });
    }
}