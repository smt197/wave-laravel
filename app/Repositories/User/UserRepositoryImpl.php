<?php

namespace App\Repositories\User;

// use App\Facades\UserFacade as User;
use App\Facades\UserFacade;
use App\Models\User;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserRepositoryImpl implements UserRepository
{

    //cree le user en locale
    public function create(array $data, string $firebaseUid)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'photo' => $data['photo'],
            'telephone' => $data['telephone'],
            'password' => Hash::make($data['password']),
            'statut' => $data['statut'],
            'role_id' => $data['role_id'],
            'firebase_uid' => $firebaseUid,
        ]);
    }

    public function update($uid, array $data)
    {
        $user = UserFacade::where('firebase_uid', $uid)->firstOrFail();

        // Si un nouveau mot de passe est fourni, le hacher
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user;
    }
}