<?php

namespace App\Services\Authentication;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthenticationFirebase implements AuthenticationServiceInterface
{
    protected $firebaseApiKey;
    protected $client;

    public function __construct()
    {
        $this->firebaseApiKey = env('FIREBASE_API_KEY');
        $this->client = new Client();
    }

    public function authenticate($request)
    {
        try {
            // Créer un client HTTP
            $client = new Client();

            
            $email = $request['email'] ?? null;
            $password = $request['password'] ?? null;
           
            
            // Vérifier si l'email et le mot de passe sont présents
            if (!$email || !$password) {
                throw new Exception("Email ou mot de passe manquant.");
            }
    
            // Faire une requête POST à l'API Firebase Authentication
            $response = $client->post('https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=' . $this->firebaseApiKey, [
                'json' => [
                    'email' => $email,
                    'password' => $password,
                    'returnSecureToken' => true,
                ],
            ]);
    
            // Décoder la réponse JSON
            $body = json_decode((string) $response->getBody(), true);
    
            // Vérifier si la requête a réussi
            if (!isset($body['idToken'])) {
                throw new Exception('Échec de l\'authentification. Jeton non trouvé.');
            }
    
            // Récupérer le jeton et d'autres informations utilisateur
            $idToken = $body['idToken'];
            $refreshToken = $body['refreshToken'];
            $userInfo = $this->getUserInfo($idToken);

            // Créer ou mettre à jour l'utilisateur dans la base de données locale
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'nom' => $userInfo['displayName'] ?? $email,
                    'firebase_uid' => $userInfo['localId'],
                ]
            );
            
    
            return [
                'success' => true,
                'token' => $idToken,
                'refresh_token' => $refreshToken,
                'user' => $user,
            ];
        } catch (Exception $e) {
            Log::error('Erreur d\'authentification Firebase: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Échec de l\'authentification via Firebase: ' . $e->getMessage(),
            ];
        }
    }
    


    public function register($credentials)
    {
        // Vous pouvez créer un utilisateur avec l'API REST si nécessaire
        // Pour cet exemple, nous traitons l'enregistrement de manière similaire à l'authentification
        return $this->authenticate($credentials);
    }

    public function refreshToken($request)
    {
        // Vous pouvez gérer le rafraîchissement des jetons si nécessaire
        // Pour cet exemple, nous ne gérons pas le rafraîchissement ici
        return [
            'success' => false,
            'message' => 'Le rafraîchissement des jetons n\'est pas pris en charge dans cet exemple.',
        ];
    }

    public function logout($user)
    {
        // La gestion de la déconnexion côté serveur n'est pas nécessaire pour Firebase, car le client peut se déconnecter directement
        return [
            'success' => true,
            'message' => 'Déconnexion réussie.',
        ];
    }

    public function login($credentials)
    {
        
        
    }



    protected function getUserInfo($idToken)
    {
        $response = $this->client->post('https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=' . $this->firebaseApiKey, [
            'json' => [
                'idToken' => $idToken,
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);

        if (!isset($body['users'][0])) {
            throw new Exception('Impossible de récupérer les informations de l\'utilisateur.');
        }

        return $body['users'][0];
    }
}
