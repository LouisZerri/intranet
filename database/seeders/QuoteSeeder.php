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
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = Client::where('is_active', true)->get();
        $collaborateurs = User::whereIn('role', ['collaborateur', 'manager'])->where('is_active', true)->get();

        if ($clients->isEmpty() || $collaborateurs->isEmpty()) {
            $this->command->error('❌ Erreur : Clients ou collaborateurs manquants. Exécutez d\'abord ClientSeeder et assurez-vous d\'avoir des utilisateurs.');
            return;
        }

        // Prestations génériques (sans catégorisation par service)
        $prestations = [
            ['desc' => 'Mise en location - Studio', 'details' => 'Mise en location d\'un studio (moins de 30m²)', 'prix' => 350.00],
            ['desc' => 'Mise en location - T2/T3', 'details' => 'Mise en location d\'un T2 ou T3 (30-70m²)', 'prix' => 450.00],
            ['desc' => 'Mise en location - T4+', 'details' => 'Mise en location d\'un T4 ou plus (70m²+)', 'prix' => 650.00],
            ['desc' => 'État des lieux d\'entrée - Studio', 'details' => 'Réalisation état des lieux d\'entrée pour studio', 'prix' => 120.00],
            ['desc' => 'État des lieux d\'entrée - Appartement', 'details' => 'Réalisation état des lieux d\'entrée pour appartement', 'prix' => 180.00],
            ['desc' => 'État des lieux d\'entrée - Maison', 'details' => 'Réalisation état des lieux d\'entrée pour maison', 'prix' => 200.00],
            ['desc' => 'État des lieux de sortie - Studio', 'details' => 'Réalisation état des lieux de sortie pour studio', 'prix' => 120.00],
            ['desc' => 'État des lieux de sortie - Appartement', 'details' => 'Réalisation état des lieux de sortie pour appartement', 'prix' => 180.00],
            ['desc' => 'État des lieux de sortie - Maison', 'details' => 'Réalisation état des lieux de sortie pour maison', 'prix' => 200.00],
            ['desc' => 'Gestion locative mensuelle', 'details' => 'Gestion complète du bien locatif (loyers, charges, travaux)', 'prix' => 120.00],
            ['desc' => 'Recherche et sélection locataire', 'details' => 'Présélection et vérification des dossiers locataires', 'prix' => 650.00],
            ['desc' => 'Suivi des charges et régularisation', 'details' => 'Gestion annuelle des charges locatives', 'prix' => 180.00],
            ['desc' => 'Gestion des travaux', 'details' => 'Coordination et suivi des travaux dans le bien', 'prix' => 320.00],
            ['desc' => 'Syndic de copropriété', 'details' => 'Gestion syndic (honoraires mensuels)', 'prix' => 890.00],
            ['desc' => 'Assemblée générale annuelle', 'details' => 'Organisation et tenue de l\'AG annuelle', 'prix' => 450.00],
            ['desc' => 'Photos et rapport détaillé', 'details' => 'Reportage photo professionnel et rapport complet', 'prix' => 80.00],
            ['desc' => 'Évaluation des dégradations', 'details' => 'Expertise et chiffrage des dégradations constatées', 'prix' => 120.00],
        ];

        $statusDistribution = [
            'brouillon' => 5,
            'envoye' => 8,
            'accepte' => 6,
            'refuse' => 3,
            'converti' => 3,
        ];

        $quoteCount = 0;

        foreach ($statusDistribution as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $client = $clients->random();
                $user = $collaborateurs->random();
                
                // Dates en fonction du statut
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

                // Ajouter des lignes au devis (entre 1 et 4 prestations)
                $numItems = rand(1, 4);
                
                // Sélectionner des prestations aléatoires
                $randomKeys = array_rand($prestations, min($numItems, count($prestations)));
                if (!is_array($randomKeys)) {
                    $randomKeys = [$randomKeys];
                }
                
                foreach ($randomKeys as $index => $key) {
                    $prestation = $prestations[$key];
                    
                    // Combiner le titre et les détails sur 2 lignes
                    $description = $prestation['desc'] . "\n" . $prestation['details'];
                    
                    QuoteItem::create([
                        'quote_id' => $quote->id,
                        'description' => $description,
                        'quantity' => rand(1, 3),
                        'unit_price' => $prestation['prix'],
                        'tva_rate' => 20.00,
                        'sort_order' => $index,
                    ]);
                }

                // Recalculer les totaux
                $quote->refresh();
                $quote->calculateTotals();
                $quote->save();

                $quoteCount++;
            }
        }

        $this->command->info("✅ {$quoteCount} devis créés avec leurs lignes");
    }
}