<?php

namespace EmizorIpx\PrepagoBags\Traits;

use EmizorIpx\PrepagoBags\Http\Resources\AccountDetailResource;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;

trait CompanyDetailTrait{

    public function company_detail(){
        return $this->hasOne(AccountPrepagoBags::class, 'company_id');
    }

    public function includeCompanyDetail(){

        $company = $this->company_detail->load('fel_branches');
        
        return  new AccountDetailResource(collect($company));
    }
}