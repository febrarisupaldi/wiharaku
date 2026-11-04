<?php

namespace App\Providers;

use App\Repositories\Contracts\RegionRepositoryInterface;
use App\Repositories\MySQL\RegionRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RegionRepositoryInterface::class, RegionRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
