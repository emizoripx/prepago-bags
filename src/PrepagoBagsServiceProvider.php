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
use Livewire\Livewire;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyInformationTab;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyProductsTab;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyClientsTab;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyPaymentsTab;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanySettingsTab;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyTable;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyUsersTab;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\InvoicesAdminTable;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\PilotoUp;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\InvoiceEditModal;
use EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\InvoiceDetailModal;

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
        if ($this->app->runningInConsole()) {
            $this->commands([
                \EmizorIpx\PrepagoBags\Console\Commands\UpdateCompanyStatisticsCommand::class,
            ]);
        }

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

        Livewire::component('company-information-tab', CompanyInformationTab::class);
        Livewire::component('company-settings-tab', CompanySettingsTab::class);
        Livewire::component('company-products-tab', CompanyProductsTab::class);
        Livewire::component('company-clients-tab', CompanyClientsTab::class);
        Livewire::component('company-payments-tab', CompanyPaymentsTab::class);
        Livewire::component('company-parametrics-tab', \EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyParametricsTab::class);
        Livewire::component('company-branches-tab', \EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyBranchesTab::class);
        Livewire::component('company-templates-tab', \EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CompanyTemplatesTab::class);
        Livewire::component('company-table', CompanyTable::class);
        Livewire::component('company-users-tab', CompanyUsersTab::class);
        Livewire::component('invoices-admin-table', InvoicesAdminTable::class);
        Livewire::component('piloto-up', PilotoUp::class);
        Livewire::component('prepagobags::superadmin.invoice-edit-modal', InvoiceEditModal::class);
        Livewire::component('prepagobags::superadmin.invoice-detail-modal', InvoiceDetailModal::class);
        Livewire::component('invoice-detail-modal', InvoiceDetailModal::class);
        Livewire::component('prepagobags::superadmin.create-company-modal', \EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\CreateCompanyModal::class);
        Livewire::component('upload-company-logo-modal', \EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\UploadCompanyLogoModal::class);
        Livewire::component('prepagobags::superadmin.upload-company-logo-modal', \EmizorIpx\PrepagoBags\Http\Livewire\Superadmin\UploadCompanyLogoModal::class);
    }
}
