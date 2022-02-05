<?php

namespace App\Providers;

use App\Interfaces\{
    AuthInterface,
    WalletInterface,
    ProfileInterface,
    DashboardInterface,
    BlogInterface,
    BillInterface,
    CardInterface,
    SupportInterface,
};
use App\Repositories\{
    AuthRepository,
    BillRepository,
    BlogRepository,
    CardRepository,
    WalletRepository,
    ProfileRepository,
    DashboardRepository,
    SupportRepository,
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
        $this->app->bind(BillInterface::class, BillRepository::class);
        $this->app->bind(BlogInterface::class, BlogRepository::class);
        $this->app->bind(SupportInterface::class, SupportRepository::class);
        $this->app->bind(CardInterface::class, CardRepository::class);
    }
}
