<?php
namespace App\Services\Firebase;

use App\Services\Firebase\FirebaseServiceInterface;
use Kreait\Firebase\Factory;


class FirebaseService implements FirebaseServiceInterface
{
    protected $auth;
    protected $database;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
            ->withDatabaseUri(env('FIREBASE_DATABASE_URL'));

        $this->auth = $firebase->createAuth();
        $this->database = $firebase->createDatabase();
    }

    // Créer un utilisateur dans Firebase
    public function createUser($data)
    {
        try {
            $userProperties = [
                'email' => $data['email'],
                'password' => $data['password'],
                'displayName' => $data['displayName'],
                'disabled' => false,
            ];

            $createdUser = $this->auth->createUser($userProperties);

            return $createdUser;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la création de l\'utilisateur Firebase: ' . $e->getMessage());
        }
    }

    // Ajouter des détails utilisateur à Realtime Database
    public function storeUserDetails($uid, $details)
    {
        try {
            $this->database->getReference('users/' . $uid)->set($details);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'ajout des détails utilisateur: ' . $e->getMessage());
        }
    }


    public function findAll()
    {
        try {
            $users = $this->auth->listUsers();
            $userList = [];

            foreach ($users as $user) {
                $userDetails = $this->database->getReference('users/' . $user->uid)->getValue();
                $userList[] = array_merge(
                    [
                        'uid' => $user->uid,
                        'email' => $user->email,
                        'displayName' => $user->displayName,                    
                    ],
                    $userDetails ?? []
                );
            }

            return $userList;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
        }
    }


    public function updateUser($uid, $data)
    {
        try {
            $updatedUser = $this->auth->updateUser($uid, $data);

            // Mettre à jour les détails supplémentaires dans la base de données
            $this->database->getReference('users/' . $uid)->update($data);

            return $updatedUser;
        } catch (\Exception  $e) {
            throw new \Exception('Utilisateur non trouvé: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage());
        }
    }

    public function findUserByEmail($email)
    {
        try {
            $user = $this->auth->getUserByEmail($email);
            return $user;
        } catch (\Exception  $e) {
            throw new \Exception('Utilisateur non trouvé: '. $e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération de l\'utilisateur: '. $e->getMessage());
        }
    }

}
