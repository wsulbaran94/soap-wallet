<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Service\ClientService;
use App\Repositories\ClientRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        // $this->app->singleton(ClientRepository::class, function ($app) {
        //     return new ClientRepository(new \App\Models\Client);
        // });
        $this->app->singleton(ClientService::class, function ($app) {
            return new ClientService(new \App\Repositories\ClientRepository(new \App\Models\Client));
        });
    
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
