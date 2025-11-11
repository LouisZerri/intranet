<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private function getRandomDepartement(): string
    {
        $departements = [
            // France métropolitaine (96 départements)
            'Ain',
            'Aisne',
            'Allier',
            'Alpes-de-Haute-Provence',
            'Hautes-Alpes',
            'Alpes-Maritimes',
            'Ardèche',
            'Ardennes',
            'Ariège',
            'Aube',
            'Aude',
            'Aveyron',
            'Bouches-du-Rhône',
            'Calvados',
            'Cantal',
            'Charente',
            'Charente-Maritime',
            'Cher',
            'Corrèze',
            'Corse-du-Sud',
            'Haute-Corse',
            'Côte-d\'Or',
            'Côtes-d\'Armor',
            'Creuse',
            'Dordogne',
            'Doubs',
            'Drôme',
            'Eure',
            'Eure-et-Loir',
            'Finistère',
            'Gard',
            'Haute-Garonne',
            'Gers',
            'Gironde',
            'Hérault',
            'Ille-et-Vilaine',
            'Indre',
            'Indre-et-Loire',
            'Isère',
            'Jura',
            'Landes',
            'Loir-et-Cher',
            'Loire',
            'Haute-Loire',
            'Loire-Atlantique',
            'Loiret',
            'Lot',
            'Lot-et-Garonne',
            'Lozère',
            'Maine-et-Loire',
            'Manche',
            'Marne',
            'Haute-Marne',
            'Mayenne',
            'Meurthe-et-Moselle',
            'Meuse',
            'Morbihan',
            'Moselle',
            'Nièvre',
            'Nord',
            'Oise',
            'Orne',
            'Pas-de-Calais',
            'Puy-de-Dôme',
            'Pyrénées-Atlantiques',
            'Hautes-Pyrénées',
            'Pyrénées-Orientales',
            'Bas-Rhin',
            'Haut-Rhin',
            'Rhône',
            'Haute-Saône',
            'Saône-et-Loire',
            'Sarthe',
            'Savoie',
            'Haute-Savoie',
            'Paris',
            'Seine-Maritime',
            'Seine-et-Marne',
            'Yvelines',
            'Deux-Sèvres',
            'Somme',
            'Tarn',
            'Tarn-et-Garonne',
            'Var',
            'Vaucluse',
            'Vendée',
            'Vienne',
            'Haute-Vienne',
            'Vosges',
            'Yonne',
            'Territoire de Belfort',
            'Essonne',
            'Hauts-de-Seine',
            'Seine-Saint-Denis',
            'Val-de-Marne',
            'Val-d\'Oise',
            // Départements d'Outre-mer (5 départements)
            'Guadeloupe',
            'Martinique',
            'Guyane',
            'La Réunion',
            'Mayotte'
        ];

        return $departements[array_rand($departements)];
    }

    public function run(): void
    {
        // Administrateur
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Système',
            'email' => 'admin@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'administrateur',
            'phone' => '01.23.45.67.89',
            'department' => 'Direction',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Administrateur Système',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Managers
        $manager1 = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'marie.dupont@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '01.23.45.67.10',
            'department' => 'Commercial',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Manager Commercial',
            'revenue_target' => 150000.00,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $manager2 = User::create([
            'first_name' => 'Pierre',
            'last_name' => 'Martin',
            'email' => 'pierre.martin@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '01.23.45.67.11',
            'department' => 'Marketing',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Manager Marketing',
            'revenue_target' => 120000.00,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $manager3 = User::create([
            'first_name' => 'Sophie',
            'last_name' => 'Leroy',
            'email' => 'sophie.leroy@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '01.23.45.67.12',
            'department' => 'RH',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Manager RH',
            'revenue_target' => 80000.00,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Collaborateurs équipe Commercial (Manager: Marie Dupont)
        User::create([
            'first_name' => 'Jean',
            'last_name' => 'Moreau',
            'email' => 'jean.moreau@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.20',
            'department' => 'Commercial',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Commercial Senior',
            'manager_id' => $manager1->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Claire',
            'last_name' => 'Bernard',
            'email' => 'claire.bernard@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.21',
            'department' => 'Commercial',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Commercial Junior',
            'manager_id' => $manager1->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Thomas',
            'last_name' => 'Petit',
            'email' => 'thomas.petit@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.22',
            'department' => 'Commercial',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Commercial',
            'manager_id' => $manager1->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Collaborateurs équipe Marketing (Manager: Pierre Martin)
        User::create([
            'first_name' => 'Amélie',
            'last_name' => 'Rousseau',
            'email' => 'amelie.rousseau@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.30',
            'department' => 'Marketing',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Chef de Produit',
            'manager_id' => $manager2->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Nicolas',
            'last_name' => 'Blanc',
            'email' => 'nicolas.blanc@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.31',
            'department' => 'Marketing',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Chargé de Communication',
            'manager_id' => $manager2->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Collaborateurs équipe RH (Manager: Sophie Leroy)
        User::create([
            'first_name' => 'Julie',
            'last_name' => 'Garnier',
            'email' => 'julie.garnier@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.40',
            'department' => 'RH',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Chargée de Recrutement',
            'manager_id' => $manager3->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Vincent',
            'last_name' => 'Durand',
            'email' => 'vincent.durand@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.41',
            'department' => 'RH',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Gestionnaire Paie',
            'manager_id' => $manager3->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Collaborateurs sans manager direct (rattachés à l'admin)
        User::create([
            'first_name' => 'Laura',
            'last_name' => 'Simon',
            'email' => 'laura.simon@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.50',
            'department' => 'IT',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Développeuse',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'first_name' => 'Antoine',
            'last_name' => 'Michel',
            'email' => 'antoine.michel@intranet.com',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'phone' => '01.23.45.67.51',
            'department' => 'Comptabilité',
            'localisation' => $this->getRandomDepartement(),
            'position' => 'Comptable',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Utilisateurs créés avec succès !');
        $this->command->info('Admin: admin@intranet.com / password');
        $this->command->info('Managers: marie.dupont@intranet.com, pierre.martin@intranet.com, sophie.leroy@intranet.com / password');
        $this->command->info('Collaborateurs: jean.moreau@intranet.com, claire.bernard@intranet.com, etc. / password');
    }
}