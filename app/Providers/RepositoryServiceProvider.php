<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ClientRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
        // $this->app->bind(ClientRepository::class, function($app) {
        //     return new ClientRepository(new \App\Models\Client);
        // });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
