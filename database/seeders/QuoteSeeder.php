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

        $services = [
            'location' => 'Location',
            'etat_lieux_entree' => 'État des lieux d\'entrée',
            'etat_lieux_sortie' => 'État des lieux de sortie',
            'gestion' => 'Gestion locative',
            'syndic' => 'Syndic de copropriété',
        ];

        $prestations = [
            'location' => [
                ['desc' => 'Mise en location d\'un appartement T3', 'prix' => 850.00],
                ['desc' => 'Gestion locative mensuelle', 'prix' => 120.00],
                ['desc' => 'Recherche et sélection locataire', 'prix' => 650.00],
            ],
            'etat_lieux_entree' => [
                ['desc' => 'État des lieux d\'entrée - Appartement', 'prix' => 180.00],
                ['desc' => 'État des lieux d\'entrée - Maison', 'prix' => 250.00],
                ['desc' => 'Photos et rapport détaillé', 'prix' => 80.00],
            ],
            'etat_lieux_sortie' => [
                ['desc' => 'État des lieux de sortie - Appartement', 'prix' => 180.00],
                ['desc' => 'État des lieux de sortie - Maison', 'prix' => 250.00],
                ['desc' => 'Évaluation des dégradations', 'prix' => 120.00],
            ],
            'gestion' => [
                ['desc' => 'Gestion complète d\'un bien locatif', 'prix' => 450.00],
                ['desc' => 'Suivi des charges et régularisation', 'prix' => 180.00],
                ['desc' => 'Gestion des travaux', 'prix' => 320.00],
            ],
            'syndic' => [
                ['desc' => 'Gestion syndic copropriété (honoraires mensuels)', 'prix' => 890.00],
                ['desc' => 'Assemblée générale annuelle', 'prix' => 450.00],
                ['desc' => 'Suivi travaux copropriété', 'prix' => 380.00],
            ],
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
                $service = array_rand($services);
                
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
                    'service' => $service,
                    'status' => $status,
                    'validity_date' => $status === 'envoye' ? Carbon::now()->addDays(30) : 
                                      ($status === 'brouillon' ? null : Carbon::now()->subDays(rand(1, 10))),
                    'accepted_at' => in_array($status, ['accepte', 'converti']) ? $createdAt->copy()->addDays(rand(2, 7)) : null,
                    'refused_at' => $status === 'refuse' ? $createdAt->copy()->addDays(rand(3, 10)) : null,
                    'converted_at' => $status === 'converti' ? $createdAt->copy()->addDays(rand(15, 25)) : null,
                    'discount_percentage' => rand(0, 100) > 70 ? rand(5, 15) : null,
                    'internal_notes' => rand(0, 100) > 60 ? 'Note interne sur ce devis' : null,
                    'client_notes' => 'Merci de votre confiance. Conditions de paiement : 30 jours.',
                    'payment_terms' => 'Paiement à 30 jours fin de mois',
                    'signed_electronically' => in_array($status, ['accepte', 'converti']) ? (rand(0, 100) > 50) : false,
                    'signature_date' => in_array($status, ['accepte', 'converti']) && rand(0, 100) > 50 ? 
                                       $createdAt->copy()->addDays(rand(2, 7)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Ajouter des lignes au devis
                $items = $prestations[$service];
                $numItems = rand(1, min(3, count($items)));
                
                // array_rand retourne un int si numItems = 1, ou un array si numItems > 1
                $randomKeys = array_rand($items, $numItems);
                if (!is_array($randomKeys)) {
                    $randomKeys = [$randomKeys]; // Convertir en tableau si un seul élément
                }
                
                foreach ($randomKeys as $key) {
                    QuoteItem::create([
                        'quote_id' => $quote->id,
                        'description' => $items[$key]['desc'],
                        'quantity' => rand(1, 3),
                        'unit_price' => $items[$key]['prix'],
                        'tva_rate' => 20.00,
                        'sort_order' => $key,
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