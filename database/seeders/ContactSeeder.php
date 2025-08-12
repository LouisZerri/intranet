<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            // Secteur Commercial
            [
                'name' => 'Marie Dubois',
                'position' => 'Directrice Commerciale',
                'company' => 'TechSolutions SARL',
                'sector' => 'commercial',
                'email' => 'marie.dubois@techsolutions.fr',
                'phone' => '01 42 56 78 90',
                'mobile' => '06 12 34 56 78',
                'address' => "25 Avenue des Champs-Élysées\n75008 Paris",
                'notes' => 'Contact principal pour les gros contrats. Disponible du lundi au vendredi 9h-18h.',
                'is_active' => true
            ],
            [
                'name' => 'Pierre Martin',
                'position' => 'Chef des Ventes',
                'company' => 'CommerceFirst',
                'sector' => 'commercial',
                'email' => 'p.martin@commercefirst.com',
                'phone' => '01 45 67 89 01',
                'mobile' => '06 23 45 67 89',
                'address' => "15 Rue de Rivoli\n75001 Paris",
                'notes' => 'Spécialiste des solutions B2B. Préfère être contacté par email.',
                'is_active' => true
            ],
            
            // Secteur Technique
            [
                'name' => 'Sophie Leroy',
                'position' => 'Responsable Technique',
                'company' => 'InnoTech Industries',
                'sector' => 'technique',
                'email' => 'sophie.leroy@innotech.fr',
                'phone' => '01 56 78 90 12',
                'mobile' => '06 34 56 78 90',
                'address' => "45 Boulevard Saint-Germain\n75005 Paris",
                'notes' => 'Expert en intégration système. Urgences uniquement sur mobile.',
                'is_active' => true
            ],
            [
                'name' => 'Jean-Luc Moreau',
                'position' => 'Ingénieur Support',
                'company' => 'TechAssist Pro',
                'sector' => 'technique',
                'email' => 'jl.moreau@techassist.fr',
                'phone' => '01 67 89 01 23',
                'mobile' => '06 45 67 89 01',
                'address' => "78 Avenue de la République\n92100 Boulogne-Billancourt",
                'notes' => 'Support technique 24h/24. Hotline dédiée pour les urgences.',
                'is_active' => true
            ],
            
            // Secteur Juridique
            [
                'name' => 'Maître Valérie Rousseau',
                'position' => 'Avocate d\'Affaires',
                'company' => 'Cabinet Rousseau & Associés',
                'sector' => 'juridique',
                'email' => 'v.rousseau@cabinet-rousseau.fr',
                'phone' => '01 78 90 12 34',
                'mobile' => '06 56 78 90 12',
                'address' => "12 Place Vendôme\n75001 Paris",
                'notes' => 'Spécialisée en droit des contrats et propriété intellectuelle.',
                'is_active' => true
            ],
            [
                'name' => 'Antoine Bernard',
                'position' => 'Juriste d\'Entreprise',
                'company' => 'Legal Conseil',
                'sector' => 'juridique',
                'email' => 'a.bernard@legalconseil.fr',
                'phone' => '01 89 01 23 45',
                'mobile' => '06 67 89 01 23',
                'address' => "88 Avenue Kléber\n75016 Paris",
                'notes' => 'Conseil en droit social et commercial. Consultations sur RDV.',
                'is_active' => true
            ],
            
            // Secteur RH
            [
                'name' => 'Isabelle Fournier',
                'position' => 'Consultante RH',
                'company' => 'RH Excellence',
                'sector' => 'rh',
                'email' => 'i.fournier@rh-excellence.fr',
                'phone' => '01 90 12 34 56',
                'mobile' => '06 78 90 12 34',
                'address' => "33 Rue de la Paix\n75002 Paris",
                'notes' => 'Spécialisée en recrutement et gestion des talents.',
                'is_active' => true
            ],
            
            // Secteur Finance
            [
                'name' => 'Michel Leclerc',
                'position' => 'Expert-Comptable',
                'company' => 'Expertise Comptable Leclerc',
                'sector' => 'finance',
                'email' => 'm.leclerc@expertise-leclerc.fr',
                'phone' => '01 01 23 45 67',
                'mobile' => '06 89 01 23 45',
                'address' => "56 Boulevard Haussmann\n75008 Paris",
                'notes' => 'Tenue de comptabilité et conseil fiscal. Rendez-vous sur demande.',
                'is_active' => true
            ],
            
            // Secteur IT
            [
                'name' => 'Thomas Girard',
                'position' => 'Administrateur Système',
                'company' => 'IT Services Pro',
                'sector' => 'it',
                'email' => 't.girard@itservices.fr',
                'phone' => '01 12 34 56 78',
                'mobile' => '06 90 12 34 56',
                'address' => "22 Rue du Faubourg Saint-Antoine\n75012 Paris",
                'notes' => 'Maintenance serveurs et infrastructures. Astreinte week-end.',
                'is_active' => true
            ]
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }
    }
}