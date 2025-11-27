<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class News extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'image',
        'priority',
        'status',
        'target_roles',
        'target_departments',
        'published_at',
        'expires_at',
        'author_id'
    ];

    protected $casts = [
        'target_roles' => 'array',
        'target_departments' => 'array',
        'published_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relation avec l'auteur de l'actualité.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope: Filtre les actualités publiées, c'est-à-dire dont le statut est "published", dont la date de publication est passée,
     * et dont la date d'expiration n'est pas atteinte (ou non renseignée).
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope: Filtre les actualités pour un utilisateur donné en fonction de ses rôles et départements cibles.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            $q->whereNull('target_roles')
                ->orWhereJsonContains('target_roles', $user->role);
        })->where(function ($q) use ($user) {
            $q->whereNull('target_departments')
                ->orWhereJsonContains('target_departments', $user->department);
        });
    }

    /**
     * Scope: Permet de filtrer ou d’ordonner les actualités selon la priorité.
     * Si une priorité est donnée, filtre sur celle-ci. Sinon, trie par priorité : urgent > important > normal.
     */
    public function scopeByPriority(Builder $query, ?string $priority = null): Builder
    {
        if ($priority !== null) {
            return $query->where('priority', $priority);
        }

        return $query->orderByRaw("
            CASE priority 
                WHEN 'urgent' THEN 1 
                WHEN 'important' THEN 2 
                WHEN 'normal' THEN 3 
            END
        ");
    }

    /**
     * Détermine si l’actualité est expirée (si expires_at est renseigné et est passé).
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Détermine si l’actualité est actuellement publiée (statut published, date de publication passée, non expirée).
     */
    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at <= now()
            && !$this->isExpired();
    }

    /**
     * Renvoie un extrait du contenu limité à $length caractères, suivi de "..." si besoin.
     */
    public function getExcerpt(int $length = 150): string
    {
        return strlen($this->content) > $length
            ? substr($this->content, 0, $length) . '...'
            : $this->content;
    }

    /**
     * Retourne le label lisible (français) pour la priorité de l'actualité.
     */
    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'Urgent',
            'important' => 'Important',
            'normal' => 'Normal',
            default => 'Normal'
        };
    }

    /**
     * Retourne la couleur associée à la priorité (pour affichage).
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'red',
            'important' => 'orange',
            'normal' => 'blue',
            default => 'blue'
        };
    }
}
