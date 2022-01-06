<?php

namespace App\Providers;

use App\Interfaces\{
    AuthInterface,
    DashboardInterface,
};
use App\Repositories\AuthRepository;
use App\Repositories\DashboardRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(AuthInterface::class, AuthRepository::class );
        $this->app->bind(DashboardInterface::class, DashboardRepository::class);
    }
}
