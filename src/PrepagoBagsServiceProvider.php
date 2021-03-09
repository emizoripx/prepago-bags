<?php

namespace EmizorIpx\PrepagoBags;

use App\Models\Account;
use EmizorIpx\PrepagoBags\Observers\AccountObserver;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Illuminate\Support\Facades\Config;
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

        //  CONFIG FILE
        $this->publishes([
            __DIR__."/config/prepagobag.php" => config_path('prepagobag.php')
        ]);

        $this->mergeConfigFrom(__DIR__.'/config/prepagobag.php', 'prepagobag');

        // // Obeservers
        
        // $account = $this->app->make(Config::get('prepagobag.entity_table_account'));
        Account::observe(new AccountObserver(new AccountPrepagoBagService));
    }
}
