<?php

namespace EmizorIpx\PrepagoBags;

use Illuminate\Support\ServiceProvider;

class PrepagoBagsServiceProvider extends ServiceProvider
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
        // Migrations

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }
}
