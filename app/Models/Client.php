<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'company_name',
        'siret',
        'tva_number',
        'email',
        'phone',
        'mobile',
        'address',
        'postal_code',
        'city',
        'country',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // =====================================
    // RELATIONS
    // =====================================

    /**
     * Utilisateur propriétaire du client
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Devis du client
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    /**
     * Factures du client
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // =====================================
    // SCOPES
    // =====================================

    /**
     * Scope pour filtrer les clients selon le rôle de l'utilisateur
     * - Administrateur : voit tous les clients
     * - Manager / Collaborateur : voit uniquement SES clients
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        // Admin voit tout
        if ($user->isAdministrateur()) {
            return $query;
        }

        // Tout le monde (manager ou collaborateur) voit uniquement ses propres clients
        return $query->where('user_id', $user->id);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeParticulier(Builder $query): Builder
    {
        return $query->where('type', 'particulier');
    }

    public function scopeProfessionnel(Builder $query): Builder
    {
        return $query->where('type', 'professionnel');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('siret', 'like', "%{$search}%");
        });
    }

    // =====================================
    // ACCESSEURS
    // =====================================

    public function getFullNameAttribute(): string
    {
        if ($this->type === 'professionnel' && $this->company_name) {
            return $this->company_name;
        }
        return $this->name;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'professionnel') {
            return $this->company_name 
                ? "{$this->company_name} - {$this->name}"
                : $this->name;
        }
        return $this->name;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            trim($this->postal_code . ' ' . $this->city),
            $this->country !== 'France' ? $this->country : null,
        ]);

        return implode("\n", $parts);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'particulier' => 'Particulier',
            'professionnel' => 'Professionnel',
            default => 'Non défini'
        };
    }

    // =====================================
    // MÉTHODES UTILITAIRES
    // =====================================

    public function getTotalRevenue(): float
    {
        return $this->invoices()
                   ->where('status', 'payee')
                   ->sum('total_ht') ?? 0;
    }

    public function getQuotesCount(): int
    {
        return $this->quotes()->count();
    }

    public function getInvoicesCount(): int
    {
        return $this->invoices()->count();
    }

    public function getUnpaidInvoicesCount(): int
    {
        return $this->invoices()
                   ->whereIn('status', ['emise', 'en_retard'])
                   ->count();
    }

    public function getUnpaidAmount(): float
    {
        return $this->invoices()
                   ->whereIn('status', ['emise', 'en_retard'])
                   ->sum('total_ttc') ?? 0;
    }

    public function hasOverdueInvoices(): bool
    {
        return $this->invoices()
                   ->where('status', 'en_retard')
                   ->exists();
    }

    public function getConversionRate(): float
    {
        $totalQuotes = $this->quotes()
                           ->whereIn('status', ['envoye', 'accepte', 'refuse', 'converti'])
                           ->count();
        
        $convertedQuotes = $this->quotes()
                               ->whereIn('status', ['accepte', 'converti'])
                               ->count();

        return $totalQuotes > 0 ? round(($convertedQuotes / $totalQuotes) * 100, 2) : 0;
    }

    public function getLastInvoice(): ?Invoice
    {
        return $this->invoices()
                   ->orderBy('issued_at', 'desc')
                   ->first();
    }

    public function getLastQuote(): ?Quote
    {
        return $this->quotes()
                   ->orderBy('created_at', 'desc')
                   ->first();
    }

    public function isGoodPayer(): bool
    {
        return !$this->hasOverdueInvoices();
    }

    public function getStatistics(): array
    {
        return [
            'quotes_count' => $this->getQuotesCount(),
            'invoices_count' => $this->getInvoicesCount(),
            'total_revenue' => $this->getTotalRevenue(),
            'unpaid_invoices_count' => $this->getUnpaidInvoicesCount(),
            'unpaid_amount' => $this->getUnpaidAmount(),
            'conversion_rate' => $this->getConversionRate(),
            'has_overdue' => $this->hasOverdueInvoices(),
            'is_good_payer' => $this->isGoodPayer(),
            'last_invoice_date' => $this->getLastInvoice()?->issued_at,
            'last_quote_date' => $this->getLastQuote()?->created_at,
        ];
    }

    // =====================================
    // MÉTHODES STATIQUES
    // =====================================

    public static function getTopClients(int $limit = 10)
    {
        return static::active()
                    ->withCount('invoices')
                    ->withSum(['invoices as total_revenue' => function ($query) {
                        $query->where('status', 'payee');
                    }], 'total_ht')
                    ->having('total_revenue', '>', 0)
                    ->orderBy('total_revenue', 'desc')
                    ->take($limit)
                    ->get();
    }

    public static function getClientsWithOverdueInvoices()
    {
        return static::active()
                    ->whereHas('invoices', function ($query) {
                        $query->where('status', 'en_retard');
                    })
                    ->with(['invoices' => function ($query) {
                        $query->where('status', 'en_retard')
                              ->orderBy('due_date');
                    }])
                    ->get();
    }

    // =====================================
    // ÉVÉNEMENTS
    // =====================================

    protected static function booted()
    {
        static::creating(function ($client) {
            if (!$client->country) {
                $client->country = 'France';
            }
        });
    }
}