<?php

namespace EmizorIpx\PrepagoBags\Observers;

use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;

class AccountObserver {
    
    protected $service;

    public function __construct(AccountPrepagoBagService $service)
    {
        $this->service = $service;
    }

    public function created($company){
        \Log::debug('Bolsa de Regalo');
        \Log::debug($company);
        \Log::debug('Bolsa de Regalo company id');
        \Log::debug($company->id);
        $this->service->addBagGift($company->id);
    }
}