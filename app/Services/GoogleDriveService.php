<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected ?Drive $drive = null;
    protected string $rootFolderId;
    protected bool $isConfigured = false;

    public function __construct()
    {
        $this->rootFolderId = config('services.google.drive_folder_id') ?: env('GOOGLE_DRIVE_FOLDER_ID', '');
        
        $tokenPath = storage_path('app/google/token.json');
        $credentialsPath = storage_path('app/google/oauth_credentials.json');
        
        // Vérifier si OAuth est configuré
        if (!file_exists($credentialsPath)) {
            Log::warning('Google Drive: oauth_credentials.json non trouvé');
            return;
        }
        
        if (!file_exists($tokenPath)) {
            Log::warning('Google Drive: Pas de token. L\'utilisateur doit se connecter via /google/auth');
            return;
        }

        try {
            $client = new Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(Drive::DRIVE_FILE);
            $client->setAccessType('offline');
            
            $token = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($token);
            
            // Rafraîchir le token si expiré
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                    Log::info('Google Drive: Token rafraîchi');
                } else {
                    Log::warning('Google Drive: Token expiré et pas de refresh token');
                    return;
                }
            }

            $this->drive = new Drive($client);
            $this->isConfigured = true;
            Log::info('Google Drive: Service initialisé avec succès');
            
        } catch (\Exception $e) {
            Log::error('Google Drive: Erreur d\'initialisation - ' . $e->getMessage());
        }
    }

    /**
     * Vérifie si le service est configuré et prêt
     */
    public function isReady(): bool
    {
        return $this->isConfigured && $this->drive !== null && !empty($this->rootFolderId);
    }

    /**
     * Récupère ou crée un dossier pour un candidat
     */
    public function getOrCreateCandidateFolder(string $candidateName): string
    {
        if (!$this->isReady()) {
            throw new \Exception('Google Drive non configuré. Connectez-vous via /google/auth');
        }

        $safeName = $this->sanitizeFolderName($candidateName);
        
        Log::info("Google Drive: Recherche/création du dossier '{$safeName}'");
        
        // Chercher si le dossier existe déjà
        $query = "name='{$safeName}' and mimeType='application/vnd.google-apps.folder' and '{$this->rootFolderId}' in parents and trashed=false";
        
        $results = $this->drive->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name)',
        ]);

        if (count($results->files) > 0) {
            Log::info("Google Drive: Dossier trouvé - ID: " . $results->files[0]->id);
            return $results->files[0]->id;
        }

        // Créer le dossier
        $folderMetadata = new DriveFile([
            'name' => $safeName,
            'parents' => [$this->rootFolderId],
            'mimeType' => 'application/vnd.google-apps.folder'
        ]);

        $folder = $this->drive->files->create($folderMetadata, [
            'fields' => 'id'
        ]);

        Log::info("Google Drive: Nouveau dossier créé - ID: " . $folder->id);

        return $folder->id;
    }

    /**
     * Upload un fichier dans un dossier
     */
    public function uploadFile(string $folderId, UploadedFile $file, ?string $customName = null): DriveFile
    {
        if (!$this->isReady()) {
            throw new \Exception('Google Drive non configuré. Connectez-vous via /google/auth');
        }

        $fileName = $customName ?? $file->getClientOriginalName();
        
        Log::info("Google Drive: Upload de '{$fileName}'");
        
        // Supprimer l'ancien fichier si existant
        $this->deleteExistingFile($folderId, $fileName);

        $fileMetadata = new DriveFile([
            'name' => $fileName,
            'parents' => [$folderId]
        ]);

        $uploadedFile = $this->drive->files->create($fileMetadata, [
            'data' => file_get_contents($file->getRealPath()),
            'mimeType' => $file->getClientMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id, name, webViewLink, webContentLink',
        ]);
        
        Log::info("Google Drive: Fichier uploadé - ID: " . $uploadedFile->id);

        return $uploadedFile;
    }

    /**
     * Supprime un fichier existant avec le même nom
     */
    public function deleteExistingFile(string $folderId, string $fileName): void
    {
        if (!$this->isReady()) return;

        try {
            $safeFileName = str_replace("'", "\\'", $fileName);
            $query = "name='{$safeFileName}' and '{$folderId}' in parents and trashed=false";
            
            $results = $this->drive->files->listFiles([
                'q' => $query,
                'fields' => 'files(id)',
            ]);

            foreach ($results->files as $file) {
                $this->drive->files->delete($file->id);
                Log::info("Google Drive: Ancien fichier supprimé - ID: " . $file->id);
            }
        } catch (\Exception $e) {
            Log::warning("Google Drive: Erreur suppression - " . $e->getMessage());
        }
    }

    /**
     * Supprime un fichier par son ID
     */
    public function deleteFile(string $fileId): void
    {
        if (!$this->isReady()) return;

        try {
            $this->drive->files->delete($fileId);
            Log::info("Google Drive: Fichier supprimé - ID: " . $fileId);
        } catch (\Exception $e) {
            Log::warning("Google Drive: Erreur suppression fichier - " . $e->getMessage());
        }
    }

    /**
     * Supprime un dossier
     */
    public function deleteFolder(string $folderId): void
    {
        if (!$this->isReady()) return;

        try {
            // Ne pas supprimer le dossier racine
            if ($folderId === $this->rootFolderId) {
                return;
            }
            $this->drive->files->delete($folderId);
            Log::info("Google Drive: Dossier supprimé - ID: " . $folderId);
        } catch (\Exception $e) {
            Log::warning("Google Drive: Erreur suppression dossier - " . $e->getMessage());
        }
    }

    /**
     * Récupère le lien de visualisation
     */
    public function getFileViewLink(string $fileId): ?string
    {
        if (!$this->isReady()) return null;

        try {
            $file = $this->drive->files->get($fileId, ['fields' => 'webViewLink']);
            return $file->webViewLink;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Nettoie le nom
     */
    private function sanitizeFolderName(string $name): string
    {
        $name = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $name);
        $name = str_replace("'", '', $name);
        return trim($name);
    }
}