<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PredefinedService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'default_price',
        'default_tva_rate',
        'unit',
        'default_quantity',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
        'default_tva_rate' => 'decimal:2',
        'default_quantity' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope: Sélectionne uniquement les prestations actives.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filtre les prestations par catégorie.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope: Permet d'ordonner les services selon sort_order puis par nom.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Renvoie le prix formaté en euro (ex: 15,00 €).
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->default_price, 2, ',', ' ') . ' €';
    }

    /**
     * Retourne le label lisible (français) de la catégorie.
     */
    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'location' => 'Location',
            'etat_lieux_entree' => 'État des lieux d\'entrée',
            'etat_lieux_sortie' => 'État des lieux de sortie',
            'gestion' => 'Gestion locative',
            'syndic' => 'Syndic de copropriété',
            'transaction' => 'Transaction immobilière',
            'expertise' => 'Expertise immobilière',
            'consultation' => 'Consultation',
            'autres' => 'Autres prestations',
            default => 'Non défini'
        };
    }

    /**
     * Renvoie la liste des catégories disponibles pour les prestations prédéfinies.
     */
    public static function getCategories(): array
    {
        return [
            'location' => 'Location',
            'etat_lieux_entree' => 'État des lieux d\'entrée',
            'etat_lieux_sortie' => 'État des lieux de sortie',
            'gestion' => 'Gestion locative',
            'syndic' => 'Syndic de copropriété',
            'transaction' => 'Transaction immobilière',
            'expertise' => 'Expertise immobilière',
            'consultation' => 'Consultation',
            'autres' => 'Autres prestations',
        ];
    }

    /**
     * Renvoie les unités disponibles pour les prestations prédéfinies.
     */
    public static function getUnits(): array
    {
        return [
            'unité' => 'Unité',
            'forfait' => 'Forfait',
            'heure' => 'Heure',
            'jour' => 'Jour',
            'mois' => 'Mois',
            'm²' => 'Mètre carré (m²)',
            'lot' => 'Lot',
        ];
    }
}