<?php

namespace EmizorIpx\PrepagoBags\routes;

use Illuminate\Support\Facades\Route;

class PrepagoBags {

    public static function routes(){

        Route::group(['namespace' => "\EmizorIpx\PrepagoBags\Http\Controllers"],function () {
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

        Route::group(['namespace' => "\EmizorIpx\PrepagoBags\Http\Controllers"], function () {
            Route::get('dashboard/clients', 'DashboardController@clientsList')->name('dashboard.getClients');
            Route::post('dashboard/pilot-up', 'CompanyAccountController@pilotUp')->name('dashboard.pilot');
            Route::post('dashboard/production-up', 'CompanyAccountController@productionUp')->name('dashboard.production');
            Route::get('dashboard/form-phase-piloto/{company_id}', 'DashboardController@showForm');
            Route::get('dashboard/form-phase-production/{company_id}', 'DashboardController@showForm2');
        });

    }
}