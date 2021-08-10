<?php

namespace EmizorIpx\PrepagoBags\Services;

use Carbon\Carbon;
use EmizorIpx\ClientFel\Models\FelParametric;
use EmizorIpx\ClientFel\Utils\TypeParametrics;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Models\PrepagoBag;
use EmizorIpx\PrepagoBags\Traits\RechargeBagsTrait;
use Exception;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class AccountPrepagoBagService {

    // use RechargeBagsTrait;

    protected $fel_company;

    public function __construct( AccountPrepagoBags $fel_company )
    {
        $this->fel_company = $fel_company;
        
    }
    
    public function controlPrepagoBag($sector_document_type_code){

        // $hashid = new Hashids(config('ninja.hash_salt'), 10);

        // $company_id = $hashid->decode($company_id);
        $accountDetail = FelCompanyDocumentSector::getCompanyDocumentSectorByCode($this->fel_company->id, $sector_document_type_code);
        if (!$accountDetail) {
            throw new PrepagoBagsException('No existe una bolsa prepago para el tipo de Factura');
        }
        if(!$this->fel_company->is_postpago){

            // $accountDetail = FelCompanyDocumentSector::where('fel_company_id', $this->fel_company->id)->where('fel_doc_sector_id', $sector_document_type_code)->first();
    
            $accountDetail->checkBagDuedate();
            $accountDetail->checkInvoiceAvailable();
        }
        return $accountDetail;

        
    }

    public function addBagGift(){
        Log::debug('Add bag Gift');
        Log::debug($this->fel_company);
        try {
            $bagFree = PrepagoBag::where('amount', 0)->first();


            Log::debug($bagFree->id);
            
            if(!empty($bagFree)){
                $this->fel_company->rechargePrepagoBags($bagFree->id);
            }
            else{
                bitacora_info("AccountPrepagoBagService:addBagFree", "PrepagoBags gratis no encontrado");
            }
        } catch (Exception $ex) {
            bitacora_error('AccountPrepagoBagService:addBagFree', $ex->getMessage());
        }
    }

    public function addBagFree($bag_id){
        Log::debug('Add bag Free');

        $this->fel_company->rechargePrepagoBags($bag_id);
    }


    public function registerCompanySectorDocuments(){
        
        try {
            $documents = FelParametric::index(TypeParametrics::TIPOS_DOCUMENTO_SECTOR, $this->fel_company->company_id);

            foreach( $documents as $document ){
                
                FelCompanyDocumentSector::createOrUpdate([
                    'company_id' => $this->fel_company->company_id,
                    'fel_doc_sector_id' => $document->codigo,
                    'fel_company_id' => $this->fel_company->id
                ]);

            }
            return $this;
        } catch (Exception $ex) {
            \Log::debug("Error al Registrar Comapany Sector Documents...". $ex);
            bitacora_error('AccountPrepagoBagService:addAccount', $ex->getMessage());
        }
        
    }


    public function savePostpagoLimit($sectorDocuments ){

        try{
            foreach ($sectorDocuments as $key => $value) {
                FelCompanyDocumentSector::createOrUpdate([
                    'company_id' => $this->fel_company->company_id,
                    'fel_doc_sector_id' => $key,
                    'postpago_limit' => intval($value),
                    'fel_company_id' => $this->fel_company->id
                ]);
            }
            return $this;
        } catch(Exception $ex){
            \Log::debug("Error al Registrar Limit Company Sector Documents...". $ex);
            bitacora_error('AccountPrepagoBagService:UpdateLimitSectorDocuments', $ex->getMessage());
        }

    }

    public function saveStartDate(){
        try{
            FelCompanyDocumentSector::where('company_id', $this->fel_company->company_id)->update([
                'start_date' => Carbon::now()->toDateString()
            ]);

            return $this;
        } catch(Exception $ex){
            \Log::debug("Error al Actualizar startDate");
        }
    }
    public function saveFrequency( $frequency ){
        try{
            FelCompanyDocumentSector::where('company_id', $this->fel_company->company_id)->update([
                'frequency' => $frequency
            ]);
            return $this;
        } catch(Exception $ex){
            \Log::debug("Error al Frequency startDate");
        }
    }

    public function resetInvoiceAvailable(){
        try{
            \Log::debug("Reset Invoice Available");
            FelCompanyDocumentSector::resetInvoiceAvailable($this->fel_company->company_id);

            return $this;

        } catch ( Exception $ex ){
            \Log::debug("Error to reset Invoice Available...". $ex);
        }
    }
    public function resetCounter(){
        try{
            \Log::debug("Reset Counter");
            FelCompanyDocumentSector::resetCounter($this->fel_company->company_id);

            return $this;

        } catch ( Exception $ex ){
            \Log::debug("Error to reset Invoice Available...". $ex);
        }
    }


}