<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FormationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'formation_id',
        'original_name',
        'filename',
        'path',
        'mime_type',
        'size',
        'type',
        'description',
        'sort_order',
        'is_public'
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
        'is_public' => 'boolean',
    ];

    // Relations
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    // Accesseurs
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes === 0) return '0 Bytes';

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'document' => 'Document',
            'video' => 'Vidéo',
            'audio' => 'Audio',
            'image' => 'Image',
            'archive' => 'Archive',
            default => 'Fichier'
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'document' => '📄',
            'video' => '🎥',
            'audio' => '🎵',
            'image' => '🖼️',
            'archive' => '📦',
            default => '📁'
        };
    }

    public function getColorClassAttribute(): string
    {
        return match ($this->type) {
            'document' => 'bg-red-500',
            'video' => 'bg-blue-500',
            'audio' => 'bg-green-500',
            'image' => 'bg-purple-500',
            'archive' => 'bg-yellow-500',
            default => 'bg-gray-500'
        };
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('formations.files.download', $this);
    }

    // Méthodes utilitaires
    public static function determineFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        }

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (in_array($mimeType, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'text/csv'
        ])) {
            return 'document';
        }

        if (in_array($mimeType, [
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/x-tar',
            'application/gzip'
        ])) {
            return 'archive';
        }

        return 'other';
    }

    public function isViewableInBrowser(): bool
    {
        return in_array($this->type, ['image', 'document']) &&
            in_array($this->mime_type, [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/pdf'
            ]);
    }

    public function isMediaFile(): bool
    {
        return in_array($this->type, ['video', 'audio']);
    }

    public function delete(): bool
    {
        // Supprimer le fichier du stockage
        if (Storage::exists($this->path)) {
            Storage::delete($this->path);
        }

        return parent::delete();
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    /**
     * Retourne l'icône HTML du type de fichier
     */
    public function getFileTypeIcon(): string
    {
        $iconClass = 'w-8 h-8 flex items-center justify-center rounded text-white text-xs font-bold';

        switch ($this->type) {
            case 'video':
                return '<div class="' . $iconClass . ' bg-blue-500">🎥</div>';
            case 'audio':
                return '<div class="' . $iconClass . ' bg-green-500">🎵</div>';
            case 'image':
                return '<div class="' . $iconClass . ' bg-purple-500">🖼️</div>';
            case 'document':
                if (str_contains($this->mime_type, 'pdf')) {
                    return '<div class="' . $iconClass . ' bg-red-500">PDF</div>';
                } elseif (str_contains($this->mime_type, 'word') || str_contains($this->mime_type, 'document')) {
                    return '<div class="' . $iconClass . ' bg-blue-600">DOC</div>';
                } elseif (str_contains($this->mime_type, 'presentation') || str_contains($this->mime_type, 'powerpoint')) {
                    return '<div class="' . $iconClass . ' bg-orange-500">PPT</div>';
                } elseif (str_contains($this->mime_type, 'spreadsheet') || str_contains($this->mime_type, 'excel')) {
                    return '<div class="' . $iconClass . ' bg-green-600">XLS</div>';
                } else {
                    return '<div class="' . $iconClass . ' bg-gray-600">📄</div>';
                }
            case 'archive':
                return '<div class="' . $iconClass . ' bg-yellow-500">ZIP</div>';
            default:
                return '<div class="' . $iconClass . ' bg-gray-500">📄</div>';
        }
    }

    /**
     * Retourne la taille formatée du fichier
     */
    public function getFormattedSize(): string
    {
        return $this->formatted_size;
    }

    /**
     * Vérifie si l'utilisateur peut voir ce fichier dans le navigateur
     */
    public function canBeViewedInBrowser(): bool
    {
        return $this->isViewableInBrowser();
    }
}
