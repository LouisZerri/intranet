<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

class QuoteSeeder extends Seeder
{
    /**
     * Prestations avec leur type d'activité associé
     */
    private array $prestations = [
        // Transaction
        ['desc' => 'Honoraires de négociation vente', 'details' => 'Commission sur vente immobilière', 'prix' => 3500.00, 'type' => 'transaction'],
        ['desc' => 'Mandat de vente exclusif', 'details' => 'Prise en charge complète de la vente du bien', 'prix' => 2500.00, 'type' => 'transaction'],
        ['desc' => 'Estimation immobilière', 'details' => 'Évaluation professionnelle du bien', 'prix' => 350.00, 'type' => 'transaction'],
        ['desc' => 'Accompagnement achat immobilier', 'details' => 'Recherche et négociation pour acquéreur', 'prix' => 1800.00, 'type' => 'transaction'],
        
        // Location
        ['desc' => 'Mise en location - Studio', 'details' => 'Mise en location d\'un studio (moins de 30m²)', 'prix' => 350.00, 'type' => 'location'],
        ['desc' => 'Mise en location - T2/T3', 'details' => 'Mise en location d\'un T2 ou T3 (30-70m²)', 'prix' => 450.00, 'type' => 'location'],
        ['desc' => 'Mise en location - T4+', 'details' => 'Mise en location d\'un T4 ou plus (70m²+)', 'prix' => 650.00, 'type' => 'location'],
        ['desc' => 'État des lieux d\'entrée - Studio', 'details' => 'Réalisation état des lieux d\'entrée pour studio', 'prix' => 120.00, 'type' => 'location'],
        ['desc' => 'État des lieux d\'entrée - Appartement', 'details' => 'Réalisation état des lieux d\'entrée pour appartement', 'prix' => 180.00, 'type' => 'location'],
        ['desc' => 'État des lieux d\'entrée - Maison', 'details' => 'Réalisation état des lieux d\'entrée pour maison', 'prix' => 260.00, 'type' => 'location'],
        ['desc' => 'État des lieux de sortie - Studio', 'details' => 'Réalisation état des lieux de sortie pour studio', 'prix' => 120.00, 'type' => 'location'],
        ['desc' => 'État des lieux de sortie - Appartement', 'details' => 'Réalisation état des lieux de sortie pour appartement', 'prix' => 180.00, 'type' => 'location'],
        ['desc' => 'État des lieux de sortie - Maison', 'details' => 'Réalisation état des lieux de sortie pour maison', 'prix' => 260.00, 'type' => 'location'],
        ['desc' => 'Gestion locative mensuelle', 'details' => 'Gestion complète du bien locatif (loyers, charges, travaux)', 'prix' => 120.00, 'type' => 'location'],
        ['desc' => 'Recherche et sélection locataire', 'details' => 'Présélection et vérification des dossiers locataires', 'prix' => 650.00, 'type' => 'location'],
        ['desc' => 'Rédaction bail d\'habitation', 'details' => 'Rédaction complète du contrat de location', 'prix' => 350.00, 'type' => 'location'],
        
        // Syndic
        ['desc' => 'Syndic de copropriété', 'details' => 'Gestion syndic (honoraires mensuels)', 'prix' => 890.00, 'type' => 'syndic'],
        ['desc' => 'Assemblée générale annuelle', 'details' => 'Organisation et tenue de l\'AG annuelle', 'prix' => 450.00, 'type' => 'syndic'],
        ['desc' => 'Suivi travaux copropriété', 'details' => 'Coordination et suivi des travaux en copropriété', 'prix' => 680.00, 'type' => 'syndic'],
        ['desc' => 'Mise en conformité copropriété', 'details' => 'Audit et mise aux normes de la copropriété', 'prix' => 1200.00, 'type' => 'syndic'],
        
        // Autres
        ['desc' => 'Photos et rapport détaillé', 'details' => 'Reportage photo professionnel et rapport complet', 'prix' => 80.00, 'type' => 'autres'],
        ['desc' => 'Évaluation des dégradations', 'details' => 'Expertise et chiffrage des dégradations constatées', 'prix' => 120.00, 'type' => 'autres'],
        ['desc' => 'Conseil en investissement', 'details' => 'Étude personnalisée pour investissement locatif', 'prix' => 500.00, 'type' => 'autres'],
        ['desc' => 'Accompagnement administratif', 'details' => 'Aide aux démarches administratives immobilières', 'prix' => 200.00, 'type' => 'autres'],
    ];

    /**
     * Distribution pondérée des types d'activité
     * Transaction: 25%, Location: 50%, Syndic: 15%, Autres: 10%
     */
    private function getRandomRevenueType(): string
    {
        $rand = rand(1, 100);
        
        if ($rand <= 25) {
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
        return array_values(array_filter($this->prestations, fn($p) => $p['type'] === $type));
    }

    /**
     * Récupérer une prestation aléatoire d'un type donné
     */
    private function getRandomPrestationByType(string $type): array
    {
        $filtered = $this->getPrestationsByType($type);
        
        if (empty($filtered)) {
            return $this->prestations[array_rand($this->prestations)];
        }
        
        return $filtered[array_rand($filtered)];
    }

    public function run(): void
    {
        // Récupérer les utilisateurs actifs (sauf admin pour les devis)
        $users = User::whereIn('role', ['collaborateur', 'manager'])
            ->where('is_active', true)
            ->get();

        if ($users->isEmpty()) {
            $this->command->error('❌ Erreur : Aucun collaborateur ou manager trouvé.');
            return;
        }

        $quoteCount = 0;
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
                'envoye' => 2,
                'accepte' => 2,
                'refuse' => 1,
                'converti' => 1,
            ];

            foreach ($statusDistribution as $status => $count) {
                for ($i = 0; $i < $count; $i++) {
                    // Choisir un client de cet utilisateur
                    $client = $userClients->random();
                    
                    // Déterminer le type d'activité pour ce devis
                    $revenueType = $this->getRandomRevenueType();
                    $typeStats[$revenueType]++;
                    
                    $createdAt = match($status) {
                        'brouillon' => Carbon::now()->subDays(rand(1, 5)),
                        'envoye' => Carbon::now()->subDays(rand(5, 15)),
                        'accepte' => Carbon::now()->subDays(rand(10, 30)),
                        'refuse' => Carbon::now()->subDays(rand(15, 45)),
                        'converti' => Carbon::now()->subDays(rand(20, 60)),
                    };

                    $quote = Quote::create([
                        'quote_number' => Quote::generateQuoteNumber(),
                        'client_id' => $client->id,
                        'user_id' => $user->id,
                        'status' => $status,
                        'revenue_type' => $revenueType,
                        'validity_date' => $status === 'envoye' ? Carbon::now()->addDays(30) : 
                                          ($status === 'brouillon' ? null : Carbon::now()->subDays(rand(1, 10))),
                        'accepted_at' => in_array($status, ['accepte', 'converti']) ? $createdAt->copy()->addDays(rand(2, 7)) : null,
                        'refused_at' => $status === 'refuse' ? $createdAt->copy()->addDays(rand(3, 10)) : null,
                        'converted_at' => $status === 'converti' ? $createdAt->copy()->addDays(rand(15, 25)) : null,
                        'discount_percentage' => rand(0, 100) > 70 ? rand(5, 15) : null,
                        'internal_notes' => rand(0, 100) > 60 ? 'Note interne sur ce devis' : null,
                        'client_notes' => 'Merci de votre confiance. N\'hésitez pas à me contacter pour toute question.',
                        'payment_terms' => 'Paiement à 30 jours fin de mois',
                        'delivery_terms' => rand(0, 100) > 50 ? 'Intervention sous 7 jours ouvrés' : null,
                        'signed_electronically' => in_array($status, ['accepte', 'converti']) ? (rand(0, 100) > 50) : false,
                        'signature_date' => in_array($status, ['accepte', 'converti']) && rand(0, 100) > 50 ? 
                                           $createdAt->copy()->addDays(rand(2, 7)) : null,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);

                    // Ajouter des lignes au devis cohérentes avec le type d'activité
                    $numItems = rand(1, 4);
                    
                    for ($j = 0; $j < $numItems; $j++) {
                        // 80% chance d'avoir une prestation du même type, 20% mixte
                        if (rand(1, 100) <= 80) {
                            $prestation = $this->getRandomPrestationByType($revenueType);
                        } else {
                            $prestation = $this->prestations[array_rand($this->prestations)];
                        }
                        
                        $description = $prestation['desc'] . "\n" . $prestation['details'];
                        
                        QuoteItem::create([
                            'quote_id' => $quote->id,
                            'description' => $description,
                            'quantity' => rand(1, 3),
                            'unit_price' => $prestation['prix'],
                            'tva_rate' => 20.00,
                            'sort_order' => $j,
                        ]);
                    }

                    $quote->refresh();
                    $quote->calculateTotals();
                    $quote->save();

                    $quoteCount++;
                }
            }

            $this->command->info("  ✓ {$user->full_name} : 7 devis créés");
        }

        $this->command->info("✅ {$quoteCount} devis créés au total");
        
        // Statistiques par type
        $this->command->info("");
        $this->command->info("📊 Répartition par type d'activité :");
        $this->command->info("  🏠 Transaction : {$typeStats['transaction']} devis");
        $this->command->info("  🔑 Location   : {$typeStats['location']} devis");
        $this->command->info("  🏢 Syndic     : {$typeStats['syndic']} devis");
        $this->command->info("  📋 Autres     : {$typeStats['autres']} devis");
    }
}