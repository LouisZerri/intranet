<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_number',
        'client_id',
        'user_id',
        'status',
        'total_ht',
        'total_tva',
        'total_ttc',
        'discount_amount',
        'discount_percentage',
        'validity_date',
        'accepted_at',
        'refused_at',
        'converted_at',
        'internal_notes',
        'client_notes',
        'payment_terms',
        'delivery_terms',
        'signed_electronically',
        'signature_date',
    ];

    protected $casts = [
        'total_ht' => 'decimal:2',
        'total_tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'validity_date' => 'date',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'refused_at' => 'datetime',
        'converted_at' => 'datetime',
        'signed_electronically' => 'boolean',
        'signature_date' => 'datetime',
    ];

    // =====================================
    // RELATIONS
    // =====================================

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function mission(): HasOne
    {
        return $this->hasOne(Mission::class);
    }

    // =====================================
    // SCOPES
    // =====================================

    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdministrateur()) {
            return $query;
        }

        if ($user->isManager()) {
            return $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('user', function ($subQ) use ($user) {
                      $subQ->where('manager_id', $user->id);
                  });
            });
        }

        return $query->where('user_id', $user->id);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'brouillon');
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', 'envoye');
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('status', 'accepte');
    }

    public function scopeRefused(Builder $query): Builder
    {
        return $query->where('status', 'refuse');
    }

    public function scopeConverted(Builder $query): Builder
    {
        return $query->where('status', 'converti');
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('status', 'envoye')
                    ->where('validity_date', '<', now());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereYear('created_at', now()->year);
    }

    // =====================================
    // ACCESSEURS
    // =====================================

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'brouillon' => 'Brouillon',
            'envoye' => 'Envoyé',
            'accepte' => 'Accepté',
            'refuse' => 'Refusé',
            'converti' => 'Converti',
            default => 'Inconnu'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'brouillon' => 'gray',
            'envoye' => 'blue',
            'accepte' => 'green',
            'refuse' => 'red',
            'converti' => 'purple',
            default => 'gray'
        };
    }

    public function getFormattedTotalHtAttribute(): string
    {
        return number_format($this->total_ht, 2, ',', ' ') . ' €';
    }

    public function getFormattedTotalTvaAttribute(): string
    {
        return number_format($this->total_tva, 2, ',', ' ') . ' €';
    }

    public function getFormattedTotalTtcAttribute(): string
    {
        return number_format($this->total_ttc, 2, ',', ' ') . ' €';
    }

    // =====================================
    // MÉTHODES UTILITAIRES
    // =====================================

    public function isExpired(): bool
    {
        return $this->status === 'envoye' 
            && $this->validity_date 
            && $this->validity_date->isPast();
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['brouillon', 'envoye']);
    }

    public function canBeConverted(): bool
    {
        return $this->status === 'accepte' && !$this->invoice;
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        if ($this->discount_percentage > 0) {
            $this->discount_amount = $subtotal * ($this->discount_percentage / 100);
        }

        $this->total_ht = $subtotal - ($this->discount_amount ?? 0);

        $this->total_tva = $this->items->sum(function ($item) {
            $itemTotal = $item->quantity * $item->unit_price;
            return $itemTotal * ($item->tva_rate / 100);
        });

        if ($this->discount_amount > 0 && $subtotal > 0) {
            $discountRatio = 1 - ($this->discount_amount / $subtotal);
            $this->total_tva *= $discountRatio;
        }

        $this->total_ttc = $this->total_ht + $this->total_tva;
    }

    public static function generateQuoteNumber(): string
    {
        $year = now()->year;
        
        $lastQuote = static::whereYear('created_at', $year)
            ->orderByRaw('CAST(SUBSTRING(quote_number, 9) AS UNSIGNED) DESC')
            ->first();

        if ($lastQuote && preg_match('/DV-' . $year . '-(\d+)/', $lastQuote->quote_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $quoteNumber = 'DV-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        while (static::where('quote_number', $quoteNumber)->exists()) {
            $nextNumber++;
            $quoteNumber = 'DV-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $quoteNumber;
    }

    public function send(): bool
    {
        if ($this->status !== 'brouillon') {
            return false;
        }

        $this->status = 'envoye';
        
        if (!$this->validity_date) {
            $this->validity_date = now()->addDays(30);
        }

        return $this->save();
    }

    public function accept(): bool
    {
        if ($this->status !== 'envoye') {
            return false;
        }

        $this->status = 'accepte';
        $this->accepted_at = now();
        $saved = $this->save();

        if ($saved) {
            $this->createMission();
        }

        return $saved;
    }

    public function refuse(): bool
    {
        if ($this->status !== 'envoye') {
            return false;
        }

        $this->status = 'refuse';
        $this->refused_at = now();
        return $this->save();
    }

    public function convertToInvoice(): ?Invoice
    {
        if (!$this->canBeConverted()) {
            return null;
        }

        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'quote_id' => $this->id,
            'client_id' => $this->client_id,
            'user_id' => $this->user_id,
            'status' => 'emise',
            'total_ht' => $this->total_ht,
            'total_tva' => $this->total_tva,
            'total_ttc' => $this->total_ttc,
            'discount_amount' => $this->discount_amount,
            'discount_percentage' => $this->discount_percentage,
            'payment_terms' => $this->payment_terms,
            'due_date' => now()->addDays(30),
            'issued_at' => now(),
        ]);

        foreach ($this->items as $quoteItem) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $quoteItem->description,
                'quantity' => $quoteItem->quantity,
                'unit_price' => $quoteItem->unit_price,
                'tva_rate' => $quoteItem->tva_rate,
                'total_ht' => $quoteItem->total_ht,
            ]);
        }

        $this->status = 'converti';
        $this->converted_at = now();
        $this->save();

        if ($this->mission) {
            $this->mission->update([
                'notes' => ($this->mission->notes ?? '') . "\nFacture générée : " . $invoice->invoice_number,
            ]);
        }

        return $invoice;
    }

    protected function createMission(): Mission
    {
        $title = 'Mission - ' . $this->client->name;

        $mission = Mission::create([
            'quote_id' => $this->id,
            'title' => $title,
            'description' => "Mission créée automatiquement depuis le devis {$this->quote_number}.\n\n" .
                           "Client : {$this->client->name}\n" .
                           "Montant : {$this->formatted_total_ttc}",
            'status' => 'en_attente',
            'priority' => 'normale',
            'category' => 'autres',
            'subcategory' => 'projet_special',
            'assigned_to' => $this->user_id,
            'created_by' => $this->user_id,
            'manager_id' => $this->user->manager_id,
            'revenue' => $this->total_ht,
            'start_date' => now(),
            'due_date' => now()->addDays(30),
            'notes' => "Lien devis : {$this->quote_number}",
        ]);

        return $mission;
    }

    public static function getConversionRate(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = static::where('user_id', $user->id);

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $totalSent = $query->whereIn('status', ['envoye', 'accepte', 'refuse', 'converti'])->count();
        $totalAccepted = (clone $query)->whereIn('status', ['accepte', 'converti'])->count();

        return $totalSent > 0 ? round(($totalAccepted / $totalSent) * 100, 2) : 0;
    }

    // =====================================
    // ÉVÉNEMENTS
    // =====================================

    protected static function booted()
    {
        static::creating(function ($quote) {
            if (!$quote->quote_number) {
                $quote->quote_number = static::generateQuoteNumber();
            }

            if (!$quote->validity_date && $quote->status === 'envoye') {
                $quote->validity_date = now()->addDays(30);
            }
        });
    }
}