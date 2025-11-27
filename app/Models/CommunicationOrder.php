<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'total_amount',
        'status',
        'notes',
        'ordered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'ordered_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur ayant passé la commande
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les lignes de la commande (articles)
     */
    public function items(): HasMany
    {
        return $this->hasMany(CommunicationOrderItem::class, 'order_id');
    }

    /**
     * Évènements du modèle : génère un numéro de commande et la date si absents
     */
    protected static function booted()
    {
        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = 'COM-' . date('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', today())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
            if (!$order->ordered_at) {
                $order->ordered_at = now();
            }
        });
    }

    /**
     * Recalcule le montant total de la commande en fonction de ses articles
     */
    public function calculateTotal(): void
    {
        $this->total_amount = $this->items->sum('subtotal');
        $this->save();
    }

    /**
     * Renvoie le libellé associé au statut de la commande
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'En attente',
            'validee' => 'Validée',
            'en_preparation' => 'En préparation',
            'expediee' => 'Expédiée',
            'livree' => 'Livrée',
            'annulee' => 'Annulée',
            default => 'Inconnu'
        };
    }

    /**
     * Renvoie la couleur associée au statut de la commande
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'en_attente' => 'yellow',
            'validee' => 'blue',
            'en_preparation' => 'indigo',
            'expediee' => 'purple',
            'livree' => 'green',
            'annulee' => 'red',
            default => 'gray'
        };
    }
}