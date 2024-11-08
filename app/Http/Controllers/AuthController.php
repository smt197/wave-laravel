<?php

namespace App\Http\Controllers;

use App\Enums\StatusResponseEnum;
use App\Http\Requests\RegistreRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Authentication\AuthenticationServiceInterface;
use App\Traits\RestResponseTrait;
use Exception;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class AuthController extends Controller
{
    use RestResponseTrait;

    protected $authService;

    public function __construct(AuthenticationServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function register(StoreUserRequest $request)
    {
        try {
            $response = $this->authService->register($request->validated());

            if ($response['success']) {
                return $this->sendResponse([
                    'user' => new UserResource($response['user']),
                    'token' => $response['token'],
                ], StatusResponseEnum::SUCCESS);
            } else {
                return $this->sendResponse(['error' => $response['message']], StatusResponseEnum::ECHEC, $response['status'] ?? 400);
            }
        } catch (Exception $e) {
            return $this->sendResponse(['error' => $e->getMessage()], StatusResponseEnum::ECHEC, 500);
        }
    }

    public function login(Request $request)
    {
        $authDriver = Config::get('auth.default_driver');

        $credentials = $authDriver === 'firebase' 
            ? $request->only('email', 'password')  // Pour Firebase, on s'attend à recevoir le token directement
            : $request->only('email', 'password');  // Pour Passport

        $response = $this->authService->authenticate($credentials);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'token' => $response['token'],
                'refresh_token' => $response['refresh_token'] ?? null,
                'user' => new UserResource($response['user']),
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message'],
            ], 401);
        }
    }

    public function refreshToken(Request $request)
    {
        $authDriver = Config::get('auth.default_driver');

        $data = $authDriver === 'firebase' 
            ? $request->input('token')  // Pour Firebase, on s'attend à recevoir le token actuel
            : $request->only('login', 'password');  // Pour Passport

        $response = $this->authService->refreshToken($data);

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'token' => $response['token'],
                'refresh_token' => $response['refresh_token'] ?? null,
                'user' => isset($response['user']) ? new UserResource($response['user']) : null,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message']
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $response = $this->authService->logout($user);

        if ($response['success']) {
            return response()->json(['message' => $response['message']]);
        } else {
            return response()->json(['message' => $response['message']], 401);
        }
    }
}