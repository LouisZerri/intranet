<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    public const REVENUE_TYPE_TRANSACTION = 'transaction';
    public const REVENUE_TYPE_LOCATION = 'location';
    public const REVENUE_TYPE_SYNDIC = 'syndic';
    public const REVENUE_TYPE_AUTRES = 'autres';

    public const REVENUE_TYPES = [
        self::REVENUE_TYPE_TRANSACTION => 'Transaction',
        self::REVENUE_TYPE_LOCATION => 'Location',
        self::REVENUE_TYPE_SYNDIC => 'Syndic',
        self::REVENUE_TYPE_AUTRES => 'Autres',
    ];

    public const REVENUE_TYPE_COLORS = [
        self::REVENUE_TYPE_TRANSACTION => 'blue',
        self::REVENUE_TYPE_LOCATION => 'green',
        self::REVENUE_TYPE_SYNDIC => 'purple',
        self::REVENUE_TYPE_AUTRES => 'gray',
    ];

    protected $fillable = [
        'invoice_number',
        'quote_id',
        'client_id',
        'user_id',
        'status',
        'revenue_type',
        'total_ht',
        'total_tva',
        'total_ttc',
        'discount_amount',
        'discount_percentage',
        'payment_terms',
        'issued_at',
        'due_date',
        'paid_at',
        'cancelled_at',
        'internal_notes',
        'payment_method',
        'payment_reference',
        'reminder_sent_at',
        'reminder_count',
    ];

    protected $casts = [
        'total_ht' => 'decimal:2',
        'total_tva' => 'decimal:2',
        'total_ttc' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'issued_at' => 'datetime',
        'due_date' => 'date',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'reminder_count' => 'integer',
    ];

    /**
     * Relation avec le client de la facture
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec l'utilisateur propriétaire de la facture
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le devis relié à la facture (s'il existe)
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Liste des items de la facture
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Liste des paiements de la facture
     */
    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
    }

    // public function scopeForUser(Builder $query, User $user): Builder
    // {
    //     if ($user->isAdministrateur()) {
    //         return $query;
    //     }

    //     return $query->where('user_id', $user->id);
    // }

    /**
     * Scope : factures de l'utilisateur donné
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        // Tout le monde voit uniquement ses propres devis
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope : factures à l'état brouillon
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'brouillon');
    }

    /**
     * Scope : factures émises
     */
    public function scopeIssued(Builder $query): Builder
    {
        return $query->where('status', 'emise');
    }

    /**
     * Scope : factures payées
     */
    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'payee');
    }

    /**
     * Scope : factures en retard
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'en_retard')
            ->orWhere(function ($q) {
                $q->where('status', 'emise')
                    ->where('due_date', '<', now());
            });
    }

    /**
     * Scope : factures annulées
     */
    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'annulee');
    }

    /**
     * Scope : factures du mois en cours (par date d'émission)
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('issued_at', now()->month)
            ->whereYear('issued_at', now()->year);
    }

    /**
     * Scope : factures de l'année en cours (par date d'émission)
     */
    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereYear('issued_at', now()->year);
    }

    /**
     * Scope : factures payées ce mois-ci (par date de paiement)
     */
    public function scopePaidThisMonth(Builder $query): Builder
    {
        return $query->where('status', 'payee')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year);
    }

    /**
     * Scope : factures de type transaction
     */
    public function scopeTransaction(Builder $query): Builder
    {
        return $query->where('revenue_type', self::REVENUE_TYPE_TRANSACTION);
    }

    /**
     * Scope : factures de type location
     */
    public function scopeLocation(Builder $query): Builder
    {
        return $query->where('revenue_type', self::REVENUE_TYPE_LOCATION);
    }

    /**
     * Scope : factures de type syndic
     */
    public function scopeSyndic(Builder $query): Builder
    {
        return $query->where('revenue_type', self::REVENUE_TYPE_SYNDIC);
    }

    /**
     * Renvoie le label lisible pour le statut de la facture
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'brouillon' => 'Brouillon',
            'emise' => 'Émise',
            'payee' => 'Payée',
            'en_retard' => 'En retard',
            'annulee' => 'Annulée',
            default => 'Inconnu'
        };
    }

    /**
     * Renvoie la couleur associée au statut de la facture (pour affichage)
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'brouillon' => 'gray',
            'emise' => 'blue',
            'payee' => 'green',
            'en_retard' => 'red',
            'annulee' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Renvoie le label lisible pour le type de revenu de la facture
     */
    public function getRevenueTypeLabelAttribute(): string
    {
        return self::REVENUE_TYPES[$this->revenue_type] ?? 'Inconnu';
    }

    /**
     * Renvoie la couleur associée au type de revenu (pour affichage)
     */
    public function getRevenueTypeColorAttribute(): string
    {
        return self::REVENUE_TYPE_COLORS[$this->revenue_type] ?? 'gray';
    }

    /**
     * Renvoie le montant HT formaté en euro
     */
    public function getFormattedTotalHtAttribute(): string
    {
        return number_format($this->total_ht, 2, ',', ' ') . ' €';
    }

    /**
     * Renvoie le montant TTC formaté en euro
     */
    public function getFormattedTotalTtcAttribute(): string
    {
        return number_format($this->total_ttc, 2, ',', ' ') . ' €';
    }

    /**
     * Renvoie le montant TVA formaté en euro
     */
    public function getFormattedTotalTvaAttribute(): string
    {
        return number_format($this->total_tva, 2, ',', ' ') . ' €';
    }

    /**
     * Renvoie le montant restant dû sur la facture
     */
    public function getRemainingAmountAttribute(): float
    {
        $totalPaid = $this->payments->sum('amount');
        return max(0, $this->total_ttc - $totalPaid);
    }

    /**
     * Renvoie le montant restant dû formaté en euro
     */
    public function getFormattedRemainingAmountAttribute(): string
    {
        return number_format($this->remaining_amount, 2, ',', ' ') . ' €';
    }

    /**
     * Renvoie le nombre de jours de retard (0 si pas en retard)
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->startOfDay()->diffInDays($this->due_date->startOfDay());
    }

    /**
     * Renvoie le montant total payé sur la facture, formaté en euro
     */
    public function getFormattedPaidAmountAttribute(): string
    {
        return number_format($this->payments->sum('amount'), 2, ',', ' ') . ' €';
    }

    /**
     * Indique si la facture est en retard de paiement
     */
    public function isOverdue(): bool
    {
        return $this->status === 'emise'
            && $this->due_date
            && $this->due_date->isPast();
    }

    /**
     * Indique si la facture est totalement réglée
     */
    public function isFullyPaid(): bool
    {
        return $this->remaining_amount <= 0;
    }

    /**
     * Indique si la facture peut encore être modifiée
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['brouillon', 'emise']);
    }

    /**
     * Calcule les totaux de la facture (HT, TVA, TTC) en fonction des items et des remises
     */
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

    /**
     * Génère un numéro de facture unique, formaté
     */
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;

        $lastInvoice = static::whereYear('created_at', $year)
            ->orderByRaw('CAST(SUBSTRING(invoice_number, 10) AS UNSIGNED) DESC')
            ->first();

        if ($lastInvoice && preg_match('/FAC-' . $year . '-(\d+)/', $lastInvoice->invoice_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $invoiceNumber = 'FAC-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        while (static::where('invoice_number', $invoiceNumber)->exists()) {
            $nextNumber++;
            $invoiceNumber = 'FAC-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $invoiceNumber;
    }

    /**
     * Met la facture à l'état "émise" (si en brouillon), date d'émission et d'échéance
     */
    public function issue(): bool
    {
        if ($this->status !== 'brouillon') {
            return false;
        }

        $this->status = 'emise';
        $this->issued_at = now();

        if (!$this->due_date) {
            $this->due_date = now()->addDays(30);
        }

        return $this->save();
    }

    /**
     * Enregistre un paiement pour la facture et met à jour le statut si besoin
     */
    public function recordPayment(float $amount, string $method = 'virement', ?string $reference = null): InvoicePayment
    {
        $payment = InvoicePayment::create([
            'invoice_id' => $this->id,
            'amount' => $amount,
            'payment_method' => $method,
            'payment_reference' => $reference,
            'payment_date' => now(),
        ]);

        $this->refresh();

        if ($this->isFullyPaid()) {
            $this->status = 'payee';
            $this->paid_at = now();
            $this->save();
        }

        return $payment;
    }

    /**
     * Annule la facture, sauf si elle est payée
     */
    public function cancel(): bool
    {
        if ($this->status === 'payee') {
            return false;
        }

        $this->status = 'annulee';
        $this->cancelled_at = now();
        return $this->save();
    }

    /**
     * Marque l'envoi d'un rappel de paiement (modifie la date et incrémente le compteur)
     */
    public function sendPaymentReminder(): bool
    {
        if ($this->status !== 'emise' && $this->status !== 'en_retard') {
            return false;
        }

        $this->reminder_sent_at = now();
        $this->reminder_count = ($this->reminder_count ?? 0) + 1;
        $this->save();

        return true;
    }

    /**
     * Indique si on doit envoyer un rappel (pas trop souvent et si en retard)
     */
    public function shouldSendReminder(): bool
    {
        if (!$this->isOverdue()) {
            return false;
        }

        if ($this->reminder_sent_at && $this->reminder_sent_at->gt(now()->subDays(7))) {
            return false;
        }

        if ($this->reminder_count >= 3) {
            return false;
        }

        return true;
    }

    /**
     * Envoie des rappels automatiques pour toutes les factures en retard si nécessaire
     */
    public static function sendAutomaticReminders(): int
    {
        $count = 0;
        $overdueInvoices = static::overdue()->get();

        foreach ($overdueInvoices as $invoice) {
            if ($invoice->shouldSendReminder() && $invoice->sendPaymentReminder()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Calcule le chiffre d'affaires HT d'un utilisateur sur une période
     */
    public static function getUserRevenue(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null): float
    {
        $query = static::where('user_id', $user->id)
            ->where('status', 'payee');

        if ($startDate) {
            $query->where('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('paid_at', '<=', $endDate);
        }

        return $query->sum('total_ht') ?? 0;
    }

    /**
     * Renvoie un tableau détaillé du chiffre d'affaires pour l'URSSAF (par type, factures, etc)
     */
    public static function getURSSAFRevenue(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $invoices = static::where('user_id', $user->id)
            ->where('status', 'payee')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->with(['items', 'client'])
            ->orderBy('paid_at')
            ->get();

        // Ventilation par type de revenu
        $byType = [
            self::REVENUE_TYPE_TRANSACTION => [
                'label' => 'Transaction',
                'color' => 'blue',
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
                'invoice_count' => 0,
            ],
            self::REVENUE_TYPE_LOCATION => [
                'label' => 'Location',
                'color' => 'green',
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
                'invoice_count' => 0,
            ],
            self::REVENUE_TYPE_SYNDIC => [
                'label' => 'Syndic',
                'color' => 'purple',
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
                'invoice_count' => 0,
            ],
            self::REVENUE_TYPE_AUTRES => [
                'label' => 'Autres',
                'color' => 'gray',
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
                'invoice_count' => 0,
            ],
        ];

        // Calcul des totaux par type
        foreach ($invoices as $invoice) {
            $type = $invoice->revenue_type ?? self::REVENUE_TYPE_TRANSACTION;

            $byType[$type]['total_ht'] += $invoice->total_ht;
            $byType[$type]['total_tva'] += $invoice->total_tva;
            $byType[$type]['total_ttc'] += $invoice->total_ttc;
            $byType[$type]['invoice_count']++;
        }

        // Filtrer les types vides (optionnel, on peut garder tous les types)
        $byTypeFiltered = array_filter($byType, fn($data) => $data['invoice_count'] > 0);

        return [
            'period_start' => $startDate->format('d/m/Y'),
            'period_end' => $endDate->format('d/m/Y'),
            'user_name' => $user->full_name,
            'user_email' => $user->email,
            'user_phone' => $user->phone ?? 'Non renseigné',
            'user_siret' => $user->siret ?? 'Non renseigné',

            // Totaux globaux
            'total_ht' => $invoices->sum('total_ht'),
            'total_tva' => $invoices->sum('total_tva'),
            'total_ttc' => $invoices->sum('total_ttc'),
            'invoice_count' => $invoices->count(),

            // Ventilation par type de CA
            'by_type' => $byType,
            'by_type_filtered' => $byTypeFiltered,

            // Totaux par type (raccourcis)
            'transaction' => $byType[self::REVENUE_TYPE_TRANSACTION],
            'location' => $byType[self::REVENUE_TYPE_LOCATION],
            'syndic' => $byType[self::REVENUE_TYPE_SYNDIC],
            'autres' => $byType[self::REVENUE_TYPE_AUTRES],

            // Détail des factures avec type
            'invoices' => $invoices->map(function ($invoice) {
                return [
                    'invoice_number' => $invoice->invoice_number,
                    'client_name' => $invoice->client->name ?? 'N/A',
                    'paid_at' => $invoice->paid_at->format('d/m/Y'),
                    'revenue_type' => $invoice->revenue_type ?? 'transaction',
                    'revenue_type_label' => $invoice->revenue_type_label,
                    'total_ht' => $invoice->total_ht,
                    'total_tva' => $invoice->total_tva,
                    'total_ttc' => $invoice->total_ttc,
                ];
            }),
        ];
    }

    /**
     * Hook de boot : assignation des champs automatiques à la création/mise à jour
     */
    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }

            if (!$invoice->revenue_type) {
                $invoice->revenue_type = self::REVENUE_TYPE_TRANSACTION;
            }

            if (!$invoice->issued_at && $invoice->status !== 'brouillon') {
                $invoice->issued_at = now();
            }

            if (!$invoice->due_date && $invoice->status !== 'brouillon') {
                $invoice->due_date = now()->addDays(30);
            }
        });

        static::updating(function ($invoice) {
            if ($invoice->status === 'emise' && $invoice->due_date && $invoice->due_date->isPast()) {
                $invoice->status = 'en_retard';
            }
        });
    }
}
