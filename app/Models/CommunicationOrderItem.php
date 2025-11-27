<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Récupère la commande associée à cet item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(CommunicationOrder::class, 'order_id');
    }

    /**
     * Récupère le produit associé à cet item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(CommunicationProduct::class, 'product_id');
    }

    /**
     * Méthode appelée lors du boot du modèle pour définir les événements.
     */
    protected static function booted()
    {
        static::creating(function ($item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });

        static::updating(function ($item) {
            $item->subtotal = $item->quantity * $item->unit_price;
        });
    }
}