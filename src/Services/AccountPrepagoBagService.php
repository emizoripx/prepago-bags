<?php

namespace EmizorIpx\PrepagoBags\Services;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Traits\RechargeBagsTrait;
use Exception;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class AccountPrepagoBagService {

    use RechargeBagsTrait;

    
    public function controlPrepagoBag($company_id, $sector_document_type_code){

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $company_id = $hashid->decode($company_id);

        $accountDetail = AccountPrepagoBags::where('company_id', $company_id)->where('is_postpago', false)->where('sector_document_type_code', $sector_document_type_code)->first();

        if (! empty($accountDetail)) {

            $accountDetail->checkBagDuedate();
            $accountDetail->checkInvoiceAvailable();
        }
            

        
    }

    public function addBagGift($company_id){
        Log::debug('Add bag Gift');
        try {
            $bagFree = PrepagoBag::where('amount', 0)->first();


            Log::debug($bagFree->id);
            
            if(!empty($bagFree)){
                $this->rechargePrepagoBags($company_id, $bagFree->id);
            }
            else{
                bitacora_info("AccountPrepagoBagService:addBagFree", "PrepagoBags gratis no encontrado");
            }
        } catch (Exception $ex) {
            bitacora_error('AccountPrepagoBagService:addBagFree', $ex->getMessage());
        }
    }

    public function addBagFree($company_id, $bag_id){
        Log::debug('Add bag Free');

        $this->rechargePrepagoBags($company_id, $bag_id);
    }

    public function updateAdditionalInformation($feldata, $company_id)
    {

        $accountDetail = AccountPrepagoBags::where('company_id', $company_id)->first();

        if (!empty($accountDetail)) {
            //these fields were added for exportation companies
            $accountDetail->ruex = !empty($feldata['ruex']) ? $feldata['ruex'] : "";
            $accountDetail->nim = !empty($feldata['nim']) ? $feldata['nim'] : "" ;
            $accountDetail->save();
        }
            
    }

    public function registerAccountPrepagoBags($company_id){
        
        try {
            $documents = FelParametric::index(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, $company_id);

            foreach( $documents as $document ){
                AccountPrepagoBags::createOrUpdate([
                    'company_id' => $company_id,
                    'sector_document_type_code' => $document->codigo
                ]);
            }
        } catch (Exception $ex) {
            bitacora_error('AccountPrepagoBagService:addAccount', $ex->getMessage());
        }
        
    }


}