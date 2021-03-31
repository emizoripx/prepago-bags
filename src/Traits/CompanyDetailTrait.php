<?php

namespace EmizorIpx\PrepagoBags\Traits;

use EmizorIpx\PrepagoBags\Http\Resources\AccountDetailResource;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;

trait CompanyDetailTrait{

    public function company_detail(){
        return $this->hasOne(AccountPrepagoBags::class, 'company_id');
    }

    public function includeCompanyDetail(){
        $company_detail = $this->company_detail;

        return is_null($company_detail) ? null : new AccountDetailResource($company_detail);
    }
}