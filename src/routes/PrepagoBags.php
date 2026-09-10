<?php

namespace EmizorIpx\PrepagoBags\routes;

use EmizorIpx\PrepagoBags\Models\FelPin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\Auth\AdminLoginController;
class PrepagoBags {

    public static function routes(){

        Route::group(['namespace' => "\EmizorIpx\PrepagoBags\Http\Controllers"],function () {
            Route::post('api/webhooks/partner/events', 'PartnerWebhookReceiverController@handle');
            Route::get('prepago_bags/purchase/{prepago_bag_id}', 'PurchasePrepagoBagController@get');
            
            Route::post('prepago_bags', 'PrepagoBagController@store');
            Route::get('prepago_bags', 'PrepagoBagController@index');
            Route::put('prepago_bags/{id_bag}', 'PrepagoBagController@update');
            Route::delete('prepago_bags/{id_bag}', 'PrepagoBagController@delete');
            Route::get('prepago_bags/{id_bag}', 'PrepagoBagController@show');

            Route::get('prepago_bags/free/{id_bag}', 'PrepagoBagController@getBagFree');

            // Route::get('dashboard/clients', 'DashboardController@clientsList');

            // Route::post('company/pilot-up/{company_id}', 'CompanyAccountController@pilotUp');
            
        });

        
    }


    public static function adminRoutes() {
        info("================================================== admin routes=====================");

        Route::get('superu', [AdminLoginController::class, 'showLoginForm'])->name('admin.catchall')->middleware(['locale']);
        Route::post('superu/login', [AdminLoginController::class, 'login'])->name('admin.login');
        Route::get('superu/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
        
        Route::group(['namespace' => "\EmizorIpx\PrepagoBags\Http\Controllers", 'middleware' => ['auth:admin']], function () {
                
            Route::get('dashboard', 'DashboardController@dashboard')->name('admin.dashboard');
            
            Route::get('dashboard/administrators', 'DashboardController@administrators')->name('admin.administrators');
            Route::post('dashboard/administrators', 'DashboardController@storeAdministrator')->name('admin.administrators.store');
            Route::post('dashboard/administrators/{id}/toggle', 'DashboardController@toggleAdministrator')->name('admin.administrators.toggle');
            
            Route::get('dashboard/partner-settings', 'DashboardController@partnerSettings')->name('admin.partner.settings');
            Route::post('dashboard/partner-settings', 'DashboardController@updatePartnerSettings')->name('admin.partner.settings.update');
            Route::post('dashboard/test-partner-connection', 'DashboardController@testPartnerConnection')->name('admin.partner.settings.test');
            
            Route::get('dashboard/companies', 'DashboardController@companies')->name('admin.companies');
            Route::get('dashboard/invoices', 'DashboardController@invoices')->name('admin.invoices');
            Route::get('dashboard/companies/{id}/invoices', 'DashboardController@invoices')->name('admin.companies.invoices');
            Route::get('dashboard/companies/{company_id}/profile', 'DashboardController@profile')->name('admin.companies.profile');
            Route::get('dashboard/companies/{company_id}/impersonate', 'DashboardController@impersonate')->name('admin.companies.impersonate');

            Route::post('dashboard/pilot-up', 'CompanyAccountController@pilotUp')->name('dashboard.pilot');
            Route::post('dashboard/production-up', 'CompanyAccountController@productionUp')->name('dashboard.production');
            
            Route::post('dashboard/postpago-plans', 'PostpagoPlanController@store')->name('postpago.store');
            Route::get('dashboard/postpago-plans', 'PostpagoPlanController@index')->name('postpago.index');
            Route::get('dashboard/postpago-plans/{id}', 'PostpagoPlanController@show')->name('postpago.show');
            Route::put('dashboard/postpago-plans/{id}', 'PostpagoPlanController@update')->name('postpago.update');
            Route::get('dashboard/postpago-plans/publish/{id}', 'PostpagoPlanController@publishPlan')->name('postpago.publish');
            Route::get('dashboard/postpago-plans-to-select', 'PostpagoPlanController@getPostpagoPlans')->name('postpago.getPostpagoPlans');

            Route::post('dashboard/linked-client', 'CompanyAccountController@linkedClient')->name('dashboard.linkedClient');
            Route::post('dashboard/suspend-client', 'CompanyAccountController@suspendClient')->name('dashboard.suspendClient');
            Route::post('dashboard/up-client', 'CompanyAccountController@upClient')->name('dashboard.upClient');
            
            Route::get('dashboard/form-phase-piloto/{company_id}', 'DashboardController@showForm');
            Route::get('dashboard/form-phase-production/{company_id}', 'DashboardController@showForm2');
            Route::get('dashboard/form-information/{company_id}', 'DashboardController@showInformation');
            Route::get('dashboard/form-users/{company_id}', 'DashboardController@showUsers');
        Route::get('dashboard/form-settings/{company_id}', 'DashboardController@showSettings');
        Route::put('dashboard/company-settings/{company_id}', 'DashboardController@updateSettings');
            Route::get('dashboard/form-linked/{company_id}', 'DashboardController@showFormLikend');
            Route::get('dashboard/form-suspend/{company_id}', 'DashboardController@showFormSuspend');
            Route::get('dashboard/form-up/{company_id}', 'DashboardController@showFormUp');
            Route::get('dashboard/form-edit-plans/{plan_id}', 'PostpagoPlanController@formEdit');
            Route::get('dashboard/form-create-plans', 'PostpagoPlanController@formCreate');
            Route::get('dashboard/users/{id}/mark-verified', 'DashboardController@markVerified');
            
        });

    }
}