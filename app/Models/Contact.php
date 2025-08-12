<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'company',
        'sector',
        'email',
        'phone',
        'mobile',
        'address',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope pour les contacts actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour filtrer par secteur
     */
    public function scopeBySector($query, $sector)
    {
        return $query->where('sector', $sector);
    }

    /**
     * Accessor pour le nom complet avec position
     */
    public function getFullInfoAttribute()
    {
        $info = $this->name;
        if ($this->position) {
            $info .= ' - ' . $this->position;
        }
        if ($this->company) {
            $info .= ' (' . $this->company . ')';
        }
        return $info;
    }

    /**
     * Accessor pour formater l'adresse
     */
    public function getFormattedAddressAttribute()
    {
        return $this->address ? nl2br(e($this->address)) : null;
    }
}