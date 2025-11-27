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
     * Prestations avec leur type d'activité associé
     */
    private array $prestations = [
        // Transaction
        ['desc' => 'Honoraires de négociation vente', 'prix' => 3500.00, 'type' => 'transaction'],
        ['desc' => 'Commission vente immobilière', 'prix' => 8500.00, 'type' => 'transaction'],
        ['desc' => 'Mandat de vente exclusif', 'prix' => 2500.00, 'type' => 'transaction'],
        ['desc' => 'Honoraires de transaction', 'prix' => 5000.00, 'type' => 'transaction'],
        
        // Location
        ['desc' => 'État des lieux d\'entrée', 'prix' => 150.00, 'type' => 'location'],
        ['desc' => 'État des lieux de sortie', 'prix' => 150.00, 'type' => 'location'],
        ['desc' => 'État des lieux complet (entrée + sortie)', 'prix' => 280.00, 'type' => 'location'],
        ['desc' => 'Prestation de gestion locative mensuelle', 'prix' => 450.00, 'type' => 'location'],
        ['desc' => 'Honoraires de mise en location', 'prix' => 750.00, 'type' => 'location'],
        ['desc' => 'Rédaction bail d\'habitation', 'prix' => 350.00, 'type' => 'location'],
        ['desc' => 'Gestion locative trimestrielle', 'prix' => 890.00, 'type' => 'location'],
        
        // Syndic
        ['desc' => 'Gestion syndic mensuelle', 'prix' => 890.00, 'type' => 'syndic'],
        ['desc' => 'Honoraires syndic annuels', 'prix' => 2400.00, 'type' => 'syndic'],
        ['desc' => 'Organisation AG copropriété', 'prix' => 450.00, 'type' => 'syndic'],
        ['desc' => 'Suivi travaux copropriété', 'prix' => 680.00, 'type' => 'syndic'],
        
        // Autres
        ['desc' => 'Expertise immobilière', 'prix' => 650.00, 'type' => 'autres'],
        ['desc' => 'Consultation juridique', 'prix' => 280.00, 'type' => 'autres'],
        ['desc' => 'Suivi de travaux', 'prix' => 380.00, 'type' => 'autres'],
        ['desc' => 'Accompagnement administratif', 'prix' => 200.00, 'type' => 'autres'],
        ['desc' => 'Conseil en investissement', 'prix' => 500.00, 'type' => 'autres'],
    ];

    /**
     * Distribution pondérée des types d'activité (pour factures aléatoires)
     * Transaction: 35%, Location: 40%, Syndic: 15%, Autres: 10%
     */
    private function getRandomRevenueType(): string
    {
        $rand = rand(1, 100);
        
        if ($rand <= 35) {
            return 'transaction';
        } elseif ($rand <= 75) {
            return 'location';
        } elseif ($rand <= 90) {
            return 'syndic';
        } else {
            return 'autres';
        }
    }

    /**
     * Récupérer des prestations par type
     */
    private function getPrestationsByType(string $type): array
    {
        return array_filter($this->prestations, fn($p) => $p['type'] === $type);
    }

    /**
     * Récupérer une prestation aléatoire d'un type donné
     */
    private function getRandomPrestationByType(string $type): array
    {
        $filtered = $this->getPrestationsByType($type);
        
        if (empty($filtered)) {
            // Fallback
            return $this->prestations[array_rand($this->prestations)];
        }
        
        return $filtered[array_rand($filtered)];
    }

    public function run(): void
    {
        // 1. Convertir quelques devis acceptés en factures
        $acceptedQuotes = Quote::where('status', 'accepte')->take(5)->get();
        $convertedCount = 0;
        
        foreach ($acceptedQuotes as $quote) {
            $invoice = $quote->convertToInvoice();
            
            if ($invoice) {
                // Assigner un type d'activité basé sur le service du devis
                $revenueType = $this->guessRevenueTypeFromDescription($quote->service_type ?? '');
                $invoice->revenue_type = $revenueType;
                $invoice->save();
                
                $convertedCount++;
                
                // Simuler un paiement pour certaines factures
                if (rand(0, 100) > 50) {
                    $invoice->recordPayment(
                        $invoice->total_ttc,
                        ['virement', 'cheque', 'carte'][rand(0, 2)],
                        'REF-' . strtoupper(substr(md5(rand()), 0, 8))
                    );
                }
            }
        }
        
        $this->command->info("  ✓ {$convertedCount} factures créées depuis devis acceptés");

        // 2. Créer des factures manuelles pour chaque utilisateur
        $users = User::whereIn('role', ['collaborateur', 'manager'])
            ->where('is_active', true)
            ->get();

        $invoiceCount = 0;
        $typeStats = [
            'transaction' => 0,
            'location' => 0,
            'syndic' => 0,
            'autres' => 0,
        ];

        foreach ($users as $user) {
            // Récupérer les clients de CET utilisateur
            $userClients = Client::where('user_id', $user->id)->where('is_active', true)->get();
            
            if ($userClients->isEmpty()) {
                $this->command->warn("  ⚠️ {$user->full_name} n'a pas de clients, skip...");
                continue;
            }

            // Distribution des statuts par utilisateur
            $statusDistribution = [
                'brouillon' => 1,
                'emise' => 2,
                'payee' => 4, // Plus de factures payées pour les tests URSSAF
                'en_retard' => 1,
            ];

            foreach ($statusDistribution as $status => $count) {
                for ($i = 0; $i < $count; $i++) {
                    // Choisir un client de cet utilisateur
                    $client = $userClients->random();
                    
                    // Déterminer le type d'activité pour cette facture
                    $revenueType = $this->getRandomRevenueType();
                    $typeStats[$revenueType]++;
                    
                    $issuedAt = match($status) {
                        'brouillon' => null,
                        'emise' => Carbon::now()->subDays(rand(5, 20)),
                        'payee' => Carbon::now()->subDays(rand(15, 90)), // Élargi pour avoir des données sur plusieurs mois
                        'en_retard' => Carbon::now()->subDays(rand(45, 90)),
                    };

                    $dueDate = $issuedAt ? $issuedAt->copy()->addDays(30) : null;
                    
                    // Date de paiement variée pour les factures payées
                    $paidAt = null;
                    if ($status === 'payee' && $issuedAt) {
                        $paidAt = $issuedAt->copy()->addDays(rand(5, 25));
                    }
                    
                    $invoice = Invoice::create([
                        'invoice_number' => Invoice::generateInvoiceNumber(),
                        'quote_id' => null,
                        'client_id' => $client->id,
                        'user_id' => $user->id,
                        'status' => $status,
                        'revenue_type' => $revenueType,
                        'issued_at' => $issuedAt,
                        'due_date' => $dueDate,
                        'paid_at' => $paidAt,
                        'payment_terms' => 'Paiement à 30 jours fin de mois',
                        'discount_percentage' => rand(0, 100) > 85 ? rand(5, 10) : null,
                        'reminder_count' => $status === 'en_retard' ? rand(1, 3) : 0,
                        'reminder_sent_at' => $status === 'en_retard' ? Carbon::now()->subDays(rand(1, 10)) : null,
                        'internal_notes' => rand(0, 100) > 70 ? 'Note interne importante' : null,
                    ]);

                    // Ajouter des lignes cohérentes avec le type d'activité
                    $numItems = rand(1, 3);
                    
                    for ($j = 0; $j < $numItems; $j++) {
                        // 80% chance d'avoir une prestation du même type, 20% mixte
                        if (rand(1, 100) <= 80) {
                            $prestation = $this->getRandomPrestationByType($revenueType);
                        } else {
                            $prestation = $this->prestations[array_rand($this->prestations)];
                        }
                        
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
                                'payment_date' => $invoice->paid_at ?? $invoice->issued_at->copy()->addDays(rand(10, 25)),
                                'notes' => 'Paiement intégral',
                            ]);
                        }
                    }

                    $invoiceCount++;
                }
            }

            $this->command->info("  ✓ {$user->full_name} : 8 factures créées");
        }

        $this->command->info("✅ {$invoiceCount} factures manuelles créées");
        
        // Statistiques par type
        $this->command->info("");
        $this->command->info("📊 Répartition par type d'activité :");
        $this->command->info("  🏠 Transaction : {$typeStats['transaction']} factures");
        $this->command->info("  🔑 Location   : {$typeStats['location']} factures");
        $this->command->info("  🏢 Syndic     : {$typeStats['syndic']} factures");
        $this->command->info("  📋 Autres     : {$typeStats['autres']} factures");
        
        // Statistiques finales
        $totalPaid = Invoice::where('status', 'payee')->sum('total_ttc');
        $totalUnpaid = Invoice::whereIn('status', ['emise', 'en_retard'])->sum('total_ttc');
        
        $this->command->info("");
        $this->command->info("💰 CA total payé : " . number_format($totalPaid, 2, ',', ' ') . " €");
        $this->command->info("⏳ Montant impayé : " . number_format($totalUnpaid, 2, ',', ' ') . " €");
        
        // Stats URSSAF par type
        $this->command->info("");
        $this->command->info("📈 Ventilation URSSAF (factures payées) :");
        foreach (['transaction', 'location', 'syndic', 'autres'] as $type) {
            $caType = Invoice::where('status', 'payee')->where('revenue_type', $type)->sum('total_ht');
            $countType = Invoice::where('status', 'payee')->where('revenue_type', $type)->count();
            $emoji = match($type) {
                'transaction' => '🏠',
                'location' => '🔑',
                'syndic' => '🏢',
                'autres' => '📋',
            };
            $this->command->info("  {$emoji} " . ucfirst($type) . " : " . number_format($caType, 2, ',', ' ') . " € HT ({$countType} factures)");
        }
    }

    /**
     * Deviner le type d'activité à partir d'une description
     */
    private function guessRevenueTypeFromDescription(string $description): string
    {
        $description = strtolower($description);
        
        // Transaction
        if (str_contains($description, 'vente') || 
            str_contains($description, 'transaction') || 
            str_contains($description, 'négociation') ||
            str_contains($description, 'mandat')) {
            return 'transaction';
        }
        
        // Location
        if (str_contains($description, 'location') || 
            str_contains($description, 'locatif') || 
            str_contains($description, 'état des lieux') ||
            str_contains($description, 'edl') ||
            str_contains($description, 'bail')) {
            return 'location';
        }
        
        // Syndic
        if (str_contains($description, 'syndic') || 
            str_contains($description, 'copropriété') || 
            str_contains($description, 'ag ')) {
            return 'syndic';
        }
        
        // Par défaut
        return $this->getRandomRevenueType();
    }
}