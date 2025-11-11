<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Quote;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::where('is_active', true)->get();
        $collaborateurs = User::whereIn('role', ['collaborateur', 'manager'])->where('is_active', true)->get();

        if ($clients->isEmpty() || $collaborateurs->isEmpty()) {
            $this->command->error('❌ Erreur : Clients ou collaborateurs manquants.');
            return;
        }

        // 1. Convertir quelques devis acceptés en factures
        $acceptedQuotes = Quote::where('status', 'accepte')->take(3)->get();
        
        foreach ($acceptedQuotes as $quote) {
            $invoice = $quote->convertToInvoice();
            
            if ($invoice) {
                // Simuler un paiement pour certaines factures
                if (rand(0, 100) > 50) {
                    $invoice->recordPayment(
                        $invoice->total_ttc,
                        ['virement', 'cheque', 'carte'][rand(0, 2)],
                        'REF-' . strtoupper(substr(md5(rand()), 0, 8))
                    );
                    $this->command->info("  💰 Facture {$invoice->invoice_number} payée");
                }
            }
        }

        // IMPORTANT : Rafraîchir pour obtenir le bon compteur de factures
        $existingInvoicesCount = Invoice::count();
        $this->command->info("  ℹ️  {$existingInvoicesCount} factures déjà créées (depuis devis)");

        // 2. Créer des factures manuelles (sans devis)
        $statusDistribution = [
            'brouillon' => 2,
            'emise' => 5,
            'payee' => 8,
            'en_retard' => 3,
        ];

        $prestations = [
            ['desc' => 'Prestation de gestion locative', 'prix' => 450.00],
            ['desc' => 'État des lieux complet', 'prix' => 280.00],
            ['desc' => 'Gestion syndic mensuelle', 'prix' => 890.00],
            ['desc' => 'Honoraires de mise en location', 'prix' => 750.00],
            ['desc' => 'Suivi de travaux', 'prix' => 380.00],
            ['desc' => 'Expertise immobilière', 'prix' => 650.00],
            ['desc' => 'Consultation juridique', 'prix' => 280.00],
        ];

        $invoiceCount = 0;

        foreach ($statusDistribution as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $client = $clients->random();
                $user = $collaborateurs->random();
                
                // Dates en fonction du statut
                $issuedAt = match($status) {
                    'brouillon' => null,
                    'emise' => Carbon::now()->subDays(rand(5, 20)),
                    'payee' => Carbon::now()->subDays(rand(15, 60)),
                    'en_retard' => Carbon::now()->subDays(rand(45, 90)),
                };

                $dueDate = $issuedAt ? $issuedAt->copy()->addDays(30) : null;
                
                $invoice = Invoice::create([
                    'invoice_number' => Invoice::generateInvoiceNumber(),
                    'quote_id' => null, // Facture manuelle sans devis
                    'client_id' => $client->id,
                    'user_id' => $user->id,
                    'status' => $status,
                    'issued_at' => $issuedAt,
                    'due_date' => $dueDate,
                    'paid_at' => $status === 'payee' ? $issuedAt->copy()->addDays(rand(10, 25)) : null,
                    'payment_terms' => 'Paiement à 30 jours fin de mois',
                    'discount_percentage' => rand(0, 100) > 80 ? rand(5, 10) : null,
                    'reminder_count' => $status === 'en_retard' ? rand(1, 3) : 0,
                    'reminder_sent_at' => $status === 'en_retard' ? Carbon::now()->subDays(rand(1, 10)) : null,
                    'internal_notes' => rand(0, 100) > 70 ? 'Note interne importante' : null,
                ]);

                // Ajouter des lignes
                $numItems = rand(1, 3);
                
                for ($j = 0; $j < $numItems; $j++) {
                    $prestation = $prestations[array_rand($prestations)];
                    
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'description' => $prestation['desc'],
                        'quantity' => rand(1, 2),
                        'unit_price' => $prestation['prix'],
                        'tva_rate' => 20.00,
                        'sort_order' => $j,
                    ]);
                }

                // Recalculer les totaux
                $invoice->refresh();
                $invoice->calculateTotals();
                $invoice->save();

                // Ajouter des paiements pour les factures payées
                if ($status === 'payee') {
                    // Parfois paiement unique, parfois plusieurs paiements partiels
                    if (rand(0, 100) > 70) {
                        // Paiements partiels
                        $remaining = $invoice->total_ttc;
                        $numPayments = rand(2, 3);
                        
                        for ($p = 0; $p < $numPayments; $p++) {
                            $isLast = ($p === $numPayments - 1);
                            $amount = $isLast ? $remaining : $remaining * (rand(30, 50) / 100);
                            
                            InvoicePayment::create([
                                'invoice_id' => $invoice->id,
                                'amount' => round($amount, 2),
                                'payment_method' => ['virement', 'cheque', 'carte'][rand(0, 2)],
                                'payment_reference' => 'REF-' . strtoupper(substr(md5(rand()), 0, 8)),
                                'payment_date' => $invoice->issued_at->copy()->addDays(rand(5, 20 + ($p * 10))),
                                'notes' => $isLast ? 'Solde final' : 'Paiement partiel',
                            ]);
                            
                            $remaining -= $amount;
                        }
                    } else {
                        // Paiement unique
                        InvoicePayment::create([
                            'invoice_id' => $invoice->id,
                            'amount' => $invoice->total_ttc,
                            'payment_method' => ['virement', 'cheque', 'carte', 'prelevement'][rand(0, 3)],
                            'payment_reference' => 'REF-' . strtoupper(substr(md5(rand()), 0, 8)),
                            'payment_date' => $invoice->issued_at->copy()->addDays(rand(10, 25)),
                            'notes' => 'Paiement intégral',
                        ]);
                    }
                }

                $invoiceCount++;
            }
        }

        $this->command->info("✅ {$invoiceCount} factures créées (+ 3 converties depuis devis)");
        
        // Statistiques
        $totalPaid = Invoice::where('status', 'payee')->sum('total_ttc');
        $totalUnpaid = Invoice::whereIn('status', ['emise', 'en_retard'])->sum('total_ttc');
        
        $this->command->info("  💰 CA total payé : " . number_format($totalPaid, 2, ',', ' ') . " €");
        $this->command->info("  ⏳ Montant impayé : " . number_format($totalUnpaid, 2, ',', ' ') . " €");
    }
}