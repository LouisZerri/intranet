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

    protected $fillable = [
        'invoice_number',
        'quote_id',
        'client_id',
        'user_id', // Mandataire/Collaborateur émetteur
        'status',
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
        'reminder_sent_at', // Date du dernier rappel envoyé
        'reminder_count', // Nombre de rappels envoyés
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

    // =====================================
    // RELATIONS
    // =====================================

    /**
     * Client associé à la facture
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Utilisateur (mandataire) créateur de la facture
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Devis d'origine (si converti)
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Lignes de la facture
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Paiements associés à la facture
     */
    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class);
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

    public function scopeIssued(Builder $query): Builder
    {
        return $query->where('status', 'emise');
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', 'payee');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'en_retard')
            ->orWhere(function ($q) {
                $q->where('status', 'emise')
                    ->where('due_date', '<', now());
            });
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'annulee');
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('issued_at', now()->month)
            ->whereYear('issued_at', now()->year);
    }

    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereYear('issued_at', now()->year);
    }

    public function scopePaidThisMonth(Builder $query): Builder
    {
        return $query->where('status', 'payee')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year);
    }

    // =====================================
    // ACCESSEURS
    // =====================================

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

    public function getFormattedTotalHtAttribute(): string
    {
        return number_format($this->total_ht, 2, ',', ' ') . ' €';
    }

    public function getFormattedTotalTtcAttribute(): string
    {
        return number_format($this->total_ttc, 2, ',', ' ') . ' €';
    }

    public function getRemainingAmountAttribute(): float
    {
        $totalPaid = $this->payments->sum('amount');
        return max(0, $this->total_ttc - $totalPaid);
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return number_format($this->remaining_amount, 2, ',', ' ') . ' €';
    }

    public function getDaysOverdueAttribute(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->startOfDay()->diffInDays($this->due_date->startOfDay());
    }

    // =====================================
    // MÉTHODES UTILITAIRES
    // =====================================

    /**
     * Vérifier si la facture est en retard
     */
    public function isOverdue(): bool
    {
        return $this->status === 'emise'
            && $this->due_date
            && $this->due_date->isPast();
    }

    /**
     * Vérifier si la facture est totalement payée
     */
    public function isFullyPaid(): bool
    {
        return $this->remaining_amount <= 0;
    }

    /**
     * Vérifier si la facture peut être modifiée
     */
    public function canBeEdited(): bool
    {
        return in_array($this->status, ['brouillon', 'emise']);
    }

    /**
     * Calculer les totaux de la facture
     */
    public function calculateTotals(): void
    {
        $subtotal = $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        // Application de la remise
        if ($this->discount_percentage > 0) {
            $this->discount_amount = $subtotal * ($this->discount_percentage / 100);
        }

        $this->total_ht = $subtotal - ($this->discount_amount ?? 0);

        // Calcul TVA
        $this->total_tva = $this->items->sum(function ($item) {
            $itemTotal = $item->quantity * $item->unit_price;
            return $itemTotal * ($item->tva_rate / 100);
        });

        // Application de la remise proportionnelle sur la TVA
        if ($this->discount_amount > 0 && $subtotal > 0) {
            $discountRatio = 1 - ($this->discount_amount / $subtotal);
            $this->total_tva *= $discountRatio;
        }

        $this->total_ttc = $this->total_ht + $this->total_tva;
    }

    /**
     * Générer le numéro de facture automatique
     */
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;

        // Récupérer le dernier numéro de l'année en cours
        $lastInvoice = static::whereYear('created_at', $year)
            ->orderByRaw('CAST(SUBSTRING(invoice_number, 10) AS UNSIGNED) DESC')
            ->first();

        if ($lastInvoice && preg_match('/FAC-' . $year . '-(\d+)/', $lastInvoice->invoice_number, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $invoiceNumber = 'FAC-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Vérification de sécurité : si le numéro existe déjà, incrémenter
        while (static::where('invoice_number', $invoiceNumber)->exists()) {
            $nextNumber++;
            $invoiceNumber = 'FAC-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        return $invoiceNumber;
    }

    /**
     * Émettre la facture
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
     * Enregistrer un paiement
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

        // Vérifier si la facture est totalement payée
        if ($this->isFullyPaid()) {
            $this->status = 'payee';
            $this->paid_at = now();
            $this->save();
        }

        return $payment;
    }

    /**
     * Annuler la facture
     */
    public function cancel(): bool
    {
        if ($this->status === 'payee') {
            return false; // Ne peut pas annuler une facture déjà payée
        }

        $this->status = 'annulee';
        $this->cancelled_at = now();
        return $this->save();
    }

    /**
     * Envoyer un rappel de paiement
     */
    public function sendPaymentReminder(): bool
    {
        if ($this->status !== 'emise' && $this->status !== 'en_retard') {
            return false;
        }

        // Logique d'envoi d'email à implémenter
        // Notification::send($this->client, new InvoicePaymentReminderNotification($this));

        $this->reminder_sent_at = now();
        $this->reminder_count = ($this->reminder_count ?? 0) + 1;
        $this->save();

        return true;
    }

    /**
     * Vérifier si un rappel doit être envoyé (logique automatique)
     */
    public function shouldSendReminder(): bool
    {
        if (!$this->isOverdue()) {
            return false;
        }

        // Pas de rappel si déjà envoyé dans les 7 derniers jours
        if ($this->reminder_sent_at && $this->reminder_sent_at->gt(now()->subDays(7))) {
            return false;
        }

        // Maximum 3 rappels
        if ($this->reminder_count >= 3) {
            return false;
        }

        return true;
    }

    /**
     * Envoyer les rappels automatiques pour toutes les factures en retard
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
     * Obtenir le CA d'un utilisateur pour une période
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

    public function getFormattedTotalTvaAttribute(): string
    {
        return number_format($this->total_tva, 2, ',', ' ') . ' €';
    }

    /**
     * Obtenir le CA collecté pour l'URSSAF (pour un mandataire)
     */
    public static function getURSSAFRevenue(User $user, Carbon $startDate, Carbon $endDate): array
    {
        $invoices = static::where('user_id', $user->id)
            ->where('status', 'payee')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->with('items')
            ->get();

        return [
            'period_start' => $startDate->format('d/m/Y'),
            'period_end' => $endDate->format('d/m/Y'),
            'user_name' => $user->full_name,
            'total_ht' => $invoices->sum('total_ht'),
            'total_tva' => $invoices->sum('total_tva'),
            'total_ttc' => $invoices->sum('total_ttc'),
            'invoice_count' => $invoices->count(),
            'invoices' => $invoices->map(function ($invoice) {
                return [
                    'number' => $invoice->invoice_number,
                    'date' => $invoice->paid_at->format('d/m/Y'),
                    'client' => $invoice->client->name,
                    'total_ht' => $invoice->total_ht,
                    'total_ttc' => $invoice->total_ttc,
                ];
            }),
        ];
    }

    // =====================================
    // ÉVÉNEMENTS
    // =====================================

    protected static function booted()
    {
        static::creating(function ($invoice) {
            if (!$invoice->invoice_number) {
                $invoice->invoice_number = static::generateInvoiceNumber();
            }

            if (!$invoice->issued_at && $invoice->status !== 'brouillon') {
                $invoice->issued_at = now();
            }

            if (!$invoice->due_date && $invoice->status !== 'brouillon') {
                $invoice->due_date = now()->addDays(30);
            }
        });

        static::updating(function ($invoice) {
            // Marquer automatiquement comme en retard si nécessaire
            if ($invoice->status === 'emise' && $invoice->due_date && $invoice->due_date->isPast()) {
                $invoice->status = 'en_retard';
            }
        });
    }
}
