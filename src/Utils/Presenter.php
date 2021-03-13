<?php


namespace EmizorIpx\PrepagoBags\Utils;

use EmizorIpx\PrepagoBags\Http\Resources\PrepagoBagResource;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use Illuminate\Support\Facades\Log;

class Presenter {


    public static function appendBagsFelData($data, $company_id){
        Log::debug('AppendBags');
        Log::debug($company_id);
        $data['fel_data']['bags'] = PrepagoBagResource::collection( PrepagoBag::getAllBags($company_id) );

        return $data;
    }
}