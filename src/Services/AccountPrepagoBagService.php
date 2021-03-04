<?php

namespace EmizorIpx\PrepagoBags\Services;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class AccountPrepagoBagService {

    public function controlPrepagoBag($account_id){

        $hashid = new Hashids(config('ninja.hash_salt'), 10);

        $account_id = $hashid->decode($account_id);

        $accountDetail = AccountPrepagoBags::where('account_id', $account_id)->first();

        
            $accountDetail->checkBagDuedate();
            
            $accountDetail->checkInvoiceAvailable();

        
    }
}