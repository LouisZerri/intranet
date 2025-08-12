<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mission;
use App\Models\User;
use Carbon\Carbon;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        // Récupération des utilisateurs
        $admin = User::where('role', 'administrateur')->first();
        $managers = User::where('role', 'manager')->get();
        $collaborateurs = User::where('role', 'collaborateur')->get();

        // Manager Commercial et son équipe
        $managerCommercial = User::where('email', 'marie.dupont@intranet.com')->first();
        $equipeCommercial = User::where('manager_id', $managerCommercial->id)->get();

        // Manager Marketing et son équipe  
        $managerMarketing = User::where('email', 'pierre.martin@intranet.com')->first();
        $equipeMarketing = User::where('manager_id', $managerMarketing->id)->get();

        // Manager RH et son équipe
        $managerRH = User::where('email', 'sophie.leroy@intranet.com')->first();
        $equipeRH = User::where('manager_id', $managerRH->id)->get();

        // === MISSIONS COMMERCIALES ===
        
        // Mission terminée avec CA
        Mission::create([
            'title' => 'Négociation contrat Entreprise ABC',
            'description' => 'Finaliser la négociation du contrat annuel avec l\'entreprise ABC pour un montant de 45K€.',
            'status' => 'termine',
            'priority' => 'haute',
            'assigned_to' => $equipeCommercial->first()->id,
            'created_by' => $managerCommercial->id,
            'manager_id' => $managerCommercial->id,
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
            'assigned_to' => $equipeCommercial->skip(1)->first()->id,
            'created_by' => $managerCommercial->id,
            'manager_id' => $managerCommercial->id,
            'revenue' => 25000.00,
            'start_date' => now()->subDays(5),
            'due_date' => now()->addDays(3),
            'notes' => '8/15 prospects déjà recontactés. 3 rendez-vous programmés cette semaine.',
        ]);

        // Mission en retard
        Mission::create([
            'title' => 'Présentation solution Entreprise XYZ',
            'description' => 'Préparer et présenter notre solution à l\'équipe dirigeante d\'XYZ Corp.',
            'status' => 'en_retard',
            'priority' => 'haute',
            'assigned_to' => $equipeCommercial->last()->id,
            'created_by' => $managerCommercial->id,
            'manager_id' => $managerCommercial->id,
            'revenue' => 60000.00,
            'start_date' => now()->subDays(10),
            'due_date' => now()->subDays(2),
            'notes' => 'Retard dû à la disponibilité du client. Nouvelle date prévue semaine prochaine.',
        ]);

        // === MISSIONS MARKETING ===

        // Mission en attente
        Mission::create([
            'title' => 'Campagne publicitaire réseaux sociaux',
            'description' => 'Concevoir et lancer la campagne publicitaire sur LinkedIn et Facebook pour la rentrée.',
            'status' => 'en_attente',
            'priority' => 'normale',
            'assigned_to' => $equipeMarketing->first()->id,
            'created_by' => $managerMarketing->id,
            'manager_id' => $managerMarketing->id,
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
            'assigned_to' => $equipeMarketing->last()->id,
            'created_by' => $managerMarketing->id,
            'manager_id' => $managerMarketing->id,
            'start_date' => now()->subDays(15),
            'due_date' => now()->subDays(1),
            'completed_at' => now(),
            'notes' => 'Site mis à jour avec succès. Amélioration significative du taux de conversion.',
        ]);

        // === MISSIONS RH ===

        // Mission en cours
        Mission::create([
            'title' => 'Recrutement développeur senior',
            'description' => 'Mener le processus de recrutement pour le poste de développeur senior - équipe IT.',
            'status' => 'en_cours',
            'priority' => 'haute',
            'assigned_to' => $equipeRH->first()->id,
            'created_by' => $managerRH->id,
            'manager_id' => $managerRH->id,
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
            'assigned_to' => $equipeRH->last()->id,
            'created_by' => $managerRH->id,
            'manager_id' => $managerRH->id,
            'start_date' => now()->subDays(12),
            'due_date' => now()->subDays(2),
            'completed_at' => now()->subDays(1),
            'notes' => '100% des collaborateurs formés. Certificats délivrés.',
        ]);

        // === MISSIONS DIVERSES ===

        // Mission IT sans manager spécifique
        $devIT = User::where('email', 'laura.simon@intranet.com')->first();
        Mission::create([
            'title' => 'Migration serveur de fichiers',
            'description' => 'Migrer tous les fichiers partagés vers le nouveau serveur sécurisé.',
            'status' => 'en_cours',
            'priority' => 'haute',
            'assigned_to' => $devIT->id,
            'created_by' => $admin->id,
            'start_date' => now()->subDays(3),
            'due_date' => now()->addDays(7),
            'notes' => '60% des fichiers migrés. Aucun incident signalé.',
        ]);

        // Mission comptabilité
        $comptable = User::where('email', 'antoine.michel@intranet.com')->first();
        Mission::create([
            'title' => 'Clôture comptable mensuelle',
            'description' => 'Effectuer la clôture comptable du mois de juillet et préparer les rapports financiers.',
            'status' => 'termine',
            'priority' => 'normale',
            'assigned_to' => $comptable->id,
            'created_by' => $admin->id,
            'start_date' => now()->subDays(8),
            'due_date' => now()->subDays(3),
            'completed_at' => now()->subDays(2),
            'notes' => 'Clôture effectuée dans les délais. Rapports transmis à la direction.',
        ]);

        // Missions supplémentaires pour avoir plus de données de test
        foreach ($collaborateurs->take(5) as $index => $collaborateur) {
            Mission::create([
                'title' => 'Mission test ' . ($index + 1),
                'description' => 'Description de la mission test ' . ($index + 1) . ' pour générer des données KPI.',
                'status' => collect(['en_attente', 'en_cours', 'termine', 'en_retard'])->random(),
                'priority' => collect(['basse', 'normale', 'haute', 'urgente'])->random(),
                'assigned_to' => $collaborateur->id,
                'created_by' => $collaborateur->manager_id ?? $admin->id,
                'manager_id' => $collaborateur->manager_id,
                'revenue' => rand(5000, 50000),
                'start_date' => now()->subDays(rand(1, 30)),
                'due_date' => now()->addDays(rand(1, 30)),
                'completed_at' => rand(0, 1) ? now()->subDays(rand(1, 10)) : null,
            ]);
        }

        $this->command->info('Missions créées avec succès !');
        $this->command->info('- Missions commerciales avec CA');
        $this->command->info('- Missions marketing et RH');
        $this->command->info('- Missions IT et comptabilité');
        $this->command->info('- Différents statuts : en_attente, en_cours, terminé, en_retard');
        $this->command->info('- Différentes priorités pour les tests KPI');
    }
}