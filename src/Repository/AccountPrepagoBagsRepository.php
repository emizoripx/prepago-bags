<?php


namespace EmizorIpx\PrepagoBags\Repository;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Utils\TypeFrequency;

class AccountPrepagoBagsRepository{





    public function updateInvoicesAvailable($number_invoice_before, $bag){
        return $bag->acumulative ? $number_invoice_before +  $bag->number_invoices : $bag->number_invoices;
    }


    public function setDataHistorial( $account, $number_invoice_before, $number_invoices, $bag_id){
        return [
            'purchase_date' => $account->updated_at,
            'account_id' => $account->account_id,
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

}