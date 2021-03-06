<?php

namespace EmizorIpx\PrepagoBags\Traits;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPurchaseHistorial as ModelsPrepagoBagsPurchaseHistorial;
use EmizorIpx\PrepagoBags\Repository\AccountPrepagoBagsRepository;
use EmizorIpx\PrepagoBags\Utils\TypeFrequency;
use Exception;
use Illuminate\Support\Facades\Log;

trait RechargeBagsTrait {

    public function rechargePrepagoBags($prepagoBagId){
        
        try {
            $bag = PrepagoBag::where('id', $prepagoBagId)->first();
            Log::debug($prepagoBagId);
            $account_repo = new AccountPrepagoBagsRepository();

            // TODO obtener mediante la relacion de fel_company id
            $number_invoice_before = FelCompanyDocumentSector::getInvoiceAvailable($this->id, $bag->sector_document_type_code);
            Log::debug($number_invoice_before);
            

            $accountDetails = FelCompanyDocumentSector::createOrUpdate([
                'fel_company_id' => $this->id,
                'invoice_number_available' => $account_repo->updateInvoicesAvailable($number_invoice_before, $bag),
                'accumulative' => $bag->acumulative,
                'duedate' => $account_repo->setDueDate($bag),
                'fel_doc_sector_id' => $bag->sector_document_type_code
            ]);
            
            Log::debug($accountDetails);
            
            $dataHistorial = $account_repo->setDataHistorial($accountDetails, $number_invoice_before, $bag->number_invoices, $bag->id);
            
            Log::debug($dataHistorial);

            ModelsPrepagoBagsPurchaseHistorial::registerHistorial($dataHistorial);

            
        } catch (Exception $ex) {
            \Log::error("Error  recharge PrepagoBags: " . $ex->getMessage());
            return  $ex->getMessage();
        }

    }

    
}