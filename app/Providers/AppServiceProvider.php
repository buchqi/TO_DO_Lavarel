<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Service providers are Laravel's bootstrapping classes.
// They are used when the application needs to register services, bindings,
// macros, or startup behavior before requests reach routes/controllers.
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // register() is for binding services into Laravel's container.
        // This project does not need custom bindings yet, so it stays empty.
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // boot() runs after services are registered.
        // It is often used for global model/view configuration, but this app
        // keeps that logic inside routes, controllers, and models.
        //
    }
}
