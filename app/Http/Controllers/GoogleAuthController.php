<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GoogleAuthController extends Controller
{
    private function getClient(): Client
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/oauth_credentials.json'));
        $client->setRedirectUri(route('google.callback'));
        $client->addScope(Drive::DRIVE_FILE);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        
        // Charger le token existant s'il existe
        $tokenPath = storage_path('app/google/token.json');
        if (file_exists($tokenPath)) {
            $token = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($token);
            
            // Rafraîchir le token si expiré
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                }
            }
        }
        
        return $client;
    }

    public function redirect()
    {
        $client = $this->getClient();
        
        // Vérifier si on a déjà un token valide
        $tokenPath = storage_path('app/google/token.json');
        if (file_exists($tokenPath)) {
            $token = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($token);
            
            if (!$client->isAccessTokenExpired()) {
                return redirect()->route('recruitment.index')
                    ->with('success', 'Déjà connecté à Google Drive !');
            }
        }
        
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('recruitment.index')
                ->with('error', 'Erreur d\'autorisation Google: ' . $request->get('error'));
        }

        $client = $this->getClient();
        
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
        
        if (isset($token['error'])) {
            return redirect()->route('recruitment.index')
                ->with('error', 'Erreur lors de la récupération du token: ' . $token['error']);
        }
        
        $client->setAccessToken($token);
        
        // Sauvegarder le token
        $tokenPath = storage_path('app/google/token.json');
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        
        return redirect()->route('recruitment.index')
            ->with('success', 'Connexion à Google Drive réussie ! Vous pouvez maintenant uploader des documents.');
    }
}