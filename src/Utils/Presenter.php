<?php


namespace EmizorIpx\PrepagoBags\Utils;

use EmizorIpx\PrepagoBags\Http\Resources\PrepagoBagResource;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use Illuminate\Support\Facades\Log;

class Presenter {


    public static function appendBagsFelData($data, $account_id){
        Log::debug('AppendBags');
        Log::debug($account_id);
        $data['fel_data']['bags'] = PrepagoBagResource::collection( PrepagoBag::getAllBags($account_id) );

        return $data;
    }
}