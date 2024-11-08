<?php

namespace App\Services\User;

use App\Models\Role;
use App\Events\PhotoUploaded;
use App\Repositories\User\UserRepository;
use App\Services\Firebase\FirebaseService;
use App\Services\UploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;


class UserServiceImpl implements UserService
{
    protected $userRepository;
    protected $firebaseService;
    protected $uploadService;

    public function __construct(UserRepository $userRepository, FirebaseService $firebaseService,UploadService $uploadService)
    {
        $this->userRepository = $userRepository;
        $this->firebaseService = $firebaseService;
        $this->uploadService = $uploadService;
    }

    public function index($role = null)
    {
        try {
            $users = $this->firebaseService->findAll();

            if ($role !== null) {
                $users = array_filter($users, function($user) use ($role) {
                    return isset($user['role_id']) && $user['role_id'] == $role;
                });
            }

            return $users;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
        }
    }

    public function createUser(array $data)
    {
        // $currentUserRole = Auth::user()->role->nomRole;
        // dd($currentUserRole);
        // $newUserRole = Role::find($data['role_id'])->nomRole;

        // if (!$this->canCreateUser($currentUserRole, $newUserRole)) {
        //     throw new \Exception('Action non autorisée');
        // }

        $firebaseUser = $this->firebaseService->createUser([
            'email' => $data['email'],
            'password' => $data['password'],
            'displayName' => $data['name'] ,
        ]);

        // Vérifier si une photo est fournie avant d'accéder à la clé
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $photoPath = $this->uploadService->uploadImage($data['photo'], 'users');
            $data['photo'] = $photoPath;
        } else {
            $data['photo'] = null; // Assurez-vous que la clé "photo" existe toujours
        }

        $user = $this->userRepository->create($data, $firebaseUser->uid);

        if (isset($data['photo'])) {
            Event::dispatch(new PhotoUploaded($data['photo'], $user->id));
        }

        $this->firebaseService->storeUserDetails($firebaseUser->uid, [
            'name' => $data['name'],
            'telephone' => $data['telephone'],
            'photo' => $data['photo'],
            'statut' => $data['statut'],
            'role_id' => $data['role_id'],
        ]);
        

        return $user;
    }

    public function updateUser($uid, array $data)
    {
        try {
            // // Vérifier si l'utilisateur actuel a le droit de modifier cet utilisateur
            // if (!$this->canCreateUser(Auth::user()->role->nomRole, $data['role_id'] ?? null)) {
            //     throw new \Exception('Action non autorisée');
            // }

            // Gérer l'upload de photo si une nouvelle photo est fournie
            if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
                $photoPath = $this->uploadService->uploadImage($data['photo'], 'users');
                $data['photo'] = $photoPath;

                // Déclencher l'événement PhotoUploaded
                Event::dispatch(new PhotoUploaded($photoPath, $uid));
            }

            // Mettre à jour l'utilisateur dans Firebase
            $updatedUser = $this->firebaseService->updateUser($uid, $data);

            // Mettre à jour l'utilisateur dans la base de données locale
            $localUser = $this->userRepository->update($uid, $data);

            return $localUser;
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage());
        }
    }

    public function findUserByEmail($email)
    {
        $user = $this->firebaseService->findUserByEmail($email); // Exemple de recherche via Firebase
        return $user;
    }


    public function canCreateUser($userRole, $newUserRole)
    {
        $rolePermissions = [
            'ADMIN' => ['ADMIN', 'CLIENT', 'DISTRIBUTEUR'],
            'CLIENT' => ['CLIENT'],
            'DISTRIBUTEUR' => ['DISTRIBUTEUR'],
        ];

        return in_array($newUserRole, $rolePermissions[$userRole] ?? []);
    }
}