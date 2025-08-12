<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Resource;

class ResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le répertoire de ressources s'il n'existe pas
        if (!Storage::exists('documentation/resources')) {
            Storage::makeDirectory('documentation/resources');
        }

        // Créer des fichiers d'exemple (simulation)
        $this->createSampleFiles();

        $resources = [
            // Catégorie Modèles
            [
                'name' => 'Modèle de Contrat Commercial',
                'description' => 'Modèle type de contrat commercial avec clauses standard. À adapter selon les besoins spécifiques de chaque client.',
                'category' => 'modèles',
                'file_path' => 'documentation/resources/modele_contrat_commercial.pdf',
                'original_filename' => 'modele_contrat_commercial.pdf',
                'file_size' => 245760, // ~240KB
                'mime_type' => 'application/pdf',
                'download_count' => 15,
                'is_active' => true
            ],
            [
                'name' => 'Template Rapport Mensuel',
                'description' => 'Template Word pour les rapports mensuels d\'activité. Contient tous les sections standard requises.',
                'category' => 'modèles',
                'file_path' => 'documentation/resources/template_rapport_mensuel.docx',
                'original_filename' => 'template_rapport_mensuel.docx',
                'file_size' => 52480, // ~50KB
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'download_count' => 23,
                'is_active' => true
            ],
            [
                'name' => 'Fiche de Frais - Modèle Excel',
                'description' => 'Tableau Excel pour la saisie des notes de frais avec calculs automatiques et validation.',
                'category' => 'modèles',
                'file_path' => 'documentation/resources/fiche_frais_template.xlsx',
                'original_filename' => 'fiche_frais_template.xlsx',
                'file_size' => 28672, // ~28KB
                'mime_type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'download_count' => 31,
                'is_active' => true
            ],

            // Catégorie Procédures
            [
                'name' => 'Procédure de Sauvegarde IT',
                'description' => 'Guide complet des procédures de sauvegarde informatique, planning et vérifications.',
                'category' => 'procédures',
                'file_path' => 'documentation/resources/procedure_sauvegarde_it.pdf',
                'original_filename' => 'procedure_sauvegarde_it.pdf',
                'file_size' => 1048576, // 1MB
                'mime_type' => 'application/pdf',
                'download_count' => 8,
                'is_active' => true
            ],
            [
                'name' => 'Guide d\'Accueil Nouveaux Employés',
                'description' => 'Processus d\'intégration complet pour les nouveaux collaborateurs, checklist et documents nécessaires.',
                'category' => 'procédures',
                'file_path' => 'documentation/resources/guide_accueil_nouveaux_employes.pdf',
                'original_filename' => 'guide_accueil_nouveaux_employes.pdf',
                'file_size' => 735232, // ~720KB
                'mime_type' => 'application/pdf',
                'download_count' => 12,
                'is_active' => true
            ],

            // Catégorie Formulaires
            [
                'name' => 'Demande de Congés',
                'description' => 'Formulaire officiel pour les demandes de congés payés, RTT et congés exceptionnels.',
                'category' => 'formulaires',
                'file_path' => 'documentation/resources/formulaire_demande_conges.pdf',
                'original_filename' => 'formulaire_demande_conges.pdf',
                'file_size' => 163840, // 160KB
                'mime_type' => 'application/pdf',
                'download_count' => 45,
                'is_active' => true
            ],
            [
                'name' => 'Déclaration d\'Incident Sécurité',
                'description' => 'Formulaire à remplir en cas d\'incident de sécurité informatique ou physique.',
                'category' => 'formulaires',
                'file_path' => 'documentation/resources/declaration_incident_securite.pdf',
                'original_filename' => 'declaration_incident_securite.pdf',
                'file_size' => 98304, // 96KB
                'mime_type' => 'application/pdf',
                'download_count' => 3,
                'is_active' => true
            ],

            // Catégorie Guides
            [
                'name' => 'Guide Utilisation Intranet',
                'description' => 'Manuel d\'utilisation complet de l\'intranet avec captures d\'écran et tutoriels pas à pas.',
                'category' => 'guides',
                'file_path' => 'documentation/resources/guide_utilisation_intranet.pdf',
                'original_filename' => 'guide_utilisation_intranet.pdf',
                'file_size' => 2097152, // 2MB
                'mime_type' => 'application/pdf',
                'download_count' => 67,
                'is_active' => true
            ],
            [
                'name' => 'Guide Sécurité Informatique',
                'description' => 'Bonnes pratiques de sécurité informatique : mots de passe, emails, navigation web, télétravail.',
                'category' => 'guides',
                'file_path' => 'documentation/resources/guide_securite_informatique.pdf',
                'original_filename' => 'guide_securite_informatique.pdf',
                'file_size' => 1572864, // 1.5MB
                'mime_type' => 'application/pdf',
                'download_count' => 34,
                'is_active' => true
            ],

            // Catégorie Règlements
            [
                'name' => 'Règlement Intérieur',
                'description' => 'Règlement intérieur de l\'entreprise : horaires, congés, télétravail, discipline.',
                'category' => 'règlements',
                'file_path' => 'documentation/resources/reglement_interieur.pdf',
                'original_filename' => 'reglement_interieur.pdf',
                'file_size' => 524288, // 512KB
                'mime_type' => 'application/pdf',
                'download_count' => 28,
                'is_active' => true
            ],
            [
                'name' => 'Charte Informatique',
                'description' => 'Charte d\'utilisation des moyens informatiques et des systèmes d\'information.',
                'category' => 'règlements',
                'file_path' => 'documentation/resources/charte_informatique.pdf',
                'original_filename' => 'charte_informatique.pdf',
                'file_size' => 196608, // 192KB
                'mime_type' => 'application/pdf',
                'download_count' => 19,
                'is_active' => true
            ]
        ];

        foreach ($resources as $resource) {
            Resource::create($resource);
        }
    }

    /**
     * Créer des fichiers d'exemple pour la démonstration
     */
    private function createSampleFiles(): void
    {
        $sampleFiles = [
            'modele_contrat_commercial.pdf',
            'template_rapport_mensuel.docx',
            'fiche_frais_template.xlsx',
            'procedure_sauvegarde_it.pdf',
            'guide_accueil_nouveaux_employes.pdf',
            'formulaire_demande_conges.pdf',
            'declaration_incident_securite.pdf',
            'guide_utilisation_intranet.pdf',
            'guide_securite_informatique.pdf',
            'reglement_interieur.pdf',
            'charte_informatique.pdf'
        ];

        foreach ($sampleFiles as $filename) {
            $path = 'documentation/resources/' . $filename;
            if (!Storage::exists($path)) {
                // Créer un fichier texte de démonstration
                $content = "Ceci est un fichier d'exemple pour la démonstration.\n\n";
                $content .= "Nom du fichier: {$filename}\n";
                $content .= "Créé automatiquement par le seeder.\n";
                $content .= "Date: " . now()->format('d/m/Y H:i:s') . "\n\n";
                $content .= "Dans un environnement réel, ce fichier serait remplacé par le vrai document.";
                
                Storage::put($path, $content);
            }
        }
    }
}