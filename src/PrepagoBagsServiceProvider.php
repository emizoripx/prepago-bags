<?php

namespace EmizorIpx\PrepagoBags;

use App\Models\Account;
use App\Models\Company;
use EmizorIpx\PrepagoBags\Http\Middleware\VerifyAccountEnabled;
use EmizorIpx\PrepagoBags\Observers\AccountObserver;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\Router;
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
        Paginator::useBootstrap();
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        //  CONFIG FILE
        $this->publishes([
            __DIR__."/config/prepagobag.php" => config_path('prepagobag.php')
        ]);

        $this->mergeConfigFrom(__DIR__.'/config/prepagobag.php', 'prepagobag');

        // Obeservers only for update
            
        $account = $this->app->make(Config::get('prepagobag.entity_table_account'));
        Company::observe(new AccountObserver);


        // VISTAS
        $this->loadViewsFrom(__DIR__.'/Resource/views', 'prepagobags');


        # Middleware
        
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('verify_account', VerifyAccountEnabled::class);
    }
}
