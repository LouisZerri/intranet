<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;
use App\Models\User;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        // Récupération des utilisateurs avec les BONS emails
        $admin = User::where('email', 'admin@gestimmo.fr')->first();
        $managers = User::where('role', 'manager')->get();
        $collaborateurs = User::where('role', 'collaborateur')->get();

        // Manager IDF et son équipe
        $managerIDF = User::where('email', 'marie.dupont@intranet.fr')->first();
        $equipeIDF = User::where('manager_id', $managerIDF->id)->get();

        // Manager PACA et son équipe  
        $managerPACA = User::where('email', 'pierre.bernard@gestimmo.fr')->first();
        $equipePACA = User::where('manager_id', $managerPACA->id)->get();

        // Manager ARA et son équipe
        $managerARA = User::where('email', 'sophie.dubois@gestimmo.fr')->first();
        $equipeARA = User::where('manager_id', $managerARA->id)->get();

        // Manager Occitanie et son équipe
        $managerOccitanie = User::where('email', 'luc.moreau@gestimmo.fr')->first();
        $equipeOccitanie = User::where('manager_id', $managerOccitanie->id)->get();

        // Manager Local et son équipe
        $managerLocal = User::where('email', 'claire.petit@gestimmo.fr')->first();
        $equipeLocal = User::where('manager_id', $managerLocal->id)->get();

        // === MISSIONS COMMERCIALES IDF ===
        
        // Mission terminée avec CA
        Mission::create([
            'title' => 'Négociation contrat Entreprise ABC',
            'description' => 'Finaliser la négociation du contrat annuel avec l\'entreprise ABC pour un montant de 45K€.',
            'status' => 'termine',
            'priority' => 'haute',
            'category' => 'autres',
            'subcategory' => 'prospection_commerciale',
            'assigned_to' => $equipeIDF->first()->id,
            'created_by' => $managerIDF->id,
            'manager_id' => $managerIDF->id,
            'revenue' => 45000.00,
            'start_date' => now()->subDays(20),
            'due_date' => now()->subDays(5),
            'completed_at' => now()->subDays(3),
            'notes' => 'Contrat signé avec succès. Client très satisfait des conditions négociées.',
        ]);

        // Mission en cours priorité urgente
        Mission::create([
            'title' => 'Relance prospects qualifiés Q2',
            'description' => 'Relancer les 15 prospects qualifiés du trimestre Q2 pour finaliser les ventes avant fin de mois.',
            'status' => 'en_cours',
            'priority' => 'urgente',
            'category' => 'autres',
            'subcategory' => 'prospection_commerciale',
            'assigned_to' => $equipeIDF->skip(1)->first()->id,
            'created_by' => $managerIDF->id,
            'manager_id' => $managerIDF->id,
            'revenue' => 25000.00,
            'start_date' => now()->subDays(5),
            'due_date' => now()->addDays(3),
            'notes' => '8/15 prospects déjà recontactés. 3 rendez-vous programmés cette semaine.',
        ]);

        // === MISSIONS PACA ===

        // Mission en retard
        Mission::create([
            'title' => 'Présentation solution Entreprise XYZ',
            'description' => 'Préparer et présenter notre solution à l\'équipe dirigeante d\'XYZ Corp.',
            'status' => 'en_retard',
            'priority' => 'haute',
            'category' => 'autres',
            'subcategory' => 'relation_client',
            'assigned_to' => $equipePACA->first()->id,
            'created_by' => $managerPACA->id,
            'manager_id' => $managerPACA->id,
            'revenue' => 60000.00,
            'start_date' => now()->subDays(10),
            'due_date' => now()->subDays(2),
            'notes' => 'Retard dû à la disponibilité du client. Nouvelle date prévue semaine prochaine.',
        ]);

        // === MISSIONS ARA ===

        // Mission en attente
        Mission::create([
            'title' => 'Campagne publicitaire réseaux sociaux',
            'description' => 'Concevoir et lancer la campagne publicitaire sur LinkedIn et Facebook pour la rentrée.',
            'status' => 'en_attente',
            'priority' => 'normale',
            'category' => 'autres',
            'subcategory' => 'prospection_commerciale',
            'assigned_to' => $equipeARA->first()->id,
            'created_by' => $managerARA->id,
            'manager_id' => $managerARA->id,
            'start_date' => now()->addDays(2),
            'due_date' => now()->addDays(15),
            'notes' => 'En attente de validation du budget par la direction.',
        ]);

        // Mission terminée récemment
        Mission::create([
            'title' => 'Refonte site web - Section produits',
            'description' => 'Mise à jour complète de la section produits du site web avec les nouvelles fiches techniques.',
            'status' => 'termine',
            'priority' => 'normale',
            'category' => 'autres',
            'subcategory' => 'administration',
            'assigned_to' => $equipeARA->last()->id,
            'created_by' => $managerARA->id,
            'manager_id' => $managerARA->id,
            'start_date' => now()->subDays(15),
            'due_date' => now()->subDays(1),
            'completed_at' => now(),
            'notes' => 'Site mis à jour avec succès. Amélioration significative du taux de conversion.',
        ]);

        // === MISSIONS OCCITANIE ===

        // Mission en cours
        Mission::create([
            'title' => 'Recrutement développeur senior',
            'description' => 'Mener le processus de recrutement pour le poste de développeur senior - équipe IT.',
            'status' => 'en_cours',
            'priority' => 'haute',
            'category' => 'autres',
            'subcategory' => 'administration',
            'assigned_to' => $equipeOccitanie->first()->id,
            'created_by' => $managerOccitanie->id,
            'manager_id' => $managerOccitanie->id,
            'start_date' => now()->subDays(7),
            'due_date' => now()->addDays(10),
            'notes' => '12 candidatures reçues. 3 entretiens programmés cette semaine.',
        ]);

        // Mission terminée
        Mission::create([
            'title' => 'Formation sécurité - Tous collaborateurs',
            'description' => 'Organiser et animer la formation sécurité obligatoire pour tous les collaborateurs.',
            'status' => 'termine',
            'priority' => 'normale',
            'category' => 'autres',
            'subcategory' => 'formation_interne',
            'assigned_to' => $equipeOccitanie->last()->id,
            'created_by' => $managerOccitanie->id,
            'manager_id' => $managerOccitanie->id,
            'start_date' => now()->subDays(12),
            'due_date' => now()->subDays(2),
            'completed_at' => now()->subDays(1),
            'notes' => '100% des collaborateurs formés. Certificats délivrés.',
        ]);

        // === MISSIONS ÉQUIPE LOCALE ===

        // Mission en cours
        Mission::create([
            'title' => 'Audit satisfaction clients Lyon',
            'description' => 'Réaliser un audit de satisfaction auprès de nos 50 principaux clients sur Lyon.',
            'status' => 'en_cours',
            'priority' => 'normale',
            'category' => 'autres',
            'subcategory' => 'relation_client',
            'assigned_to' => $equipeLocal->first()->id,
            'created_by' => $managerLocal->id,
            'manager_id' => $managerLocal->id,
            'start_date' => now()->subDays(5),
            'due_date' => now()->addDays(10),
            'notes' => '20 clients contactés. Taux de réponse de 80%.',
        ]);

        // === MISSIONS IMMOBILIÈRES ===

        // Missions Location
        Mission::create([
            'title' => 'Visite appartement 3P - Rue de la Paix',
            'description' => 'Faire visiter l\'appartement 3 pièces situé rue de la Paix à 3 candidats locataires.',
            'status' => 'en_cours',
            'priority' => 'normale',
            'category' => 'location',
            'subcategory' => 'visite_locataire',
            'assigned_to' => $collaborateurs->random()->id,
            'created_by' => $admin->id,
            'start_date' => now()->subDays(2),
            'due_date' => now()->addDays(5),
            'notes' => '2 visites déjà effectuées. 1 candidature reçue.',
        ]);

        Mission::create([
            'title' => 'État des lieux entrée - Appartement Résidence Central',
            'description' => 'Effectuer l\'état des lieux d\'entrée pour le nouvel appartement de la résidence Central.',
            'status' => 'termine',
            'priority' => 'haute',
            'category' => 'location',
            'subcategory' => 'etat_lieux_entree',
            'assigned_to' => $collaborateurs->random()->id,
            'created_by' => $admin->id,
            'revenue' => 800.00,
            'start_date' => now()->subDays(3),
            'due_date' => now()->subDays(1),
            'completed_at' => now(),
            'notes' => 'État des lieux réalisé sans anomalie. Locataire installé.',
        ]);

        Mission::create([
            'title' => 'Recouvrement loyers impayés - M. Durand',
            'description' => 'Procédure de recouvrement pour les loyers impayés de M. Durand (3 mois de retard).',
            'status' => 'en_cours',
            'priority' => 'urgente',
            'category' => 'location',
            'subcategory' => 'recouvrement',
            'assigned_to' => $collaborateurs->random()->id,
            'created_by' => $admin->id,
            'start_date' => now()->subDays(10),
            'due_date' => now()->addDays(5),
            'notes' => 'Mise en demeure envoyée. Rendez-vous programmé avec le locataire.',
        ]);

        // Missions Syndic
        Mission::create([
            'title' => 'AG Copropriété Les Jardins - Préparation',
            'description' => 'Préparer l\'assemblée générale de la copropriété Les Jardins : ordre du jour, convocations, rapports.',
            'status' => 'en_attente',
            'priority' => 'haute',
            'category' => 'syndic',
            'subcategory' => 'ag_copropriete',
            'assigned_to' => $collaborateurs->random()->id,
            'created_by' => $admin->id,
            'start_date' => now()->addDays(3),
            'due_date' => now()->addDays(20),
            'notes' => 'AG prévue le 25 du mois. 45 copropriétaires à convoquer.',
        ]);

        Mission::create([
            'title' => 'Suivi travaux ravalement - Résidence Soleil',
            'description' => 'Superviser les travaux de ravalement de façade de la résidence Soleil.',
            'status' => 'en_cours',
            'priority' => 'normale',
            'category' => 'syndic',
            'subcategory' => 'suivi_travaux',
            'assigned_to' => $collaborateurs->random()->id,
            'created_by' => $admin->id,
            'start_date' => now()->subDays(15),
            'due_date' => now()->addDays(30),
            'notes' => 'Travaux à 40%. Respect du planning. Aucun incident signalé.',
        ]);

        Mission::create([
            'title' => 'Gestion sinistre dégât des eaux - Apt 15',
            'description' => 'Gérer le sinistre dégât des eaux de l\'appartement 15 : expertise, réparations, assurance.',
            'status' => 'en_retard',
            'priority' => 'urgente',
            'category' => 'syndic',
            'subcategory' => 'gestion_sinistre',
            'assigned_to' => $collaborateurs->random()->id,
            'created_by' => $admin->id,
            'start_date' => now()->subDays(7),
            'due_date' => now()->subDays(1),
            'notes' => 'Expert passé. En attente du rapport d\'expertise pour débloquer les réparations.',
        ]);

        // === MISSIONS DIVERSES ===

        // Missions supplémentaires pour avoir plus de données de test
        $categories = ['location', 'syndic', 'autres'];
        $subcategoriesByCategory = [
            'location' => ['visite_locataire', 'etat_lieux_entree', 'etat_lieux_sortie', 'gestion_charges', 'recouvrement'],
            'syndic' => ['ag_copropriete', 'conseil_syndical', 'suivi_travaux', 'gestion_sinistre', 'comptabilite_syndic'],
            'autres' => ['prospection_commerciale', 'formation_interne', 'administration', 'relation_client', 'reporting_direction']
        ];

        foreach ($collaborateurs->take(10) as $index => $collaborateur) {
            $category = $categories[array_rand($categories)];
            $subcategory = $subcategoriesByCategory[$category][array_rand($subcategoriesByCategory[$category])];

            Mission::create([
                'title' => 'Mission test ' . ($index + 1),
                'description' => 'Description de la mission test ' . ($index + 1) . ' pour générer des données KPI.',
                'status' => collect(['en_attente', 'en_cours', 'termine', 'en_retard'])->random(),
                'priority' => collect(['basse', 'normale', 'haute', 'urgente'])->random(),
                'category' => $category,
                'subcategory' => $subcategory,
                'assigned_to' => $collaborateur->id,
                'created_by' => $collaborateur->manager_id ?? $admin->id,
                'manager_id' => $collaborateur->manager_id,
                'revenue' => rand(5000, 50000),
                'start_date' => now()->subDays(rand(1, 30)),
                'due_date' => now()->addDays(rand(1, 30)),
                'completed_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
            ]);
        }

        $this->command->info('✅ Missions créées avec succès !');
        $this->command->info('📊 Missions commerciales IDF avec CA');
        $this->command->info('📊 Missions PACA, ARA, Occitanie');
        $this->command->info('🏢 Missions Location (visites, états des lieux, recouvrement)');
        $this->command->info('🏢 Missions Syndic (AG, travaux, sinistres)');
        $this->command->info('📈 Différents statuts : en_attente, en_cours, terminé, en_retard');
        $this->command->info('🎯 Différentes priorités et catégories pour les tests KPI');
    }
}