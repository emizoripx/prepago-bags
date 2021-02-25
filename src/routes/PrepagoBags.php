<?php

namespace EmizorIpx\PrepagoBags\routes;

use Illuminate\Support\Facades\Route;

class PrepagoBags {

    public static function routes(){

        Route::group(['namespace' => "\EmizorIpx\PrepagoBags\Http\Controllers"],function () {
            Route::post('prepago_bags', 'PrepagoBagController@store');
            Route::get('prepago_bags', 'PrepagoBagController@index');
            Route::put('prepago_bags/{id_bag}', 'PrepagoBagController@update');
            Route::delete('prepago_bags/{id_bag}', 'PrepagoBagController@delete');
            Route::get('prepago_bags/{id_bag}', 'PrepagoBagController@show');
        });
    }
}