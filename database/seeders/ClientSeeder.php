<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // 10 Clients Particuliers
        for ($i = 1; $i <= 10; $i++) {
            Client::create([
                'type' => 'particulier',
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'mobile' => $faker->mobileNumber(),
                'address' => $faker->streetAddress(),
                'postal_code' => $faker->postcode(),
                'city' => $faker->city(),
                'country' => 'France',
                'notes' => $i <= 3 ? $faker->sentence() : null,
                'is_active' => true,
            ]);
        }

        // 10 Clients Professionnels
        $companies = [
            'SCI Les Jardins',
            'Copropriété Résidence du Parc',
            'SARL Immobilier Plus',
            'SCI Les Acacias',
            'Cabinet Durand Immobilier',
            'Résidence Le Clos Fleuri',
            'SCI Patrimoine & Gestion',
            'Syndic Professionnel de l\'Est',
            'Entreprise Martin Bâtiment',
            'SCI Les Oliviers',
        ];

        foreach ($companies as $index => $companyName) {
            Client::create([
                'type' => 'professionnel',
                'name' => $faker->name(),
                'company_name' => $companyName,
                'siret' => $faker->siret(false),
                'tva_number' => 'FR' . $faker->randomNumber(9, true),
                'email' => strtolower(str_replace(' ', '', $companyName)) . '@example.com',
                'phone' => $faker->phoneNumber(),
                'mobile' => $faker->mobileNumber(),
                'address' => $faker->streetAddress(),
                'postal_code' => $faker->postcode(),
                'city' => $faker->city(),
                'country' => 'France',
                'notes' => $index <= 2 ? 'Client important - Volume élevé' : null,
                'is_active' => $index < 9, // 1 client inactif pour tester
            ]);
        }

        $this->command->info('✅ 20 clients créés (10 particuliers + 10 professionnels)');
    }
}