<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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

        // Prevent lazy loading, silently discarding attributes, and accessing missing attributes
        Model::shouldBeStrict();

        /**
         * Bind the ReqresServiceContract to an instance of ReqresService
         */
        $this->app->bind(\App\Contracts\ReqresServiceContract::class, fn () => new \App\Services\ReqresService());
    }
}
