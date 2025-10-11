<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CommunicationProduct;

class CommunicationProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Flyers A5 - Recto/Verso',
                'description' => 'Flyers format A5 (148x210mm), impression recto-verso en quadrichromie, papier 250g couché mat. Idéal pour vos campagnes marketing et événements.',
                'reference' => 'FLY-A5-RV',
                'price' => 89.90,
                'stock_quantity' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Cartes de visite - Pack 500',
                'description' => 'Cartes de visite format standard 85x55mm, impression recto-verso, papier 350g. Pack de 500 cartes. Finition au choix : mat, brillant ou soft-touch.',
                'reference' => 'CDV-500',
                'price' => 45.00,
                'stock_quantity' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Banderole kakémono 200x100cm',
                'description' => 'Banderole kakémono enroulable avec structure, impression haute qualité 200x100cm. Support idéal pour salons, événements et présentations.',
                'reference' => 'BAN-200',
                'price' => 129.00,
                'stock_quantity' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Affiches A3 - Pack 100',
                'description' => 'Affiches format A3 (297x420mm), impression recto, papier 200g. Pack de 100 affiches pour vos communications internes ou externes.',
                'reference' => 'AFF-A3-100',
                'price' => 75.00,
                'stock_quantity' => 75,
                'is_active' => true,
            ],
            [
                'name' => 'Stylos personnalisés - Pack 100',
                'description' => 'Stylos bille avec logo personnalisé, corps plastique couleur au choix. Encre bleue. Pack de 100 stylos. Délai de personnalisation : 7 jours.',
                'reference' => 'STY-100',
                'price' => 32.00,
                'stock_quantity' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Tote bags personnalisés - Pack 50',
                'description' => 'Sacs en coton 100% naturel, impression sérigraphie 1 couleur. Dimensions : 38x42cm. Pack de 50 tote bags. Parfait pour vos événements écologiques.',
                'reference' => 'TOT-50',
                'price' => 125.00,
                'stock_quantity' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Brochures A4 - Pack 250',
                'description' => 'Brochures format A4, 8 pages, impression quadri recto-verso, papier 170g. Pliage 2 volets. Pack de 250 brochures pour présenter vos services.',
                'reference' => 'BRO-A4-250',
                'price' => 185.00,
                'stock_quantity' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Autocollants personnalisés - Pack 500',
                'description' => 'Autocollants ronds ou carrés, diamètre/côté 5cm, impression couleur, vinyle brillant résistant. Pack de 500 stickers pour vos campagnes.',
                'reference' => 'AUT-500',
                'price' => 68.00,
                'stock_quantity' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Porte-clés personnalisés - Pack 100',
                'description' => 'Porte-clés métalliques avec impression logo, forme rectangulaire 5x3cm. Finition époxy brillante. Pack de 100 porte-clés.',
                'reference' => 'PCL-100',
                'price' => 95.00,
                'stock_quantity' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Mugs personnalisés - Pack 24',
                'description' => 'Mugs en céramique blanche 325ml, impression sublimation couleur. Passe au micro-ondes et lave-vaisselle. Pack de 24 mugs.',
                'reference' => 'MUG-24',
                'price' => 145.00,
                'stock_quantity' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Carnets A5 personnalisés - Pack 50',
                'description' => 'Carnets spirale format A5, 100 pages lignées, couverture rigide personnalisée. Pack de 50 carnets pour vos collaborateurs ou clients.',
                'reference' => 'CAR-A5-50',
                'price' => 210.00,
                'stock_quantity' => 35,
                'is_active' => true,
            ],
            [
                'name' => 'Calendriers muraux 2025 - Pack 100',
                'description' => 'Calendriers muraux format A3, 12 mois, impression recto, papier 200g. Reliure spirale métal. Pack de 100 calendriers.',
                'reference' => 'CAL-2025-100',
                'price' => 295.00,
                'stock_quantity' => 20,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            CommunicationProduct::create($product);
        }

        $this->command->info('Produits de communication créés avec succès !');
        $this->command->info('- 12 produits différents ajoutés');
        $this->command->info('- Flyers, cartes de visite, objets publicitaires, etc.');
    }
}