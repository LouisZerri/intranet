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

    // Relations
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

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

    public function scopeByPriority(Builder $query, string $priority = null): Builder
    {
        if ($priority) {
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

    // Méthodes utilitaires
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isPublished(): bool
    {
        return $this->status === 'published'
            && $this->published_at <= now()
            && !$this->isExpired();
    }

    public function getExcerpt(int $length = 150): string
    {
        return strlen($this->content) > $length
            ? substr($this->content, 0, $length) . '...'
            : $this->content;
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'Urgent',
            'important' => 'Important',
            'normal' => 'Normal',
            default => 'Normal'
        };
    }

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
