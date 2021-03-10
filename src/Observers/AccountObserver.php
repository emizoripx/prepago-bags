<?php

namespace EmizorIpx\PrepagoBags\Observers;

use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;

class AccountObserver {
    
    protected $service;

    public function __construct(AccountPrepagoBagService $service)
    {
        $this->service = $service;
    }

    public function created($account){
        \Log::debug($account);
        $this->service->addBagGift($account->id);
    }
}