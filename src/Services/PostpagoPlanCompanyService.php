<?php

namespace EmizorIpx\PrepagoBags\Services;

use Carbon\Carbon;
use EmizorIpx\PrepagoBags\Models\PostpagoPlan;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use Exception;
use DB;
use EmizorIpx\PrepagoBags\Http\Resources\PostpagoPlanCompanyResource;
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

    public function checkLimitInvoices($invoiceCounter){
        if($this->postpago_plan->num_invoices == 0){
            return false;
        }

        return  $invoiceCounter >= $this->postpago_plan->num_invoices;
    }
    

    public function checkDateLimit(){

        $date_postpago_limit = Carbon::parse($this->postpago_plan->start_date)->addMonths($this->postpago_plan->frequency)->toDateString();
        $current_date = Carbon::now()->toDateString();

        \Log::debug("CheckLimitDate Limit Date");
        \Log::debug($date_postpago_limit);
        \Log::debug("CheckLimitDate Current Date");
        \Log::debug($current_date);

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

        if(!$plan){
            return null;
        }

        return new PostpagoPlanCompanyResource($plan);
    }

    public function verifyLimitProducts($products_counter){

        if($this->postpago_plan->num_products == 0){
            return false;
        }

        return  $products_counter >= $this->postpago_plan->num_products;
    }
    public function verifyLimitClients($clients_counter){

        if($this->postpago_plan->num_clients == 0){
            return false;
        }

        return  $clients_counter >= $this->postpago_plan->num_clients;
    }
    public function verifyLimitUsers($users_counter){

        if($this->postpago_plan->num_users == 0){
            return false;
        }

        return $users_counter >= $this->postpago_plan->num_users ;
    }

    public function verifyLimitBranches($branches_counter){

        if($this->postpago_plan->num_branches == 0){
            return false;
        }

        return  $branches_counter >= $this->postpago_plan->num_branches;
    }

    public function processExcededProducts( $counter_products ){

        $exceded = $this->postpago_plan->num_products - $counter_products;

        if( $this->postpago_plan->num_products != 0 && $exceded < 0){
            $excededAmount = $this->postpago_plan->prorated_products * abs($exceded);
            \Log::debug("Productos excedentes:  ". $exceded);
            \Log::debug("Monto excedentes:  ". $excededAmount);
    
            $this->postpago_plan->update([
                'postpago_exceded_amount' => $this->postpago_plan->postpago_exceded_amount + $excededAmount
            ]);
        }

    }

    public function processExcededClients( $counter_clients ){

        $exceded = $this->postpago_plan->num_clients - $counter_clients;

        if( $this->postpago_plan->num_clients != 0 && $exceded < 0){
            $excededAmount = $this->postpago_plan->prorated_clients * abs($exceded);
            \Log::debug("Clientes excedentes:  ". $exceded);
            \Log::debug("Monto excedentes:  ". $excededAmount);
    
            $this->postpago_plan->update([
                'postpago_exceded_amount' => $this->postpago_plan->postpago_exceded_amount + $excededAmount
            ]);
        }

    }

    public function processExcededUsers( $counter_users ){

        $exceded = $this->postpago_plan->num_users - $counter_users;

        if( $this->postpago_plan->num_users != 0 && $exceded < 0){
            $excededAmount = $this->postpago_plan->prorated_users * abs($exceded);
            \Log::debug("Usuarios excedentes:  ". $exceded);
            \Log::debug("Monto excedentes:  ". $excededAmount);
    
            $this->postpago_plan->update([
                'postpago_exceded_amount' => $this->postpago_plan->postpago_exceded_amount + $excededAmount
            ]);
        }

    }

    public function processExcededBranches( $counter_branches ){

        
        $exceded = $this->postpago_plan->num_branches - $counter_branches;

        if( $this->postpago_plan->num_branches != 0 && $exceded < 0){
            $excededAmount = $this->postpago_plan->prorated_branches * abs($exceded);

            \Log::debug("Sucursales excedentes:  ". $exceded);
            \Log::debug("Monto excedentes:  ". $excededAmount);

            $this->postpago_plan->update([
                'postpago_exceded_amount' => $this->postpago_plan->postpago_exceded_amont + $excededAmount
            ]);
        }

    }

    public function processExcededInvoices(){
        if ($this->postpago_plan->all_sector_docs) {
            
            $counterAllDocs = $this->countAllDocumentSectorInvoices();
        } else {
            
            $counterAllDocs = $this->countDocumentSectorInvoices($this->postpago_plan->sector_doc_id);
        }
        
        $limitWasReached = $this->verifyLimit($counterAllDocs);
        
        if($this->postpago_plan->num_invoices != 0 && $limitWasReached < 0){
            $this->processExceded($limitWasReached);
        }
    }

    public function checkLimitsAccount ($entity, $fel_company){
        $fel_company->refresh();
        if($fel_company->production && $fel_company->is_postpago){

            switch ($entity) {
                case 'product':
                    if($this->verifyLimitProducts($fel_company->counter_products) && !$this->postpago_plan->enable_overflow ){
                        return false;
                    }
                    
                    return true;

                    break;
                case 'client':
                    
                    if($this->verifyLimitClients($fel_company->counter_clients) && !$this->postpago_plan->enable_overflow ){
                        return false;
                    }
                    
                    return true;

                    break;
                
                default:
                    return true;
                    break;
            }

        }
        return true;

    }

}