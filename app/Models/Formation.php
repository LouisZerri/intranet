<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'level',
        'duration_hours',
        'cost',
        'provider',
        'format',
        'max_participants',
        'is_active',
        'start_date',
        'end_date',
        'prerequisites',
        'objectives',
        'location',
        'created_by'
    ];

    protected $casts = [
        'prerequisites' => 'array',
        'objectives' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'cost' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relations
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(FormationRequest::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(FormationRequest::class)->where('status', 'termine');
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->active()
                    ->where(function($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '>=', now()->subDays(30));
                    });
    }

    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeByLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    // Méthodes utilitaires
    public function getAvailablePlaces(): int
    {
        if (!$this->max_participants) {
            return 999; // Illimité
        }

        $registered = $this->requests()->whereIn('status', ['approuve', 'termine'])->count();
        return max(0, $this->max_participants - $registered);
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->getAvailablePlaces() > 0;
    }

    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'debutant' => 'Débutant',
            'intermediaire' => 'Intermédiaire',
            'avance' => 'Avancé',
            default => 'Non défini'
        };
    }

    public function getFormatLabelAttribute(): string
    {
        return match($this->format) {
            'presentiel' => 'Présentiel',
            'distanciel' => 'Distanciel',
            'hybride' => 'Hybride',
            default => 'Non défini'
        };
    }

    public function getDurationLabelAttribute(): string
    {
        if ($this->duration_hours == 0) return 'Durée non définie';
        if ($this->duration_hours < 8) return $this->duration_hours . 'h';
        
        $days = floor($this->duration_hours / 8);
        $hours = $this->duration_hours % 8;
        
        $label = $days . ' jour' . ($days > 1 ? 's' : '');
        if ($hours > 0) $label .= ' et ' . $hours . 'h';
        
        return $label;
    }

    // Méthodes pour les KPI
    public static function getPopularFormations(int $limit = 5)
    {
        return self::withCount(['requests as participants_count' => function($query) {
                        $query->whereIn('status', ['approuve', 'termine']);
                    }])
                    ->orderBy('participants_count', 'desc')
                    ->take($limit)
                    ->get();
    }

    public static function getCategoriesStats()
    {
        return self::selectRaw('category, count(*) as formations_count, sum(duration_hours) as total_hours')
                   ->active()
                   ->whereNotNull('category')
                   ->groupBy('category')
                   ->get();
    }
}