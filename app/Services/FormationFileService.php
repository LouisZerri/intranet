<?php

namespace App\Services;

use App\Models\Formation;
use App\Models\FormationFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FormationFileService
{
    private const MAX_FILE_SIZE = 100 * 1024 * 1024; // 100MB
    private const ALLOWED_EXTENSIONS = [
        // Documents
        'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'rtf', 'odt', 'ods', 'odp',
        // Vidéos
        'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm', 'mkv', 'm4v', '3gp',
        // Audio
        'mp3', 'wav', 'wma', 'aac', 'flac', 'ogg', 'm4a', 'opus',
        // Images
        'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff',
        // Archives
        'zip', 'rar', '7z', 'tar', 'gz', 'bz2',
        // Autres
        'csv', 'json', 'xml'
    ];

    /**
     * Valider un fichier uploadé
     */
    public function validateFile(UploadedFile $file): array
    {
        $errors = [];

        // Vérifier la taille
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            $errors[] = "Le fichier {$file->getClientOriginalName()} dépasse la taille maximum de 100MB.";
        }

        // Vérifier l'extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $errors[] = "Le type de fichier {$extension} n'est pas autorisé pour {$file->getClientOriginalName()}.";
        }

        // Vérifier que le fichier n'est pas corrompu
        if ($file->getError() !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'upload du fichier {$file->getClientOriginalName()}.";
        }

        return $errors;
    }

    /**
     * Sauvegarder un fichier pour une formation
     */
    public function storeFile(Formation $formation, UploadedFile $file, ?string $description = null): FormationFile
    {
        // Générer un nom de fichier unique
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        
        // Définir le chemin de stockage
        $path = "formations/{$formation->id}/files/{$filename}";
        
        // Stocker le fichier
        $storedPath = $file->storeAs(
            "formations/{$formation->id}/files",
            $filename,
            'public'
        );

        // Déterminer le type de fichier
        $mimeType = $file->getMimeType();
        $fileType = FormationFile::determineFileType($mimeType);

        // Créer l'enregistrement en base
        return FormationFile::create([
            'formation_id' => $formation->id,
            'original_name' => $originalName,
            'filename' => $filename,
            'path' => $storedPath,
            'mime_type' => $mimeType,
            'size' => $file->getSize(),
            'type' => $fileType,
            'description' => $description,
            'sort_order' => $this->getNextSortOrder($formation),
            'is_public' => true,
        ]);
    }

    /**
     * Traiter plusieurs fichiers en une fois
     */
    public function storeMultipleFiles(Formation $formation, array $files): array
    {
        $results = [
            'success' => [],
            'errors' => []
        ];

        foreach ($files as $file) {
            try {
                // Valider le fichier
                $errors = $this->validateFile($file);
                if (!empty($errors)) {
                    $results['errors'] = array_merge($results['errors'], $errors);
                    continue;
                }

                // Sauvegarder le fichier
                $formationFile = $this->storeFile($formation, $file);
                $results['success'][] = $formationFile;
                
            } catch (\Exception $e) {
                $results['errors'][] = "Erreur lors du traitement de {$file->getClientOriginalName()}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Supprimer un fichier
     */
    public function deleteFile(FormationFile $file): bool
    {
        try {
            // Supprimer le fichier du stockage
            if (Storage::disk('public')->exists($file->path)) {
                Storage::disk('public')->delete($file->path);
            }

            // Supprimer l'enregistrement
            return $file->delete();
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Réorganiser l'ordre des fichiers
     */
    public function reorderFiles(Formation $formation, array $fileIds): bool
    {
        try {
            foreach ($fileIds as $index => $fileId) {
                FormationFile::where('formation_id', $formation->id)
                           ->where('id', $fileId)
                           ->update(['sort_order' => $index + 1]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mettre à jour les métadonnées d'un fichier
     */
    public function updateFileMetadata(FormationFile $file, array $data): bool
    {
        try {
            $updateData = [];
            
            if (isset($data['description'])) {
                $updateData['description'] = $data['description'];
            }
            
            if (isset($data['is_public'])) {
                $updateData['is_public'] = $data['is_public'];
            }
            
            if (isset($data['sort_order'])) {
                $updateData['sort_order'] = $data['sort_order'];
            }

            return $file->update($updateData);
            
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtenir le prochain ordre de tri
     */
    private function getNextSortOrder(Formation $formation): int
    {
        $maxOrder = FormationFile::where('formation_id', $formation->id)
                                ->max('sort_order');
        
        return $maxOrder ? $maxOrder + 1 : 1;
    }

    /**
     * Générer une URL de téléchargement sécurisée
     */
    public function generateSecureDownloadUrl(FormationFile $file): string
    {
        return route('formations.files.download', [
            'file' => $file->id,
            'token' => $this->generateDownloadToken($file)
        ]);
    }

    /**
     * Générer un token de téléchargement
     */
    private function generateDownloadToken(FormationFile $file): string
    {
        return hash('sha256', $file->id . $file->filename . config('app.key'));
    }

    /**
     * Vérifier un token de téléchargement
     */
    public function verifyDownloadToken(FormationFile $file, string $token): bool
    {
        return hash_equals($this->generateDownloadToken($file), $token);
    }

    /**
     * Obtenir les statistiques des fichiers d'une formation
     */
    public function getFormationFilesStats(Formation $formation): array
    {
        $files = $formation->files;
        
        return [
            'total_files' => $files->count(),
            'total_size' => $files->sum('size'),
            'by_type' => $files->groupBy('type')->map->count(),
            'formatted_size' => $this->formatBytes($files->sum('size')),
        ];
    }

    /**
     * Formater les bytes en format lisible
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 Bytes';
        
        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes) / log($k));
        
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }
}