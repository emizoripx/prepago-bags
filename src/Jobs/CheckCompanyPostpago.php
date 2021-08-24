<?php

namespace EmizorIpx\PrepagoBags\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use DB;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;

class CheckCompanyPostpago implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        nlog("Check postpago companies check");

        \Log::debug("Companies to Check");


        PostpagoPlanCompany::whereRaw('CURRENT_DATE() > DATE_ADD(start_date, INTERVAL frequency MONTH)')->cursor()->each( function ($company_plan){
            // $postpago_plan = PostpagoPlanCompany::where('company_id', $company_id)->first();
            \Log::debug("Company");
            $postpago_plan_service = $company_plan->service();

            if($postpago_plan_service->checkDateLimit()){

                AccountPrepagoBagService::changeToPostpagoAccount($company_plan->company_id);

                $postpago_plan_service->resetCounters();
                $postpago_plan_service->resetStartDate();

                \Log::debug('Reset postpago plan to company #'. $company_plan->company_id );
            }
        } );


    }
}
