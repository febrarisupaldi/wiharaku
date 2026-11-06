<?php

namespace App\Providers;

use App\Repositories\Contracts\RegionRepositoryInterface;
use App\Repositories\MySQL\RegionRepository;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        DB::listen(function (QueryExecuted $query) {
            Log::info($query->toRawSql() . ' [' . $query->time . 'ms]');
        });
    }
}
