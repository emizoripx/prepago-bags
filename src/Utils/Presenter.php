<?php


namespace EmizorIpx\PrepagoBags\Utils;

use EmizorIpx\PrepagoBags\Http\Resources\PrepagoBagResource;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;

class Presenter {


    public static function appendBagsFelData($data){

        $data['fel_data']['bags'] = PrepagoBagResource::collection( PrepagoBag::getAllBags() );

        return $data;
    }
}