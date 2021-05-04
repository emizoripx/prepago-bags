<?php

namespace EmizorIpx\PrepagoBags\Traits;

use EmizorIpx\PrepagoBags\Http\Resources\AccountDetailResource;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Carbon\Carbon;

trait CompanyDetailTrait{

    public function company_detail(){
        return $this->hasOne(AccountPrepagoBags::class, 'company_id')->with('fel_branches','fel_company_document_sector');
    }

    public function includeCompanyDetail(){

        $company = $this->company_detail->load('fel_branches', 'fel_company_document_sector');

        return  new AccountDetailResource(collect($company));
    }


    public function createFelCompany(){

        AccountPrepagoBags::create([
            'company_id' => $this->id,
            'created_at' => Carbon::now()
        ]);

        return $this;

    }
}