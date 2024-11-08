<?php

namespace App\Services\Authentication;

use App\Facades\UserFacade as User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegistreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationPassport implements AuthenticationServiceInterface
{
    public function register($request)
    {
        if (!auth()->check()) {
            return ['success' => false, 'message' => 'Utilisateur non authentifié.', 'status' => 401];
        }

        $currentUser = auth()->user();

        if (!$currentUser || $currentUser->role->nomRole !== 'CLIENT' || $currentUser->role->nomRole !== 'ADMIN') {
            return ['success' => false, 'message' => 'Seuls les utilisateurs de rôle "boutiquier et Admin" peuvent s\'enregistrer.', 'status' => 403];
        }

        $user = User::create([
            'name' => $request->name,
            'photo' => $request->photo,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'statut' => $request->statut,
        ]);

        $token = $user->createToken('authToken')->accessToken;

        return ['success' => true, 'user' => $user, 'token' => $token];
    }

    public function login($credentials)
    {
        return $this->authenticate($credentials);
    }

    public function authenticate($credentials):array
    {
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            /** @var \App\Models\User $user **/
            $token = $user->createToken('appToken')->accessToken;
            $refreshToken = $user->createToken('refreshToken')->accessToken;

            return [
                'success' => true,
                'token' => $token,
                'refresh_token' => $refreshToken,
                'user' => $user
            ];
        }

        return ['success' => false, 'message' => 'Échec lors de l\'authentification.'];
    }

    public function refreshToken($request)
    {
        $credentials = $request->only('login', 'password');
        if (!Auth::attempt($credentials)) {
            return ['success' => false, 'message' => 'Identifiants invalides.'];
        }

        $user = Auth::user();
        /** @var \App\Models\User $user **/
        $newAccessToken = $user->createToken('appToken')->accessToken;
        $newRefreshToken = $user->createToken('refreshToken')->accessToken;

        return [
            'success' => true,
            'token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'user' => $user,
        ];
    }

    public function logout($user): array
    {
        if ($user) {
            $user->tokens()->delete();
            return ['success' => true, 'message' => 'Déconnexion réussie. Vous devez vous reconnecter pour accéder à l\'application.'];
        }

        return ['success' => false, 'message' => 'Utilisateur non authentifié.'];
    }
}