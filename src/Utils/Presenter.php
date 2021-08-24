<?php


namespace EmizorIpx\PrepagoBags\Utils;

use EmizorIpx\PrepagoBags\Http\Resources\AccountDetailResource;
use EmizorIpx\PrepagoBags\Http\Resources\PostpagoPlanCompanyResource;
use EmizorIpx\PrepagoBags\Http\Resources\PrepagoBagResource;
use EmizorIpx\PrepagoBags\Http\Resources\PurchaseHistoryResource;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPurchaseHistorial;
use EmizorIpx\PrepagoBags\Services\PostpagoPlanCompanyService;
use Illuminate\Support\Facades\Log;

class Presenter {


    public static function appendBagsFelData($data, $company_id){
        $data['fel_data']['bags'] = PrepagoBagResource::collection( PrepagoBag::getAllBags($company_id) );
        // $data['fel_data']['company_detail'] = new AccountDetailResource(AccountPrepagoBags::getCompanyDetail($company_id));
        $data['fel_data']['purchase_history'] = PurchaseHistoryResource::collection(PrepagoBagsPurchaseHistorial::getPurchaseHistorial($company_id));
        return $data;
    }
}