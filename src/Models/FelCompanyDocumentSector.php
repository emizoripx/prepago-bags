<?php

namespace EmizorIpx\PrepagoBags\Models;

use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class FelCompanyDocumentSector extends Model
{
    use SoftDeletes;

    protected $table = 'fel_company_document_sectors';

    protected $fillable = ['id', 'invoice_number_available', 'accumulative','duedate', 'company_id', 'fel_company_id', 'fel_doc_sector_id', 'counter'];

    
    public static function createOrUpdate( $data ){

        $felCompany = self::where('fel_company_id', $data['fel_company_id'])->where('fel_doc_sector_id', $data['fel_doc_sector_id'])->first();
        

        if(!$felCompany){
            return self::create($data);
        }
        else{

            $felCompany->update($data);
            return $felCompany;
        }

    }

    public static function getInvoiceAvailable( $felCompanyId, $documentSectorCode ){

        \Log::debug("Company_id: ".$felCompanyId."  Document: ".$documentSectorCode);

        $felDocumentSector = self::where('fel_company_id', $felCompanyId)->where('fel_doc_sector_id', $documentSectorCode)->first();

        if(is_null($felDocumentSector)){
            return 0;
        }

        return $felDocumentSector->invoice_number_available;

    }

    public function checkBagDuedate(){
        $dateNow = Carbon::now();

        if($dateNow->greaterThan($this->duedate)){
            throw new PrepagoBagsException("Su bolsa prepago ha expirado");
        }
    }

    public function checkInvoiceAvailable(){
        if($this->invoice_number_available <=  0){
            throw new PrepagoBagsException("Facturas no diponibles para emitir. Adquirir una nueva bolsa.");
        }
    }

    public function reduceNumberInvoice(){
        $this->invoice_number_available = $this->invoice_number_available - 1;
        \Log::debug("#Facturas disponibles :" .$this->invoice_number_available);
        return $this;
    }

    public function addNumberInvoice(){
        $this->invoice_number_available = $this->invoice_number_available + 1;
        \Log::debug("#Facturas disponibles :" . $this->invoice_number_available);
        return $this;
    }

    public function setCounter($sign = 1){
        $this->counter = $this->counter + (1 * $sign);
        \Log::debug("#Cantidad facturas hechas :" . $this->counter);
        return $this;
    }

    public static function getCompanyDocumentSectorByCode($fel_company_id, $sector_document_code){
        return self::where('fel_company_id', $fel_company_id)->where('fel_doc_sector_id', $sector_document_code)->first();
    }

}
