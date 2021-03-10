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

    public function controlPrepagoBag($account_id){

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $account_id = $hashid->decode($account_id);

        $accountDetail = AccountPrepagoBags::where('account_id', $account_id)->first();

        if (! empty($accountDetail)) {

            $accountDetail->checkBagDuedate();
            $accountDetail->checkInvoiceAvailable();
        }
            

        
    }

    public function addBagGift($account_id){
        Log::debug('Add bag Gift');
        try {
            $bagFree = PrepagoBag::where('amount', 0)->first();


            Log::debug($bagFree->id);
            
            if(!empty($bagFree)){
                $this->rechargePrepagoBags($account_id, $bagFree->id);
            }
            else{
                bitacora_info("AccountPrepagoBagService:addBagFree", "PrepagoBags gratis no encontrado");
            }
        } catch (Exception $ex) {
            bitacora_error('AccountPrepagoBagService:addBagFree', $ex->getMessage());
        }
    }

    public function addBagFree($account_id, $bag_id){
        Log::debug('Add bag Free');

        $this->rechargePrepagoBags($account_id, $bag_id);
    }


}