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

        $this->service->addBagGift($company->id);
    }

    public function updated($company)
    {
        $this->service->updateAdditionalInformation(request()->input('felData'), $company->id);
    }
}