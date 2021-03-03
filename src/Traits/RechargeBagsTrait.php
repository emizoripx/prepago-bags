<?php

namespace EmizorIpx\PrepagoBags\Traits;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPurchaseHistorial as ModelsPrepagoBagsPurchaseHistorial;
use EmizorIpx\PrepagoBags\Repository\AccountPrepagoBagsRepository;
use EmizorIpx\PrepagoBags\Utils\TypeFrequency;
use Exception;
use Illuminate\Support\Facades\Log;

trait RechargeBagsTrait {

    public function rechargePrepagoBags($account_id, $prepagoBagId){
        
        try {
            $bag = PrepagoBag::where('id', $prepagoBagId)->first();

            $account_repo = new AccountPrepagoBagsRepository();

            $number_invoice_before = AccountPrepagoBags::getInvoiceAvailable($account_id);
            Log::debug($number_invoice_before);

            $accountDetails = AccountPrepagoBags::createOrUpdate([
                'account_id' => $account_id,
                'invoice_number_available' => $account_repo->updateInvoicesAvailable($number_invoice_before, $bag),
                'acumulative' => $bag->acumulative,
                'duedate' => $account_repo->setDueDate($bag)
            ]);
            
            Log::debug($accountDetails);
            
            $dataHistorial = $account_repo->setDataHistorial($accountDetails, $number_invoice_before, $bag->number_invoices);
            
            Log::debug($dataHistorial);

            ModelsPrepagoBagsPurchaseHistorial::registerHistorial($dataHistorial);

            
        } catch (Exception $ex) {
            return  $ex->getMessage();
        }

    }

    
}