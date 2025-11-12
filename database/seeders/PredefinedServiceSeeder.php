<?php

namespace Database\Seeders;

use App\Models\PredefinedService;
use Illuminate\Database\Seeder;

class PredefinedServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            // LOCATION
            [
                'name' => 'Mise en location - Studio',
                'description' => 'Mise en location d\'un studio (moins de 30m²)',
                'category' => 'location',
                'default_price' => 350.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 10,
            ],
            [
                'name' => 'Mise en location - T2/T3',
                'description' => 'Mise en location d\'un T2 ou T3 (30-70m²)',
                'category' => 'location',
                'default_price' => 450.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 20,
            ],
            [
                'name' => 'Mise en location - T4 et plus',
                'description' => 'Mise en location d\'un T4 ou plus (plus de 70m²)',
                'category' => 'location',
                'default_price' => 550.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 30,
            ],

            // ÉTATS DES LIEUX
            [
                'name' => 'État des lieux d\'entrée - Studio',
                'description' => 'Réalisation état des lieux d\'entrée pour studio',
                'category' => 'etat_lieux_entree',
                'default_price' => 120.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 40,
            ],
            [
                'name' => 'État des lieux d\'entrée - Appartement',
                'description' => 'Réalisation état des lieux d\'entrée pour appartement',
                'category' => 'etat_lieux_entree',
                'default_price' => 150.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 50,
            ],
            [
                'name' => 'État des lieux d\'entrée - Maison',
                'description' => 'Réalisation état des lieux d\'entrée pour maison',
                'category' => 'etat_lieux_entree',
                'default_price' => 200.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 60,
            ],
            [
                'name' => 'État des lieux de sortie - Studio',
                'description' => 'Réalisation état des lieux de sortie pour studio',
                'category' => 'etat_lieux_sortie',
                'default_price' => 130.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 70,
            ],
            [
                'name' => 'État des lieux de sortie - Appartement',
                'description' => 'Réalisation état des lieux de sortie pour appartement',
                'category' => 'etat_lieux_sortie',
                'default_price' => 160.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 80,
            ],
            [
                'name' => 'État des lieux de sortie - Maison',
                'description' => 'Réalisation état des lieux de sortie pour maison',
                'category' => 'etat_lieux_sortie',
                'default_price' => 220.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 90,
            ],

            // GESTION
            [
                'name' => 'Gestion locative mensuelle',
                'description' => 'Gestion locative complète (8% du loyer charges comprises)',
                'category' => 'gestion',
                'default_price' => 80.00,
                'default_tva_rate' => 20,
                'unit' => 'mois',
                'default_quantity' => 1,
                'sort_order' => 100,
            ],
            [
                'name' => 'Gestion des charges',
                'description' => 'Gestion et régularisation des charges annuelles',
                'category' => 'gestion',
                'default_price' => 150.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 110,
            ],

            // SYNDIC
            [
                'name' => 'Syndic de copropriété - Petit immeuble',
                'description' => 'Gestion syndic pour petit immeuble (moins de 10 lots)',
                'category' => 'syndic',
                'default_price' => 500.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 120,
            ],

            // TRANSACTION
            [
                'name' => 'Transaction immobilière',
                'description' => 'Commission transaction vente immobilière',
                'category' => 'transaction',
                'default_price' => 5000.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 130,
            ],

            // EXPERTISE
            [
                'name' => 'Expertise immobilière',
                'description' => 'Évaluation et expertise d\'un bien immobilier',
                'category' => 'expertise',
                'default_price' => 350.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 140,
            ],

            // CONSULTATION
            [
                'name' => 'Consultation conseil',
                'description' => 'Consultation et conseil immobilier',
                'category' => 'consultation',
                'default_price' => 80.00,
                'default_tva_rate' => 20,
                'unit' => 'heure',
                'default_quantity' => 1,
                'sort_order' => 150,
            ],

            // AUTRES
            [
                'name' => 'Déplacement',
                'description' => 'Frais de déplacement',
                'category' => 'autres',
                'default_price' => 0.50,
                'default_tva_rate' => 20,
                'unit' => 'km',
                'default_quantity' => 1,
                'sort_order' => 160,
            ],
            [
                'name' => 'Frais administratifs',
                'description' => 'Frais de dossier et administratifs',
                'category' => 'autres',
                'default_price' => 50.00,
                'default_tva_rate' => 20,
                'unit' => 'forfait',
                'default_quantity' => 1,
                'sort_order' => 170,
            ],
        ];

        foreach ($services as $service) {
            PredefinedService::create($service);
        }
    }
}