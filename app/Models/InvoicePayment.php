<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'payment_reference',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    // =====================================
    // RELATIONS
    // =====================================

    /**
     * Facture associée
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    // =====================================
    // ACCESSEURS
    // =====================================

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', ' ') . ' €';
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'carte' => 'Carte bancaire',
            'prelevement' => 'Prélèvement',
            default => 'Autre'
        };
    }

    // =====================================
    // SCOPES
    // =====================================

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year);
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }
}