<?php

namespace EmizorIpx\PrepagoBags\routes;

use EmizorIpx\PrepagoBags\Models\FelPin;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

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
            Route::get('getUuid', function () {
                $array = [];

                
                    for ($i = 0; $i < 200; $i++) {
                        // $string = strtoupper(Str::uuid()->toString());
                        do {
                            usleep(200000);
                            $newPin = rand(1111,9999)."-".time()."-".rand(1111,9999);
                        } while (FelPin::where('pin', $newPin)->exists());
                        \Log::debug($newPin);
                         echo $newPin. '  -';
                        if (in_array($newPin, $array)) {
                             echo ' True ';
                        } else {
                             echo ' False ';
                        }
                        $array [] = $newPin;
                        
                    }
                
                
                });
            });

    }
}