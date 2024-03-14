<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\IntegrationEloquentORM;
use App\Repositories\Contracts\IntegrationRepositoryInterface;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        $this->app->bind(
            IntegrationRepositoryInterface::class,
            IntegrationEloquentORM::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        //
    }
}
