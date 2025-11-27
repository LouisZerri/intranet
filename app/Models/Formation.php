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

    /**
     * Récupère l'utilisateur créateur de la formation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Récupère toutes les demandes de formation.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(FormationRequest::class);
    }

    /**
     * Récupère les participants ayant terminé la formation.
     */
    public function participants(): HasMany
    {
        return $this->hasMany(FormationRequest::class)->where('status', 'termine');
    }

    /**
     * Récupère tous les fichiers liés à la formation.
     */
    public function files(): HasMany
    {
        return $this->hasMany(FormationFile::class);
    }

    /**
     * Récupère les fichiers publics liés à la formation.
     */
    public function publicFiles(): HasMany
    {
        return $this->hasMany(FormationFile::class)->where('is_public', true);
    }

    /**
     * Récupère les fichiers de type document.
     */
    public function documents(): HasMany
    {
        return $this->files()->where('type', 'document');
    }

    /**
     * Récupère les fichiers de type vidéo.
     */
    public function videos(): HasMany
    {
        return $this->files()->where('type', 'video');
    }

    /**
     * Récupère les fichiers de type audio.
     */
    public function audios(): HasMany
    {
        return $this->files()->where('type', 'audio');
    }

    /**
     * Récupère les fichiers de type image.
     */
    public function images(): HasMany
    {
        return $this->files()->where('type', 'image');
    }

    /**
     * Scope : ne récupérer que les formations actives.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope : ne récupérer que les formations actives et récentes ou sans date de début.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->active()
                    ->where(function($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '>=', now()->subDays(30));
                    });
    }

    /**
     * Scope : filtrer par catégorie.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope : filtrer par niveau.
     */
    public function scopeByLevel(Builder $query, string $level): Builder
    {
        return $query->where('level', $level);
    }

    /**
     * Retourne le nombre de places encore disponibles pour cette formation.
     */
    public function getAvailablePlaces(): int
    {
        if (!$this->max_participants) {
            return 999; // Illimité
        }

        $registered = $this->requests()->whereIn('status', ['approuve', 'termine'])->count();
        return max(0, $this->max_participants - $registered);
    }

    /**
     * Vérifie si la formation est disponible.
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->getAvailablePlaces() > 0;
    }

    /**
     * Retourne le label du niveau de la formation.
     */
    public function getLevelLabelAttribute(): string
    {
        return match($this->level) {
            'debutant' => 'Débutant',
            'intermediaire' => 'Intermédiaire',
            'avance' => 'Avancé',
            default => 'Non défini'
        };
    }

    /**
     * Retourne le label du format de la formation.
     */
    public function getFormatLabelAttribute(): string
    {
        return match($this->format) {
            'presentiel' => 'Présentiel',
            'distanciel' => 'Distanciel',
            'hybride' => 'Hybride',
            default => 'Non défini'
        };
    }

    /**
     * Retourne la durée formatée de la formation.
     */
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

    /**
     * Indique si la formation possède au moins un fichier.
     */
    public function hasFiles(): bool
    {
        return $this->files()->count() > 0;
    }

    /**
     * Retourne les fichiers de la formation groupés par type.
     */
    public function getFilesByType(): array
    {
        $files = $this->files()->ordered()->get()->groupBy('type');
        
        return [
            'documents' => $files->get('document', collect()),
            'videos' => $files->get('video', collect()),
            'audios' => $files->get('audio', collect()),
            'images' => $files->get('image', collect()),
            'archives' => $files->get('archive', collect()),
            'others' => $files->get('other', collect()),
        ];
    }

    /**
     * Calcule la taille totale des fichiers liés à la formation.
     */
    public function getTotalFilesSize(): int
    {
        return $this->files()->sum('size');
    }

    /**
     * Accesseur : retourne la taille totale formatée.
     */
    public function getFormattedTotalSizeAttribute(): string
    {
        $bytes = $this->getTotalFilesSize();
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Retourne les formations les plus populaires (en fonction du nombre de participants).
     */
    public static function getPopularFormations(int $limit = 5)
    {
        return self::withCount(['requests as participants_count' => function($query) {
                        $query->whereIn('status', ['approuve', 'termine']);
                    }])
                    ->orderBy('participants_count', 'desc')
                    ->take($limit)
                    ->get();
    }

    /**
     * Retourne les statistiques par catégorie.
     */
    public static function getCategoriesStats()
    {
        return self::selectRaw('category, count(*) as formations_count, sum(duration_hours) as total_hours')
                   ->active()
                   ->whereNotNull('category')
                   ->groupBy('category')
                   ->get();
    }

    /**
     * Suppression de la formation et des fichiers associés.
     */
    public function delete(): bool
    {
        // Supprimer tous les fichiers associés
        foreach ($this->files as $file) {
            $file->delete();
        }
        
        return parent::delete();
    }
}