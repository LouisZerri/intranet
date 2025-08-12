<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'file_path',
        'original_filename',
        'file_size',
        'mime_type',
        'download_count',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'file_size' => 'integer',
        'download_count' => 'integer',
    ];

    /**
     * Scope pour les ressources actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Accessor pour la taille formatée du fichier
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return 'N/A';

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Accessor pour l'icône du fichier basée sur le type MIME
     */
    public function getFileIconAttribute()
    {
        if (!$this->mime_type) return '📄';

        $iconMap = [
            'application/pdf' => '📑',
            'application/msword' => '📝',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '📝',
            'application/vnd.ms-excel' => '📊',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '📊',
            'application/vnd.ms-powerpoint' => '📋',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '📋',
            'image/jpeg' => '🖼️',
            'image/png' => '🖼️',
            'image/gif' => '🖼️',
            'application/zip' => '🗜️',
            'application/x-rar-compressed' => '🗜️',
        ];

        return $iconMap[$this->mime_type] ?? '📄';
    }

    /**
     * Accessor pour le type de fichier lisible
     */
    public function getFileTypeAttribute()
    {
        if (!$this->mime_type) return 'Fichier';

        $typeMap = [
            'application/pdf' => 'PDF',
            'application/msword' => 'Word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
            'application/vnd.ms-excel' => 'Excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel',
            'application/vnd.ms-powerpoint' => 'PowerPoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint',
            'image/jpeg' => 'Image',
            'image/png' => 'Image',
            'image/gif' => 'Image',
            'application/zip' => 'Archive',
            'application/x-rar-compressed' => 'Archive',
        ];

        return $typeMap[$this->mime_type] ?? 'Fichier';
    }
}