<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
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
     * Récupère la facture associée à l'item.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Renvoie le prix unitaire formaté en euro.
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return number_format($this->unit_price, 2, ',', ' ') . ' €';
    }

    /**
     * Renvoie le total HT formaté en euro.
     */
    public function getFormattedTotalHtAttribute(): string
    {
        return number_format($this->total_ht, 2, ',', ' ') . ' €';
    }

    /**
     * Calcule la TVA totale sur cet item.
     */
    public function getTotalTvaAttribute(): float
    {
        return $this->total_ht * ($this->tva_rate / 100);
    }

    /**
     * Calcule le total TTC de l'item.
     */
    public function getTotalTtcAttribute(): float
    {
        return $this->total_ht + $this->total_tva;
    }

    /**
     * Renvoie le total TTC formaté en euro.
     */
    public function getFormattedTotalTtcAttribute(): string
    {
        return number_format($this->total_ttc, 2, ',', ' ') . ' €';
    }

    /**
     * Scope pour ordonner les items selon sort_order puis id.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Méthodes d'events pour calculer automatiquement les totaux et maintenir la cohérence avec la facture.
     */
    protected static function booted()
    {
        // Calculer automatiquement le total HT
        static::saving(function ($item) {
            $item->total_ht = $item->quantity * $item->unit_price;
        });

        // Recalculer les totaux de la facture parent après chaque modification
        static::saved(function ($item) {
            if ($item->invoice) {
                $item->invoice->calculateTotals();
                $item->invoice->save();
            }
        });

        static::deleted(function ($item) {
            if ($item->invoice) {
                $item->invoice->calculateTotals();
                $item->invoice->save();
            }
        });
    }
}