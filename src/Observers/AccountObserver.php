<?php

namespace EmizorIpx\PrepagoBags\Observers;


class AccountObserver {
    
    protected $service;

    public function updated($company)
    {
        if (request()->has('felData') && isset(request()->input('felData')['nim']) && request()->input('felData')['nim'] != "" && isset(request()->input('felData')['ruex']) && request()->input('felData')['ruex'] != "" ){
            $company->company_detail->updateAdditionalInformation(request()->input('felData'))->save();
        }
    }
}