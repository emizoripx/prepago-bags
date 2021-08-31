<?php


namespace EmizorIpx\PrepagoBags\Repository;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Utils\TypeFrequency;
use DB;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Traits\DecodeIdsTrait;

class AccountPrepagoBagsRepository{



    use DecodeIdsTrait;

    public function updateInvoicesAvailable($number_invoice_before, $bag){
        return $bag->acumulative ? $number_invoice_before +  $bag->number_invoices : $bag->number_invoices;
    }


    public function setDataHistorial( $account, $number_invoice_before, $number_invoices, $bag_id){
        return [
            'purchase_date' => $account->updated_at,
            'company_id' => $account->company_id,
            'number_invoices' => $number_invoices,
            'number_invoices_before' => $number_invoice_before,
            'bag_id' => $bag_id
        ];
    }

    public function setDueDate($bag){
        $dateNow = Carbon::now();

        switch ($bag->frequency) {
            case TypeFrequency::MONTHLY:
                return $dateNow->addMonth();
                break;
            
            case TypeFrequency::YEARLY:
                return $dateNow->addYear();
                break;
            
        }
        
    }

    public function updateCounterProducts($company_id){

        
        $fel_company = AccountPrepagoBags::where('company_id', $this->getDecodeId($company_id)[0])->first();
        
        if($fel_company->is_postpago){
            $fel_company->update([
                'counter_products' => $fel_company->counter_products + 1
            ]);
        }

    }

    public function updateCounterClients($company_id){

        
        $fel_company = AccountPrepagoBags::where('company_id', $this->getDecodeId($company_id)[0])->first();
        
        if($fel_company->is_postpago){
            $fel_company->update([
                'counter_clients' => $fel_company->counter_clients + 1
            ]);
        }

    }

    public function updateCounterUsers($company_id){

        $fel_company = AccountPrepagoBags::where('company_id', $company_id)->first();
        
        if( $fel_company && $fel_company->is_postpago){
            $fel_company->update([
                'counter_users' => $fel_company->counter_users + 1
            ]);
        }

    }

}