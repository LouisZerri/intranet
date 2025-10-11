<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Formation;
use App\Models\FormationFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FormationFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le répertoire de stockage s'il n'existe pas
        if (!Storage::disk('public')->exists('formations')) {
            Storage::disk('public')->makeDirectory('formations');
        }

        // Récupérer quelques formations existantes
        $formations = Formation::take(5)->get();

        if ($formations->isEmpty()) {
            $this->command->warn('Aucune formation trouvée. Veuillez d\'abord exécuter le seeder des formations.');
            return;
        }

        // Fichiers d'exemple à créer
        $sampleFiles = [
            // Documents
            [
                'original_name' => 'Manuel_de_formation.pdf',
                'filename' => 'manuel_formation_' . uniqid() . '.pdf',
                'mime_type' => 'application/pdf',
                'type' => 'document',
                'size' => 2548736, // ~2.5MB
                'description' => 'Manuel complet de la formation avec tous les exercices pratiques',
            ],
            [
                'original_name' => 'Presentation_cours.pptx',
                'filename' => 'presentation_' . uniqid() . '.pptx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'type' => 'document',
                'size' => 5242880, // 5MB
                'description' => 'Slides de présentation du cours principal',
            ],
            [
                'original_name' => 'Exercices_pratiques.docx',
                'filename' => 'exercices_' . uniqid() . '.docx',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'type' => 'document',
                'size' => 1048576, // 1MB
                'description' => 'Ensemble d\'exercices pratiques à réaliser',
            ],

            // Vidéos
            [
                'original_name' => 'Introduction_formation.mp4',
                'filename' => 'intro_' . uniqid() . '.mp4',
                'mime_type' => 'video/mp4',
                'type' => 'video',
                'size' => 52428800, // 50MB
                'description' => 'Vidéo d\'introduction à la formation (15 minutes)',
            ],
            [
                'original_name' => 'Demo_pratique.mp4',
                'filename' => 'demo_' . uniqid() . '.mp4',
                'mime_type' => 'video/mp4',
                'type' => 'video',
                'size' => 104857600, // 100MB
                'description' => 'Démonstration pratique des concepts clés',
            ],

            // Audio
            [
                'original_name' => 'Podcast_expert.mp3',
                'filename' => 'podcast_' . uniqid() . '.mp3',
                'mime_type' => 'audio/mpeg',
                'type' => 'audio',
                'size' => 15728640, // 15MB
                'description' => 'Interview avec un expert du domaine',
            ],
            [
                'original_name' => 'Audio_cours.m4a',
                'filename' => 'audio_cours_' . uniqid() . '.m4a',
                'mime_type' => 'audio/mp4',
                'type' => 'audio',
                'size' => 8388608, // 8MB
                'description' => 'Version audio du cours pour écoute mobile',
            ],

            // Images
            [
                'original_name' => 'Schema_processus.png',
                'filename' => 'schema_' . uniqid() . '.png',
                'mime_type' => 'image/png',
                'type' => 'image',
                'size' => 524288, // 512KB
                'description' => 'Schéma explicatif du processus principal',
            ],
            [
                'original_name' => 'Infographie_stats.jpg',
                'filename' => 'infographie_' . uniqid() . '.jpg',
                'mime_type' => 'image/jpeg',
                'type' => 'image',
                'size' => 1048576, // 1MB
                'description' => 'Infographie avec les statistiques importantes',
            ],

            // Archives
            [
                'original_name' => 'Ressources_complementaires.zip',
                'filename' => 'ressources_' . uniqid() . '.zip',
                'mime_type' => 'application/zip',
                'type' => 'archive',
                'size' => 10485760, // 10MB
                'description' => 'Archive contenant toutes les ressources complémentaires',
            ],

            // Autres
            [
                'original_name' => 'Donnees_exemple.csv',
                'filename' => 'donnees_' . uniqid() . '.csv',
                'mime_type' => 'text/csv',
                'type' => 'other',
                'size' => 262144, // 256KB
                'description' => 'Fichier de données d\'exemple pour les exercices',
            ],
            [
                'original_name' => 'Configuration_outil.json',
                'filename' => 'config_' . uniqid() . '.json',
                'mime_type' => 'application/json',
                'type' => 'other',
                'size' => 4096, // 4KB
                'description' => 'Fichier de configuration pour l\'outil de formation',
            ],
        ];

        $this->command->info('Création des fichiers de formation...');

        foreach ($formations as $index => $formation) {
            // Créer le répertoire spécifique à la formation
            $formationDir = "formations/{$formation->id}/files";
            if (!Storage::disk('public')->exists($formationDir)) {
                Storage::disk('public')->makeDirectory($formationDir);
            }

            // Ajouter 3-6 fichiers par formation
            $numFiles = rand(3, 6);
            $selectedFiles = collect($sampleFiles)->random($numFiles);

            $sortOrder = 1;
            foreach ($selectedFiles as $fileData) {
                // Créer le chemin complet
                $filePath = $formationDir . '/' . $fileData['filename'];
                
                // Créer un fichier factice (pour la démo)
                $content = $this->generateDummyFileContent($fileData['type'], $fileData['original_name']);
                Storage::disk('public')->put($filePath, $content);

                // Créer l'enregistrement en base
                FormationFile::create([
                    'formation_id' => $formation->id,
                    'original_name' => $fileData['original_name'],
                    'filename' => $fileData['filename'],
                    'path' => $filePath,
                    'mime_type' => $fileData['mime_type'],
                    'size' => $fileData['size'],
                    'type' => $fileData['type'],
                    'description' => $fileData['description'],
                    'sort_order' => $sortOrder++,
                    'is_public' => rand(0, 10) > 1, // 90% des fichiers sont publics
                ]);

                $this->command->info("Fichier créé: {$fileData['original_name']} pour la formation {$formation->title}");
            }
        }

        $totalFiles = FormationFile::count();
        $this->command->info("Seeding terminé: {$totalFiles} fichiers créés pour " . $formations->count() . " formations.");
    }

    /**
     * Générer un contenu factice pour les fichiers de démo
     */
    private function generateDummyFileContent(string $type, string $filename): string
    {
        switch ($type) {
            case 'document':
                return $this->generateDocumentContent($filename);
            case 'video':
                return $this->generateMediaContent('video', $filename);
            case 'audio':
                return $this->generateMediaContent('audio', $filename);
            case 'image':
                return $this->generateImageContent($filename);
            case 'archive':
                return $this->generateArchiveContent($filename);
            default:
                return $this->generateTextContent($filename);
        }
    }

    private function generateDocumentContent(string $filename): string
    {
        return "Ceci est un fichier de démonstration: {$filename}\n\n" .
               "Ce fichier a été généré automatiquement par le seeder.\n" .
               "Dans un environnement réel, ce serait un vrai document PDF, Word, ou PowerPoint.\n\n" .
               "Contenu de formation:\n" .
               "- Introduction aux concepts\n" .
               "- Exercices pratiques\n" .
               "- Études de cas\n" .
               "- Ressources supplémentaires\n\n" .
               "Généré le: " . now()->format('d/m/Y H:i:s');
    }

    private function generateMediaContent(string $mediaType, string $filename): string
    {
        return "FICHIER DE DÉMONSTRATION - {$mediaType}\n" .
               "Nom: {$filename}\n" .
               "Type: {$mediaType}\n" .
               "Durée estimée: " . rand(5, 60) . " minutes\n" .
               "Qualité: HD\n\n" .
               "Dans un environnement réel, ce serait un fichier {$mediaType} fonctionnel.\n" .
               "Ce fichier factice a été généré par le seeder pour les tests.\n\n" .
               str_repeat("Données {$mediaType} factices...\n", 10);
    }

    private function generateImageContent(string $filename): string
    {
        // Générer une "image" SVG simple comme contenu factice
        return '<?xml version="1.0" encoding="UTF-8"?>
<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
    <rect width="200" height="200" fill="#f3f4f6"/>
    <text x="100" y="100" text-anchor="middle" fill="#374151" font-family="Arial, sans-serif" font-size="12">
        Image de démo
    </text>
    <text x="100" y="120" text-anchor="middle" fill="#6b7280" font-family="Arial, sans-serif" font-size="10">
        ' . $filename . '
    </text>
    <text x="100" y="140" text-anchor="middle" fill="#9ca3af" font-family="Arial, sans-serif" font-size="8">
        Généré le ' . now()->format('d/m/Y') . '
    </text>
</svg>';
    }

    private function generateArchiveContent(string $filename): string
    {
        return "ARCHIVE DE DÉMONSTRATION\n" .
               "Nom: {$filename}\n" .
               "Type: Archive ZIP\n\n" .
               "Contenu simulé de l'archive:\n" .
               "- dossier_1/\n" .
               "  - document1.pdf\n" .
               "  - document2.docx\n" .
               "- dossier_2/\n" .
               "  - image1.jpg\n" .
               "  - image2.png\n" .
               "- readme.txt\n\n" .
               "Cette archive factice a été générée pour les tests.\n" .
               str_repeat("Données d'archive simulées...\n", 20);
    }

    private function generateTextContent(string $filename): string
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        
        switch ($extension) {
            case 'csv':
                return "nom,email,role,date_inscription\n" .
                       "Jean Dupont,jean@example.com,participant," . now()->subDays(10)->format('Y-m-d') . "\n" .
                       "Marie Martin,marie@example.com,formateur," . now()->subDays(5)->format('Y-m-d') . "\n" .
                       "Pierre Durand,pierre@example.com,participant," . now()->subDays(2)->format('Y-m-d') . "\n";
                       
            case 'json':
                return json_encode([
                    'formation_config' => [
                        'version' => '1.0',
                        'modules' => [
                            ['id' => 1, 'name' => 'Introduction', 'duration' => 30],
                            ['id' => 2, 'name' => 'Pratique', 'duration' => 60],
                            ['id' => 3, 'name' => 'Évaluation', 'duration' => 20]
                        ],
                        'created_at' => now()->toISOString()
                    ]
                ], JSON_PRETTY_PRINT);
                
            default:
                return "Fichier de démonstration: {$filename}\n\n" .
                       "Ce fichier texte a été généré automatiquement.\n" .
                       "Contenu factice pour les tests du système de formation.\n\n" .
                       "Généré le: " . now()->format('d/m/Y H:i:s') . "\n" .
                       str_repeat("Ligne de contenu factice numéro " . rand(1, 100) . "\n", 10);
        }
    }
}