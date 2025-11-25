<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrateur principal - Gère tous les départements
        $admin = User::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'admin@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'administrateur',
            'is_active' => true,
            'department' => 'Direction',
            'position' => 'Directeur Général',
            'localisation' => 'Paris',
            'phone' => '01 23 45 67 89',
            'revenue_target' => 500000.00,
            'rsac_number' => '123 456 789',
            'professional_email' => 'j.dupont@gestimmo.fr',
            'professional_phone' => '01 23 45 67 89',
            'professional_address' => '123 Avenue des Champs-Élysées',
            'professional_city' => 'Paris',
            'professional_postal_code' => '75008',
            'legal_mentions' => 'GEST\'IMMO - SARL au capital de 100 000€ - RCS Paris B 123 456 789',
            'footer_text' => 'GEST\'IMMO - Votre partenaire immobilier de confiance',
            'managed_departments' => ['*'], // Gère tous les départements
            'last_login_at' => now(),
        ]);

        // Manager régional Île-de-France - Gère plusieurs départements d'Île-de-France
        $managerIDF = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'marie.dupont@intranet.fr',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Manager Régional Île-de-France',
            'localisation' => 'Hauts-de-Seine',
            'phone' => '06 12 34 56 78',
            'manager_id' => $admin->id,
            'revenue_target' => 300000.00,
            'rsac_number' => '987 654 321',
            'professional_email' => 'm.dupont@gestimmo.fr',
            'professional_phone' => '01 98 76 54 32',
            'professional_address' => '45 Rue de la Défense',
            'professional_city' => 'Puteaux',
            'professional_postal_code' => '92800',
            'legal_mentions' => 'GEST\'IMMO - Agence Île-de-France',
            'footer_text' => 'Marie Dupont - Manager Régional Île-de-France',
            'managed_departments' => [
                'Paris',
                'Hauts-de-Seine',
                'Seine-Saint-Denis',
                'Val-de-Marne',
                'Seine-et-Marne',
                'Yvelines',
                'Essonne',
                'Val-d\'Oise'
            ],
            'last_login_at' => now()->subDays(1),
        ]);

        // Manager régional PACA - Gère les départements du Sud-Est
        $managerPACA = User::create([
            'first_name' => 'Pierre',
            'last_name' => 'Bernard',
            'email' => 'pierre.bernard@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Manager Régional PACA',
            'localisation' => 'Bouches-du-Rhône',
            'phone' => '06 23 45 67 89',
            'manager_id' => $admin->id,
            'revenue_target' => 250000.00,
            'rsac_number' => '456 789 123',
            'professional_email' => 'p.bernard@gestimmo.fr',
            'professional_phone' => '04 91 12 34 56',
            'professional_address' => '78 La Canebière',
            'professional_city' => 'Marseille',
            'professional_postal_code' => '13001',
            'legal_mentions' => 'GEST\'IMMO - Agence PACA',
            'footer_text' => 'Pierre Bernard - Manager Régional PACA',
            'managed_departments' => [
                'Bouches-du-Rhône',
                'Var',
                'Alpes-Maritimes',
                'Vaucluse',
                'Alpes-de-Haute-Provence',
                'Hautes-Alpes'
            ],
            'last_login_at' => now()->subDays(2),
        ]);

        // Manager régional Auvergne-Rhône-Alpes - Gère les départements du Centre-Est
        $managerARA = User::create([
            'first_name' => 'Sophie',
            'last_name' => 'Dubois',
            'email' => 'sophie.dubois@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Manager Régional Auvergne-Rhône-Alpes',
            'localisation' => 'Rhône',
            'phone' => '06 34 45 67 89',
            'manager_id' => $admin->id,
            'revenue_target' => 220000.00,
            'rsac_number' => '789 123 456',
            'professional_email' => 's.dubois@gestimmo.fr',
            'professional_phone' => '04 78 12 34 56',
            'professional_address' => '12 Place Bellecour',
            'professional_city' => 'Lyon',
            'professional_postal_code' => '69002',
            'legal_mentions' => 'GEST\'IMMO - Agence Auvergne-Rhône-Alpes',
            'footer_text' => 'Sophie Dubois - Manager Régional Auvergne-Rhône-Alpes',
            'managed_departments' => [
                'Rhône',
                'Isère',
                'Loire',
                'Haute-Savoie',
                'Savoie',
                'Ain',
                'Drôme',
                'Ardèche',
                'Puy-de-Dôme',
                'Allier',
                'Cantal',
                'Haute-Loire'
            ],
            'last_login_at' => now()->subDays(3),
        ]);

        // Manager régional Occitanie - Gère les départements du Sud-Ouest
        $managerOccitanie = User::create([
            'first_name' => 'Luc',
            'last_name' => 'Moreau',
            'email' => 'luc.moreau@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Manager Régional Occitanie',
            'localisation' => 'Haute-Garonne',
            'phone' => '06 45 56 78 90',
            'manager_id' => $admin->id,
            'revenue_target' => 200000.00,
            'rsac_number' => '321 654 987',
            'professional_email' => 'l.moreau@gestimmo.fr',
            'professional_phone' => '05 61 23 45 67',
            'professional_address' => '56 Rue de Metz',
            'professional_city' => 'Toulouse',
            'professional_postal_code' => '31000',
            'legal_mentions' => 'GEST\'IMMO - Agence Occitanie',
            'footer_text' => 'Luc Moreau - Manager Régional Occitanie',
            'managed_departments' => [
                'Haute-Garonne',
                'Hérault',
                'Aude',
                'Gard',
                'Pyrénées-Orientales',
                'Ariège',
                'Tarn',
                'Tarn-et-Garonne',
                'Aveyron',
                'Lot',
                'Gers',
                'Hautes-Pyrénées',
                'Lozère'
            ],
            'last_login_at' => now()->subDays(4),
        ]);

        // Manager sans départements gérés (gère uniquement son équipe directe)
        $managerLocal = User::create([
            'first_name' => 'Claire',
            'last_name' => 'Petit',
            'email' => 'claire.petit@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Manager Local Lyon',
            'localisation' => 'Rhône',
            'phone' => '06 56 67 78 89',
            'manager_id' => $managerARA->id,
            'revenue_target' => 150000.00,
            'rsac_number' => '654 321 987',
            'professional_email' => 'c.petit@gestimmo.fr',
            'professional_phone' => '04 78 98 76 54',
            'professional_address' => '89 Cours Vitton',
            'professional_city' => 'Lyon',
            'professional_postal_code' => '69006',
            'managed_departments' => null, // Gère uniquement son équipe directe
            'last_login_at' => now()->subDays(5),
        ]);

        // Louis
        User::create([
            'first_name' => 'Louis',
            'last_name' => 'Zerri',
            'email' => 'l.zerri@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'administrateur',
            'is_active' => true,
            'department' => 'Informatique',
            'position' => 'Développeur',
            'localisation' => 'Amiens',
            'phone' => '06 27 50 36 71',
            'managed_departments' => ['*'], // Gère tous les départements
            'last_login_at' => now(),
        ]);

        // Collaborateurs sous Marie Martin (Île-de-France)
        User::create([
            'first_name' => 'Thomas',
            'last_name' => 'Robert',
            'email' => 'thomas.robert@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial Senior',
            'localisation' => 'Paris',
            'phone' => '06 67 78 89 90',
            'manager_id' => $managerIDF->id,
            'revenue_target' => 80000.00,
            'rsac_number' => '111 222 333',
            'professional_email' => 't.robert@gestimmo.fr',
            'professional_phone' => '01 23 98 76 54',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(12),
        ]);

        User::create([
            'first_name' => 'Julie',
            'last_name' => 'Blanc',
            'email' => 'julie.blanc@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial',
            'localisation' => 'Hauts-de-Seine',
            'phone' => '06 78 89 90 01',
            'manager_id' => $managerIDF->id,
            'revenue_target' => 60000.00,
            'rsac_number' => '222 333 444',
            'professional_email' => 'j.blanc@gestimmo.fr',
            'professional_phone' => '01 45 67 89 01',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(8),
        ]);

        // Collaborateurs sous Pierre Bernard (PACA)
        User::create([
            'first_name' => 'Alexandre',
            'last_name' => 'Girard',
            'email' => 'alexandre.girard@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial Senior',
            'localisation' => 'Bouches-du-Rhône',
            'phone' => '06 89 90 01 12',
            'manager_id' => $managerPACA->id,
            'revenue_target' => 75000.00,
            'rsac_number' => '333 444 555',
            'professional_email' => 'a.girard@gestimmo.fr',
            'professional_phone' => '04 91 23 45 67',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(6),
        ]);

        User::create([
            'first_name' => 'Céline',
            'last_name' => 'Garnier',
            'email' => 'celine.garnier@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial',
            'localisation' => 'Var',
            'phone' => '06 90 01 12 23',
            'manager_id' => $managerPACA->id,
            'revenue_target' => 55000.00,
            'rsac_number' => '444 555 666',
            'professional_email' => 'c.garnier@gestimmo.fr',
            'professional_phone' => '04 94 12 34 56',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(4),
        ]);

        // Collaborateurs sous Sophie Dubois (Auvergne-Rhône-Alpes)
        User::create([
            'first_name' => 'Nicolas',
            'last_name' => 'Rousseau',
            'email' => 'nicolas.rousseau@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial Senior',
            'localisation' => 'Rhône',
            'phone' => '06 01 12 23 34',
            'manager_id' => $managerARA->id,
            'revenue_target' => 70000.00,
            'rsac_number' => '555 666 777',
            'professional_email' => 'n.rousseau@gestimmo.fr',
            'professional_phone' => '04 78 23 45 67',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(10),
        ]);

        User::create([
            'first_name' => 'Émilie',
            'last_name' => 'Vincent',
            'email' => 'emilie.vincent@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial',
            'localisation' => 'Isère',
            'phone' => '06 12 23 34 45',
            'manager_id' => $managerARA->id,
            'revenue_target' => 50000.00,
            'rsac_number' => '666 777 888',
            'professional_email' => 'e.vincent@gestimmo.fr',
            'professional_phone' => '04 76 12 34 56',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(2),
        ]);

        // Collaborateurs sous Luc Moreau (Occitanie)
        User::create([
            'first_name' => 'Julien',
            'last_name' => 'Fournier',
            'email' => 'julien.fournier@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial Senior',
            'localisation' => 'Haute-Garonne',
            'phone' => '06 23 34 45 56',
            'manager_id' => $managerOccitanie->id,
            'revenue_target' => 65000.00,
            'rsac_number' => '777 888 999',
            'professional_email' => 'j.fournier@gestimmo.fr',
            'professional_phone' => '05 61 34 56 78',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(14),
        ]);

        User::create([
            'first_name' => 'Anaïs',
            'last_name' => 'Morel',
            'email' => 'anais.morel@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial',
            'localisation' => 'Hérault',
            'phone' => '06 34 45 56 67',
            'manager_id' => $managerOccitanie->id,
            'revenue_target' => 48000.00,
            'rsac_number' => '888 999 000',
            'professional_email' => 'a.morel@gestimmo.fr',
            'professional_phone' => '04 67 12 34 56',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(16),
        ]);

        // Collaborateurs sous Claire Petit (Manager local)
        User::create([
            'first_name' => 'David',
            'last_name' => 'Simon',
            'email' => 'david.simon@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial',
            'localisation' => 'Rhône',
            'phone' => '06 45 56 67 78',
            'manager_id' => $managerLocal->id,
            'revenue_target' => 45000.00,
            'rsac_number' => '999 000 111',
            'professional_email' => 'd.simon@gestimmo.fr',
            'professional_phone' => '04 78 34 56 78',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(3),
        ]);

        User::create([
            'first_name' => 'Laura',
            'last_name' => 'Michel',
            'email' => 'laura.michel@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => true,
            'department' => 'Commercial',
            'position' => 'Commercial Junior',
            'localisation' => 'Rhône',
            'phone' => '06 56 67 78 89',
            'manager_id' => $managerLocal->id,
            'revenue_target' => 35000.00,
            'rsac_number' => '000 111 222',
            'professional_email' => 'l.michel@gestimmo.fr',
            'professional_phone' => '04 78 45 67 89',
            'managed_departments' => null,
            'last_login_at' => now()->subHours(1),
        ]);

        // Compte inactif (exemple)
        User::create([
            'first_name' => 'Marc',
            'last_name' => 'Leroy',
            'email' => 'marc.leroy@gestimmo.fr',
            'password' => Hash::make('password'),
            'role' => 'collaborateur',
            'is_active' => false, // Compte désactivé
            'department' => 'Commercial',
            'position' => 'Commercial',
            'localisation' => 'Paris',
            'phone' => '06 67 78 89 90',
            'manager_id' => $managerIDF->id,
            'revenue_target' => 50000.00,
            'rsac_number' => '111 222 333',
            'managed_departments' => null,
            'last_login_at' => now()->subMonths(3),
        ]);

        $this->command->info('✅ ' . User::count() . ' utilisateurs créés avec succès !');
        $this->command->info('📊 Répartition des départements gérés :');
        $this->command->info('   - Administrateur : Tous les départements (*)');
        $this->command->info('   - Manager IDF : 8 départements');
        $this->command->info('   - Manager PACA : 6 départements');
        $this->command->info('   - Manager ARA : 12 départements');
        $this->command->info('   - Manager Occitanie : 13 départements');
        $this->command->info('   - Manager Local : Aucun département (équipe directe uniquement)');
    }
}