<?php

namespace App\Providers;

use App\Services\Authentication\AuthenticationFirebase;
use Illuminate\Support\ServiceProvider;
use App\Services\Authentication\AuthenticationServiceInterface;
use App\Services\Authentication\AuthenticationPassport;

class AuthCustomServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AuthenticationServiceInterface::class, function ($app) {
            // Choisir l'implémentation en fonction de la configuration
            $driver = config('auth.default_driver');
            
            switch ($driver) {
                case 'passport':
                    return new AuthenticationPassport();
                case 'firebase':
                    return new AuthenticationFirebase();
                default:
                    throw new \Exception("Driver d'authentification non supporté: {$driver}");
            }
        });
    }

    public function boot()
    {
        //
    }
}