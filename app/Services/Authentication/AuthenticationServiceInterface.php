<?php

namespace App\Services\Authentication;

interface AuthenticationServiceInterface
{
    public function authenticate($credentials);
    public function register($credentials);
    public function login($credentials);
    public function refreshToken($request);
    public function logout($user);
}