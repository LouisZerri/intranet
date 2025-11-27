<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'description',
        'quantity',
        'unit_price',
        'tva_rate',
        'total_ht',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tva_rate' => 'decimal:2',
        'total_ht' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Relation avec le devis associé à la ligne.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Retourne le prix unitaire formaté en euros.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 2, ',', ' ') . ' €';
    }

    /**
     * Retourne le total HT formaté en euros.
     */
    public function getFormattedTotalHtAttribute(): string
    {
        return number_format($this->total_ht, 2, ',', ' ') . ' €';
    }

    /**
     * Calcule le montant de la TVA pour cette ligne.
     */
    public function getTotalTvaAttribute(): float
    {
        return $this->total_ht * ($this->tva_rate / 100);
    }

    /**
     * Calcule le montant TTC pour cette ligne.
     */
    public function getTotalTtcAttribute(): float
    {
        return $this->total_ht + $this->total_tva;
    }

    /**
     * Retourne le total TTC formaté en euros.
     */
    public function getFormattedTotalTtcAttribute(): string
    {
        return number_format($this->total_ttc, 2, ',', ' ') . ' €';
    }

    /**
     * Scope pour ordonner les lignes par sort_order puis par id.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Booted du modèle : calcule automatiquement le total HT,
     * et met à jour les totaux du devis parent après modification/suppression.
     */
    protected static function booted()
    {
        // Calculer automatiquement le total HT lors de la sauvegarde de l'item
        static::saving(function ($item) {
            $item->total_ht = $item->quantity * $item->unit_price;
        });

        // Recalculer les totaux du devis parent après création ou mise à jour de l'item
        static::saved(function ($item) {
            if ($item->quote) {
                $item->quote->calculateTotals();
                $item->quote->save();
            }
        });

        // Recalculer les totaux du devis parent après suppression de l'item
        static::deleted(function ($item) {
            if ($item->quote) {
                $item->quote->calculateTotals();
                $item->quote->save();
            }
        });
    }
}