<?php

namespace EmizorIpx\PrepagoBags\Services;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Traits\RechargeBagsTrait;
use Exception;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class AccountPrepagoBagService {

    use RechargeBagsTrait;

    public function controlPrepagoBag($company_id){

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $company_id = $hashid->decode($company_id);

        $accountDetail = AccountPrepagoBags::where('company_id', $company_id)->first();

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


}