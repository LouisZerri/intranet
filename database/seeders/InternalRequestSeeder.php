<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InternalRequest;
use App\Models\User;

class InternalRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Récupération des utilisateurs
        $admin = User::where('role', 'administrateur')->first();
        $managers = User::where('role', 'manager')->get();
        $collaborateurs = User::where('role', 'collaborateur')->get();

        // === DEMANDES D'ACHAT PRODUIT COMMUNICATION ===

        // Demande validée et terminée
        InternalRequest::create([
            'type' => 'achat_produit_communication',
            'title' => 'Flyers campagne été 2025',
            'description' => 'Commande de 5000 flyers pour la campagne marketing été 2025. Format A5, impression recto-verso, papier 250g.',
            'comments' => 'Urgence pour la campagne qui démarre lundi prochain.',
            'status' => 'termine',
            'requested_by' => $collaborateurs->where('department', 'Marketing')->first()->id,
            'approved_by' => $managers->where('department', 'Marketing')->first()->id,
            'assigned_to' => $admin->id,
            'requested_at' => now()->subDays(8),
            'approved_at' => now()->subDays(6),
            'completed_at' => now()->subDays(2),
            'estimated_cost' => 450.00,
        ]);

        // Demande en attente
        InternalRequest::create([
            'type' => 'achat_produit_communication',
            'title' => 'Banderoles salon professionnel',
            'description' => 'Commande de 3 banderoles pour le salon professionnel de septembre : 1 kakémono 2x1m et 2 banderoles 4x1m.',
            'comments' => 'Prévoir les visuels de la nouvelle charte graphique.',
            'status' => 'en_attente',
            'requested_by' => $collaborateurs->where('department', 'Commercial')->first()->id,
            'requested_at' => now()->subDays(3),
            'estimated_cost' => 280.00,
        ]);

        // Demande rejetée
        InternalRequest::create([
            'type' => 'achat_produit_communication',
            'title' => 'Objets publicitaires stylos personnalisés',
            'description' => 'Commande de 1000 stylos personnalisés avec logo entreprise pour distribution prospects.',
            'status' => 'rejete',
            'rejection_reason' => 'Budget communication déjà dépassé ce mois. Reporter à septembre.',
            'requested_by' => $collaborateurs->where('department', 'Commercial')->skip(1)->first()->id,
            'approved_by' => $managers->where('department', 'Commercial')->first()->id,
            'requested_at' => now()->subDays(5),
            'approved_at' => now()->subDays(4),
            'estimated_cost' => 320.00,
        ]);

        // === DEMANDES DOCUMENTATION MANAGER ===

        // Demande validée en cours
        InternalRequest::create([
            'type' => 'documentation_manager',
            'title' => 'Guide procédures recrutement 2025',
            'description' => 'Création d\'un guide complet des procédures de recrutement mis à jour avec les nouvelles réglementations 2025.',
            'comments' => 'Inclure les nouvelles obligations légales et les templates d\'entretien.',
            'status' => 'en_cours',
            'requested_by' => $managers->where('department', 'RH')->first()->id,
            'approved_by' => $admin->id,
            'assigned_to' => $collaborateurs->where('department', 'RH')->first()->id,
            'requested_at' => now()->subDays(12),
            'approved_at' => now()->subDays(10),
            'estimated_cost' => 0,
        ]);

        // Demande en attente
        InternalRequest::create([
            'type' => 'documentation_manager',
            'title' => 'Manuel onboarding nouveaux collaborateurs',
            'description' => 'Refonte complète du manuel d\'accueil des nouveaux collaborateurs avec organigramme, processus et outils.',
            'status' => 'en_attente',
            'requested_by' => $managers->where('department', 'Commercial')->first()->id,
            'requested_at' => now()->subDays(2),
        ]);

        // === DEMANDES PRESTATIONS ===

        // Prestation ménage validée
        InternalRequest::create([
            'type' => 'prestation',
            'prestation_type' => 'menage',
            'title' => 'Nettoyage complet bureaux étage 2',
            'description' => 'Nettoyage en profondeur de tous les bureaux de l\'étage 2 suite aux travaux de rénovation.',
            'comments' => 'Prévoir produits spéciaux pour traces de peinture et poussière de plâtre.',
            'status' => 'valide',
            'requested_by' => $collaborateurs->where('department', 'IT')->first()->id,
            'approved_by' => $admin->id,
            'requested_at' => now()->subDays(4),
            'approved_at' => now()->subDays(3),
            'estimated_cost' => 380.00,
        ]);

        // Prestation travaux en cours
        InternalRequest::create([
            'type' => 'prestation',
            'prestation_type' => 'travaux',
            'title' => 'Installation nouveau système de climatisation',
            'description' => 'Installation d\'un système de climatisation dans la salle de réunion principale (30m²).',
            'comments' => 'Travaux à effectuer pendant les heures de fermeture pour ne pas déranger.',
            'status' => 'en_cours',
            'requested_by' => $admin->id,
            'approved_by' => $admin->id,
            'assigned_to' => $admin->id,
            'requested_at' => now()->subDays(15),
            'approved_at' => now()->subDays(14),
            'estimated_cost' => 1200.00,
        ]);

        // Prestation location en attente
        InternalRequest::create([
            'type' => 'prestation',
            'prestation_type' => 'location',
            'title' => 'Location véhicule déplacement client',
            'description' => 'Location d\'un véhicule utilitaire pour transport matériel chez client important (présentation produits).',
            'comments' => 'Besoin pour le 15 août, durée 2 jours.',
            'status' => 'en_attente',
            'requested_by' => $collaborateurs->where('department', 'Commercial')->last()->id,
            'requested_at' => now()->subDays(1),
            'estimated_cost' => 150.00,
        ]);

        // Prestation syndic terminée
        InternalRequest::create([
            'type' => 'prestation',
            'prestation_type' => 'syndic',
            'title' => 'Gestion incident ascenseur principal',
            'description' => 'Déclaration et suivi de la panne de l\'ascenseur principal auprès du syndic de l\'immeuble.',
            'status' => 'termine',
            'requested_by' => $collaborateurs->where('department', 'Comptabilité')->first()->id,
            'approved_by' => $admin->id,
            'assigned_to' => $admin->id,
            'requested_at' => now()->subDays(10),
            'approved_at' => now()->subDays(9),
            'completed_at' => now()->subDays(3),
        ]);

        // Prestation autres administratifs
        InternalRequest::create([
            'type' => 'prestation',
            'prestation_type' => 'autres_administratifs',
            'title' => 'Mise à jour assurance responsabilité civile',
            'description' => 'Renouvellement et mise à jour du contrat d\'assurance responsabilité civile entreprise.',
            'comments' => 'Vérifier que toutes les nouvelles activités sont bien couvertes.',
            'status' => 'valide',
            'requested_by' => $managers->where('department', 'RH')->first()->id,
            'approved_by' => $admin->id,
            'requested_at' => now()->subDays(6),
            'approved_at' => now()->subDays(5),
            'estimated_cost' => 850.00,
        ]);

        // Demandes supplémentaires pour les statistiques
        foreach (['achat_produit_communication', 'documentation_manager', 'prestation'] as $type) {
            for ($i = 0; $i < 3; $i++) {
                $requestedBy = $collaborateurs->random();
                $status = collect(['en_attente', 'valide', 'rejete', 'en_cours', 'termine'])->random();
                
                $data = [
                    'type' => $type,
                    'title' => 'Demande test ' . $type . ' #' . ($i + 1),
                    'description' => 'Description de test pour ' . $type,
                    'status' => $status,
                    'requested_by' => $requestedBy->id,
                    'requested_at' => now()->subDays(rand(1, 30)),
                    'estimated_cost' => rand(50, 500),
                ];

                if ($type === 'prestation') {
                    $data['prestation_type'] = collect(['location', 'syndic', 'menage', 'travaux', 'autres_administratifs'])->random();
                }

                if (in_array($status, ['valide', 'rejete', 'en_cours', 'termine'])) {
                    $data['approved_by'] = $requestedBy->manager_id ?? $admin->id;
                    $data['approved_at'] = now()->subDays(rand(1, 25));
                }

                if ($status === 'rejete') {
                    $data['rejection_reason'] = 'Raison de test pour rejet de demande.';
                }

                if (in_array($status, ['en_cours', 'termine'])) {
                    $data['assigned_to'] = $admin->id;
                }

                if ($status === 'termine') {
                    $data['completed_at'] = now()->subDays(rand(1, 20));
                }

                InternalRequest::create($data);
            }
        }

        $this->command->info('Demandes internes créées avec succès !');
        $this->command->info('- Demandes d\'achat produit communication');
        $this->command->info('- Demandes de documentation manager');
        $this->command->info('- Prestations (location, syndic, ménage, travaux, autres)');
        $this->command->info('- Différents statuts pour tester le workflow');
        $this->command->info('- Demandes avec coûts estimés pour les KPI');
    }
}