<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CommunicationProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'reference',
        'is_active',
        'stock_quantity',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'stock_quantity' => 'integer',
    ];

    /**
     * Relation vers les items (lignes de commande) contenant ce produit
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(CommunicationOrderItem::class, 'product_id');
    }

    /**
     * Scope pour ne récupérer que les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour ne récupérer que les produits en stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    /**
     * Vérifie si le produit est disponible (actif et stock > 0)
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->stock_quantity > 0;
    }

    /**
     * Attribut accessoire : URL de l'image du produit (ou placeholder)
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholder-product.png');
    }

    /**
     * Attribut accessoire : prix formaté en euros
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->price) {
            return number_format($this->price, 2, ',', ' ') . ' €';
        }
        return 'Prix sur demande';
    }
}