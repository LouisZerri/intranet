<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
use Faker\Factory as Faker;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('fr_FR');

        // Récupérer TOUS les utilisateurs actifs (y compris admin)
        $users = User::where('is_active', true)->get();

        if ($users->isEmpty()) {
            $this->command->warn('⚠️ Aucun utilisateur trouvé. Exécutez UserSeeder d\'abord.');
            return;
        }

        $totalClients = 0;

        foreach ($users as $user) {
            // Nombre de clients selon le rôle
            $nbParticuliers = match($user->role) {
                'administrateur' => 5,
                'manager' => 4,
                'collaborateur' => 3,
            };
            
            $nbProfessionnels = match($user->role) {
                'administrateur' => 3,
                'manager' => 2,
                'collaborateur' => 2,
            };

            // Créer des clients particuliers
            for ($i = 1; $i <= $nbParticuliers; $i++) {
                Client::create([
                    'user_id' => $user->id,
                    'type' => 'particulier',
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'phone' => $faker->phoneNumber(),
                    'mobile' => $faker->mobileNumber(),
                    'address' => $faker->streetAddress(),
                    'postal_code' => $faker->postcode(),
                    'city' => $faker->city(),
                    'country' => 'France',
                    'notes' => $i === 1 ? $faker->sentence() : null,
                    'is_active' => true,
                ]);
                $totalClients++;
            }

            // Créer des clients professionnels
            for ($j = 1; $j <= $nbProfessionnels; $j++) {
                $companyTypes = ['SCI', 'SARL', 'SAS', 'EURL', 'SA'];
                $companyName = $companyTypes[array_rand($companyTypes)] . ' ' . $faker->company();
                
                Client::create([
                    'user_id' => $user->id,
                    'type' => 'professionnel',
                    'name' => $faker->name(),
                    'company_name' => $companyName,
                    'siret' => $faker->siret(false),
                    'tva_number' => 'FR' . $faker->randomNumber(9, true),
                    'email' => $faker->unique()->companyEmail(),
                    'phone' => $faker->phoneNumber(),
                    'mobile' => $faker->mobileNumber(),
                    'address' => $faker->streetAddress(),
                    'postal_code' => $faker->postcode(),
                    'city' => $faker->city(),
                    'country' => 'France',
                    'notes' => $j === 1 ? 'Client important - Volume élevé' : null,
                    'is_active' => true,
                ]);
                $totalClients++;
            }

            $this->command->info("  ✓ {$user->full_name} ({$user->role}) : " . ($nbParticuliers + $nbProfessionnels) . " clients");
        }

        $this->command->info("✅ {$totalClients} clients créés au total");
    }
}