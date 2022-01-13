<?php

namespace App\Providers;

use App\Interfaces\{
    AuthInterface,
    WalletInterface,
    ProfileInterface,
    DashboardInterface,
};
use App\Repositories\{
    AuthRepository,
    WalletRepository,
    ProfileRepository,
    DashboardRepository,
};
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
        $this->app->bind(WalletInterface::class, WalletRepository::class);
        $this->app->bind(ProfileInterface::class, ProfileRepository::class);
    }
}
