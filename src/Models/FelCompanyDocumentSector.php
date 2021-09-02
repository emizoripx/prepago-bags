<?php

namespace EmizorIpx\PrepagoBags\Models;

use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use Exception;

class FelCompanyDocumentSector extends Model
{
    use SoftDeletes;

    protected $table = 'fel_company_document_sectors';

    protected $fillable = ['id', 'invoice_number_available', 'accumulative', 'duedate', 'company_id', 'fel_company_id', 'fel_doc_sector_id', 'counter', 'postpago_limit', 'postpago_exceded_limit'];


    public static function createOrUpdate($data)
    {

        $felCompany = self::where('fel_company_id', $data['fel_company_id'])->where('fel_doc_sector_id', $data['fel_doc_sector_id'])->first();


        if (!$felCompany) {
            return self::create($data);
        } else {

            $felCompany->update($data);
            return $felCompany;
        }
    }

    public static function getInvoiceAvailable($felCompanyId, $documentSectorCode)
    {

        \Log::debug("Company_id: " . $felCompanyId . "  Document: " . $documentSectorCode);

        $felDocumentSector = self::where('fel_company_id', $felCompanyId)->where('fel_doc_sector_id', $documentSectorCode)->first();

        if (is_null($felDocumentSector)) {
            return 0;
        }

        return $felDocumentSector->invoice_number_available;
    }

    public function checkBagDuedate()
    {
        $dateNow = Carbon::now();

        if ($dateNow->greaterThan($this->duedate)) {
            throw new PrepagoBagsException("Su bolsa prepago ha expirado");
        }
    }

    public function checkInvoiceAvailable()
    {
        if ($this->invoice_number_available <=  0) {
            throw new PrepagoBagsException("Facturas no diponibles para emitir. Adquirir una nueva bolsa.");
        }
    }

    public function reduceNumberInvoice()
    {
        $this->invoice_number_available = $this->invoice_number_available - 1;
        \Log::debug("#Facturas disponibles :" . $this->invoice_number_available);
        return $this;
    }

    public function addNumberInvoice($add = 1)
    {
        $this->invoice_number_available = $this->invoice_number_available + $add;
        \Log::debug("#Facturas disponibles :" . $this->invoice_number_available);
        return $this;
    }

    public function setCounter($sign = 1)
    {
        $this->counter = $this->counter + (1 * $sign);
        \Log::debug("#Cantidad facturas hechas :" . $this->counter);
        return $this;
    }
    public function setPostpagoCounter($sign = 1)
    {
        $postpago_plan = PostpagoPlanCompany::where('company_id', $this->company_id)->first();
        // In case postpago plan is not set
        if (empty($postpago_plan))
            return $this;
        
        try {
            $this->postpago_counter = $this->postpago_counter + (1 * $sign);
            $this->save();

            $postpago_plan_service = $postpago_plan->service();

            if ($postpago_plan->all_sector_docs) {

                $counterAllDocs = $postpago_plan_service->countAllDocumentSectorInvoices();
            } else {

                $counterAllDocs = $postpago_plan_service->countDocumentSectorInvoices($postpago_plan->sector_doc_id);
            }

            $limitWasReached = $postpago_plan_service->verifyLimit($counterAllDocs);

            if ($postpago_plan_service->checkDateLimit()) {

                if ($limitWasReached < 0 && $postpago_plan->enable_overflow) {
                    $postpago_plan_service->processExceded($limitWasReached);
                }

                $postpago_plan_service->resetCounters();
                // $postpago_plan_service->resetStartDate();

                $this->refresh();

            } elseif ($limitWasReached <= 0 && !$postpago_plan->enable_overflow) {
                // TODO: Cambiar a Cuenta Prepago
                AccountPrepagoBagService::changeToPrepagoAccount($postpago_plan->company_id);

                throw new PrepagoBagsException("Usted llegó al limite de facturas permitidas por su plan. Puede adquirir una bolsa prepago para seguir emitiendo facturas");
            }


            \Log::debug("#Cantidad facturas hechas :" . $this->counter);
            return $this;
        } catch (PrepagoBagsException $ex) {
            
            throw new PrepagoBagsException($ex->getMessage());
            
        } catch (Exception $ex){
            
            \Log::debug("Error al Actualizar contador de facturas :" . $ex->getMessage() . " Line ". $ex->getLine() . "File ". $ex->getFile());
            bitacora_error('ControlPostpagoPlan:setPostPagoCounter', "Error al Actualizar contador de facturas :" . $ex->getMessage() . " Line ". $ex->getLine() . "File ". $ex->getFile());

        }
    }

    public static function getCompanyDocumentSectorByCode($fel_company_id, $sector_document_code)
    {
        return self::where('fel_company_id', $fel_company_id)->where('fel_doc_sector_id', $sector_document_code)->first();
    }

    public static function resetInvoiceAvailable($company_id)
    {
        return self::where('company_id', $company_id)->update([
            "invoice_number_available" => 0
        ]);
    }
    public static function resetCounter($company_id)
    {
        return self::where('company_id', $company_id)->update([
            "counter" => 0
        ]);
    }
}
