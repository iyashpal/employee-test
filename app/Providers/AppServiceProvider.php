<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        /**
         * Bind the ReqresServiceContract to an instance of ReqresService
         */
        $this->app->bind(\App\Contracts\ReqresServiceContract::class, fn () => new \App\Services\ReqresService());
    }
}
