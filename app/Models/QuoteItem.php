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

    // =====================================
    // RELATIONS
    // =====================================

    /**
     * Devis associé
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    // =====================================
    // ACCESSEURS
    // =====================================

    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 2, ',', ' ') . ' €';
    }

    public function getFormattedTotalHtAttribute(): string
    {
        return number_format($this->total_ht, 2, ',', ' ') . ' €';
    }

    public function getTotalTvaAttribute(): float
    {
        return $this->total_ht * ($this->tva_rate / 100);
    }

    public function getTotalTtcAttribute(): float
    {
        return $this->total_ht + $this->total_tva;
    }

    public function getFormattedTotalTtcAttribute(): string
    {
        return number_format($this->total_ttc, 2, ',', ' ') . ' €';
    }

    // =====================================
    // SCOPES
    // =====================================

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // =====================================
    // ÉVÉNEMENTS
    // =====================================

    protected static function booted()
    {
        // Calculer automatiquement le total HT
        static::saving(function ($item) {
            $item->total_ht = $item->quantity * $item->unit_price;
        });

        // Recalculer les totaux du devis parent après chaque modification
        static::saved(function ($item) {
            if ($item->quote) {
                $item->quote->calculateTotals();
                $item->quote->save();
            }
        });

        static::deleted(function ($item) {
            if ($item->quote) {
                $item->quote->calculateTotals();
                $item->quote->save();
            }
        });
    }
}