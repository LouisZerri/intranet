<?php

namespace App\Http\Controllers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    /**
     * Initialise et retourne un client Google API authentifié.
     */
    private function getClient(): Client
    {
        $client = new Client();
        // Chemin du fichier d'identifiants OAuth
        $client->setAuthConfig(storage_path('app/google/oauth_credentials.json'));
        // URI de redirection après authentification Google
        $client->setRedirectUri(route('google.callback'));
        $client->addScope(Drive::DRIVE_FILE); // Accès aux fichiers Drive de l'utilisateur
        $client->setAccessType('offline');
        $client->setPrompt('consent'); // Forcer le consentement à chaque fois
        
        // Tenter de charger le token déjà stocké (si disponible)
        $tokenPath = storage_path('app/google/token.json');
        if (file_exists($tokenPath)) {
            $token = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($token);
            
            // Rafraichir le token si expiré
            if ($client->isAccessTokenExpired()) {
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                    file_put_contents($tokenPath, json_encode($client->getAccessToken()));
                }
            }
        }
        
        return $client;
    }

    /**
     * Redirige l'utilisateur vers la page de connexion Google (ou court-circuite si déjà connecté)
     */
    public function redirect()
    {
        $client = $this->getClient();
        
        $tokenPath = storage_path('app/google/token.json');
        if (file_exists($tokenPath)) {
            $token = json_decode(file_get_contents($tokenPath), true);
            $client->setAccessToken($token);
            
            // Si token valide, ne pas ouvrir la popin Google
            if (!$client->isAccessTokenExpired()) {
                return redirect()->route('recruitment.index')
                    ->with('success', 'Déjà connecté à Google Drive !');
            }
        }
        
        // Première connexion ou token expiré
        $authUrl = $client->createAuthUrl();
        return redirect($authUrl);
    }

    /**
     * Callback après autorisation Google, enregistre le token reçu
     */
    public function callback(Request $request)
    {
        // Gestion du cas d'erreur côté Google
        if ($request->has('error')) {
            return redirect()->route('recruitment.index')
                ->with('error', 'Erreur d\'autorisation Google: ' . $request->get('error'));
        }

        $client = $this->getClient();
        // Échange le code reçu contre un token d'accès
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
        
        // Gestion d'une éventuelle erreur
        if (isset($token['error'])) {
            return redirect()->route('recruitment.index')
                ->with('error', 'Erreur lors de la récupération du token: ' . $token['error']);
        }
        
        $client->setAccessToken($token);
        
        // Sauvegarde locale du token pour les prochaines utilisations
        $tokenPath = storage_path('app/google/token.json');
        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        
        return redirect()->route('recruitment.index')
            ->with('success', 'Connexion à Google Drive réussie ! Vous pouvez maintenant uploader des documents.');
    }
}