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
            'Ain', 'Aisne', 'Allier', 'Alpes-de-Haute-Provence', 'Hautes-Alpes', 'Alpes-Maritimes',
            'Ardèche', 'Ardennes', 'Ariège', 'Aube', 'Aude', 'Aveyron', 'Bouches-du-Rhône',
            'Calvados', 'Cantal', 'Charente', 'Charente-Maritime', 'Cher', 'Corrèze',
            'Corse-du-Sud', 'Haute-Corse', 'Côte-d\'Or', 'Côtes-d\'Armor', 'Creuse', 'Dordogne',
            'Doubs', 'Drôme', 'Eure', 'Eure-et-Loir', 'Finistère', 'Gard', 'Haute-Garonne',
            'Gers', 'Gironde', 'Hérault', 'Ille-et-Vilaine', 'Indre', 'Indre-et-Loire', 'Isère',
            'Jura', 'Landes', 'Loir-et-Cher', 'Loire', 'Haute-Loire', 'Loire-Atlantique',
            'Loiret', 'Lot', 'Lot-et-Garonne', 'Lozère', 'Maine-et-Loire', 'Manche', 'Marne',
            'Haute-Marne', 'Mayenne', 'Meurthe-et-Moselle', 'Meuse', 'Morbihan', 'Moselle',
            'Nièvre', 'Nord', 'Oise', 'Orne', 'Pas-de-Calais', 'Puy-de-Dôme',
            'Pyrénées-Atlantiques', 'Hautes-Pyrénées', 'Pyrénées-Orientales', 'Bas-Rhin',
            'Haut-Rhin', 'Rhône', 'Haute-Saône', 'Saône-et-Loire', 'Sarthe', 'Savoie',
            'Haute-Savoie', 'Paris', 'Seine-Maritime', 'Seine-et-Marne', 'Yvelines',
            'Deux-Sèvres', 'Somme', 'Tarn', 'Tarn-et-Garonne', 'Var', 'Vaucluse', 'Vendée',
            'Vienne', 'Haute-Vienne', 'Vosges', 'Yonne', 'Territoire de Belfort', 'Essonne',
            'Hauts-de-Seine', 'Seine-Saint-Denis', 'Val-de-Marne', 'Val-d\'Oise',
            'Guadeloupe', 'Martinique', 'Guyane', 'La Réunion', 'Mayotte'
        ];

        return $departements[array_rand($departements)];
    }

    private function generateRSAC(): string
    {
        return sprintf('%03d %03d %03d', rand(100, 999), rand(100, 999), rand(100, 999));
    }

    private function getRandomAddress(): array
    {
        $streets = [
            ['35 Rue de la République', '75001', 'Paris'],
            ['128 Avenue des Champs-Élysées', '75008', 'Paris'],
            ['45 Boulevard Haussmann', '75009', 'Paris'],
            ['12 Rue Victor Hugo', '69002', 'Lyon'],
            ['78 Cours Lafayette', '69003', 'Lyon'],
            ['23 Avenue du Prado', '13006', 'Marseille'],
            ['56 Rue Paradis', '13001', 'Marseille'],
            ['91 Boulevard de la Liberté', '59000', 'Lille'],
            ['14 Place Kléber', '67000', 'Strasbourg'],
            ['67 Quai des Chartrons', '33000', 'Bordeaux'],
        ];

        return $streets[array_rand($streets)];
    }

    public function run(): void
    {
        // Administrateur
        $adminAddress = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $adminAddress[0],
            'professional_postal_code' => $adminAddress[1],
            'professional_city' => $adminAddress[2],
            'professional_email' => 'contact.admin@intranet.com',
            'professional_phone' => '01.23.45.67.89',
            'legal_mentions' => "Assurance RC Professionnelle - Contrat n°12345678\nTVA non applicable - Article 293 B du CGI\nDispense d'immatriculation au RCS - Loi n°2014-626 du 18 juin 2014",
            'footer_text' => "Merci de votre confiance. Pour toute question, n'hésitez pas à me contacter.",
        ]);

        // Managers
        $manager1Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $manager1Address[0],
            'professional_postal_code' => $manager1Address[1],
            'professional_city' => $manager1Address[2],
            'professional_email' => 'marie.dupont.pro@intranet.com',
            'professional_phone' => '06.12.34.56.78',
            'legal_mentions' => "Assurance RC Professionnelle MMA - Contrat n°87654321\nTVA non applicable - Article 293 B du CGI\nAgent commercial immatriculé au RSAC",
            'footer_text' => "Je reste à votre disposition pour tout complément d'information.",
        ]);

        $manager2Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $manager2Address[0],
            'professional_postal_code' => $manager2Address[1],
            'professional_city' => $manager2Address[2],
            'professional_email' => 'p.martin.pro@intranet.com',
            'professional_phone' => '06.23.45.67.89',
            'legal_mentions' => "Assurance RC Professionnelle AXA - Contrat n°11223344\nTVA non applicable - Article 293 B du CGI\nAgent commercial immatriculé au RSAC",
            'footer_text' => "Cordialement, Pierre Martin - Conseiller immobilier",
        ]);

        $manager3Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $manager3Address[0],
            'professional_postal_code' => $manager3Address[1],
            'professional_city' => $manager3Address[2],
            'legal_mentions' => "Assurance RC Professionnelle Allianz - Contrat n°99887766\nTVA non applicable - Article 293 B du CGI",
            'footer_text' => "Au plaisir de collaborer avec vous.",
        ]);

        // Collaborateurs équipe Commercial (Manager: Marie Dupont)
        $collab1Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab1Address[0],
            'professional_postal_code' => $collab1Address[1],
            'professional_city' => $collab1Address[2],
            'professional_phone' => '06.34.56.78.90',
            'legal_mentions' => "Assurance RC Professionnelle Generali\nTVA non applicable - Article 293 B du CGI",
            'footer_text' => "Merci pour votre confiance.",
        ]);

        $collab2Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab2Address[0],
            'professional_postal_code' => $collab2Address[1],
            'professional_city' => $collab2Address[2],
            'legal_mentions' => "TVA non applicable - Article 293 B du CGI",
            'footer_text' => "À bientôt !",
        ]);

        $collab3Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab3Address[0],
            'professional_postal_code' => $collab3Address[1],
            'professional_city' => $collab3Address[2],
        ]);

        // Collaborateurs équipe Marketing (Manager: Pierre Martin)
        $collab4Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab4Address[0],
            'professional_postal_code' => $collab4Address[1],
            'professional_city' => $collab4Address[2],
        ]);

        $collab5Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab5Address[0],
            'professional_postal_code' => $collab5Address[1],
            'professional_city' => $collab5Address[2],
        ]);

        // Collaborateurs équipe RH (Manager: Sophie Leroy)
        $collab6Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab6Address[0],
            'professional_postal_code' => $collab6Address[1],
            'professional_city' => $collab6Address[2],
        ]);

        $collab7Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab7Address[0],
            'professional_postal_code' => $collab7Address[1],
            'professional_city' => $collab7Address[2],
        ]);

        // Collaborateurs sans manager direct
        $collab8Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab8Address[0],
            'professional_postal_code' => $collab8Address[1],
            'professional_city' => $collab8Address[2],
        ]);

        $collab9Address = $this->getRandomAddress();
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
            // Infos professionnelles
            'rsac_number' => $this->generateRSAC(),
            'professional_address' => $collab9Address[0],
            'professional_postal_code' => $collab9Address[1],
            'professional_city' => $collab9Address[2],
        ]);

        $this->command->info('✅ Utilisateurs créés avec succès avec leurs informations professionnelles !');
        $this->command->info('📧 Admin: admin@intranet.com / password');
        $this->command->info('📧 Managers: marie.dupont@intranet.com, pierre.martin@intranet.com, sophie.leroy@intranet.com / password');
        $this->command->info('📧 Collaborateurs: jean.moreau@intranet.com, claire.bernard@intranet.com, etc. / password');
    }
}