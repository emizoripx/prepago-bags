<?php

namespace EmizorIpx\PrepagoBags\Services;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Models\PostpagoPlan;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use Exception;
use DB;
use Hashids\Hashids;
use Illuminate\Support\Facades\Log;

class PostpagoPlanCompanyService {


    protected $postpago_plan;

    public function __construct( PostpagoPlanCompany $postpago_plan )
    {
        $this->postpago_plan = $postpago_plan;
        
    }

    public function countAllDocumentSectorInvoices(){
        $counterInvoices = DB::table('fel_company_document_sectors')->where('company_id', $this->postpago_plan->company_id)->pluck('postpago_counter');

        $totalCounter = $counterInvoices->sum();

        \Log::debug("All invoices ". $totalCounter);

        return $totalCounter;
    }

    public function countDocumentSectorInvoices(){
        $counterInvoices = DB::table('fel_company_document_sectors')
                            ->where('company_id', $this->postpago_plan->company_id)
                            ->where('fel_doc_sector_id', $this->postpago_plan->sector_doc_id)
                            ->pluck('postpago_counter');

        \Log::debug("Counter invoices ". $counterInvoices[0]);
        
        return $counterInvoices[0];
    }

    public function verifyLimit( $invoiceCounter ){

        return $this->postpago_plan->num_invoices - $invoiceCounter;

    }
    

    public function checkDateLimit(){

        $date_postpago_limit = Carbon::parse($this->postpago_plan->start_date)->addMonths($this->postpago_plan->frequency);
        $current_date = Carbon::now()->toDateString();

        return $current_date > $date_postpago_limit;
    }

    public function processExceded( $exceded ){

        $excededAmount = $this->postpago_plan->prorated_invoice * abs($exceded);

        \Log::debug("Facturas excedentes:  ". $exceded);
        \Log::debug("Monto excedentes:  ". $excededAmount);

        $this->postpago_plan->update([
            'postpago_exceded_amount' => $this->postpago_plan->postpago_exceded_amount + $excededAmount
        ]);

    }

    public function resetCounters(){
        DB::table('fel_company_document_sectors')->where('company_id', $this->postpago_plan->company_id)->update([
            'postpago_counter' => 0
        ]);
    }

    public function resetStartDate(){
        $this->postpago_plan->update([
            'start_date' => Carbon::now()->toDateTimeString()
        ]);
    }

    public static function getPostpagoPlan($company_id){
        $plan = PostpagoPlanCompany::where('company_id', $company_id)->first();

        return $plan;
    }

}