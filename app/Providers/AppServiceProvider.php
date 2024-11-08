<?php

namespace App\Providers;

use App\Repositories\Client\ClientRepository;
use App\Repositories\Client\ClientRepositoryImpl;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryImpl;
use App\Services\Client\ClientService;
use App\Services\Client\ClientServiceImpl;
use App\Services\Firebase\FirebaseService;
use App\Services\Firebase\FirebaseServiceInterface;
use App\Services\User\UserService;
use App\Services\User\UserServiceImpl;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\UriInterface;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UriInterface::class, function ($app) {
            return new Uri();
        });

        // $this->app->singleton(Database::class, function ($app) {
        //     return (new Factory)
        //         ->withServiceAccount(env('FIREBASE_CREDENTIALS'))
        //         ->withDatabaseUri(config('services.firebase.database_url')) 
        //         ->createDatabase();
        // });

        $this->app->singleton('user', UserServiceImpl::class);

        $this->app->bind(UserService::class,UserServiceImpl::class);
        $this->app->bind(UserRepository::class,UserRepositoryImpl::class);

        $this->app->bind(FirebaseServiceInterface::class,FirebaseService::class);

        $this->app->singleton(ClientRepository::class, ClientRepositoryImpl::class);
        $this->app->singleton(ClientService::class, ClientServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       
    }
}
