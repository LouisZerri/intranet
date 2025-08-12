<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class InternalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'comments',
        'prestation_type',
        'status',
        'rejection_reason',
        'requested_by',
        'approved_by',
        'assigned_to',
        'requested_at',
        'approved_at',
        'completed_at',
        'attachments',
        'estimated_cost'
    ];

    protected $casts = [
        'attachments' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
    ];

    // Relations
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Scopes
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdministrateur()) {
            return $query;
        }

        if ($user->isManager()) {
            return $query->where(function ($q) use ($user) {
                $q->where('requested_by', $user->id)
                  ->orWhere('approved_by', $user->id)
                  ->orWhere('assigned_to', $user->id)
                  ->orWhereHas('requester', function ($subQ) use ($user) {
                      $subQ->where('manager_id', $user->id);
                  });
            });
        }

        return $query->where('requested_by', $user->id);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'en_attente');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'valide');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejete');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'termine');
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('requested_at', now()->month)
                    ->whereYear('requested_at', now()->year);
    }

    // Méthodes utilitaires
    public function isPending(): bool
    {
        return $this->status === 'en_attente';
    }

    public function isApproved(): bool
    {
        return $this->status === 'valide';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejete';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'termine';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'en_attente';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'en_attente';
    }

    public function getDaysWaiting(): int
    {
        return $this->requested_at->diffInDays(now());
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'achat_produit_communication' => 'Achat produit communication',
            'documentation_manager' => 'Documentation manager',
            'prestation' => 'Prestation',
            default => 'Autre'
        };
    }

    public function getPrestationTypeLabelAttribute(): ?string
    {
        if ($this->type !== 'prestation' || !$this->prestation_type) {
            return null;
        }

        return match($this->prestation_type) {
            'location' => 'Location',
            'syndic' => 'Syndic',
            'menage' => 'Ménage',
            'travaux' => 'Travaux',
            'autres_administratifs' => 'Autres administratifs',
            default => 'Autre'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'En attente',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            default => 'Inconnu'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'yellow',
            'valide' => 'green',
            'rejete' => 'red',
            'en_cours' => 'blue',
            'termine' => 'green',
            default => 'gray'
        };
    }

    public function getUrgencyLevel(): string
    {
        $daysWaiting = $this->getDaysWaiting();
        
        if ($daysWaiting > 7) {
            return 'high';
        } elseif ($daysWaiting > 3) {
            return 'medium';
        }
        
        return 'low';
    }

    // Méthodes pour workflow d'approbation
    public function approve(User $approver, ?User $assignedTo = null): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->update([
            'status' => 'valide',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'assigned_to' => $assignedTo?->id
        ]);

        return true;
    }

    public function reject(User $approver, string $reason): bool
    {
        if (!$this->canBeRejected()) {
            return false;
        }

        $this->update([
            'status' => 'rejete',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason
        ]);

        return true;
    }

    public function complete(): bool
    {
        if (!in_array($this->status, ['valide', 'en_cours'])) {
            return false;
        }

        $this->update([
            'status' => 'termine',
            'completed_at' => now()
        ]);

        return true;
    }

    // Méthodes pour les KPI
    public static function getApprovalRate(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = self::whereIn('status', ['valide', 'rejete']);

        if ($startDate) {
            $query->where('approved_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('approved_at', '<=', $endDate);
        }

        $total = $query->count();
        $approved = (clone $query)->where('status', 'valide')->count();

        return $total > 0 ? round(($approved / $total) * 100, 2) : 0;
    }

    public static function getAverageProcessingTime(?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = self::whereNotNull('approved_at');

        if ($startDate) {
            $query->where('approved_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('approved_at', '<=', $endDate);
        }

        $requests = $query->get();
        
        if ($requests->isEmpty()) {
            return 0;
        }

        $totalDays = $requests->sum(function ($request) {
            return $request->requested_at->diffInDays($request->approved_at);
        });

        return round($totalDays / $requests->count(), 2);
    }

    // Événements du modèle
    protected static function booted()
    {
        static::creating(function ($request) {
            if (!$request->requested_at) {
                $request->requested_at = now();
            }
        });

        static::updating(function ($request) {
            if ($request->status === 'valide' && !$request->approved_at) {
                $request->approved_at = now();
            }

            if ($request->status === 'termine' && !$request->completed_at) {
                $request->completed_at = now();
            }
        });
    }
}