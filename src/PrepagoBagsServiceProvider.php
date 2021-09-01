<?php

namespace EmizorIpx\PrepagoBags;

use App\Models\Account;
use App\Models\Company;
use App\Models\User;
use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use EmizorIpx\PrepagoBags\Http\Middleware\VerifyAccountEnabled;
use EmizorIpx\PrepagoBags\Http\Middleware\VerifyLimitsAccount;
use EmizorIpx\PrepagoBags\Observers\AccountObserver;
use EmizorIpx\PrepagoBags\Observers\FelClientCompanyObserver;
use EmizorIpx\PrepagoBags\Observers\FelProductCompanyObserver;
use EmizorIpx\PrepagoBags\Observers\FelUserCompanyObserver;
use EmizorIpx\PrepagoBags\Repository\AccountPrepagoBagsRepository;
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

        FelSyncProduct::observe(new FelProductCompanyObserver(new AccountPrepagoBagsRepository));
        FelClient::observe(new FelClientCompanyObserver(new AccountPrepagoBagsRepository));
        User::observe(new FelUserCompanyObserver(new AccountPrepagoBagsRepository));


        // VISTAS
        $this->loadViewsFrom(__DIR__.'/Resource/views', 'prepagobags');


        # Middleware
        
        $router = $this->app->make(Router::class);

        $router->aliasMiddleware('verify_account', VerifyAccountEnabled::class);
        $router->aliasMiddleware('verify_limits', VerifyLimitsAccount::class);
    }
}
