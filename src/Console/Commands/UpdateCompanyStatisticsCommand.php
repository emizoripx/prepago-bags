<?php

namespace EmizorIpx\PrepagoBags\Console\Commands;

use EmizorIpx\PrepagoBags\Models\CompanyStatistic;
use Illuminate\Console\Command;
use DB;

class UpdateCompanyStatisticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:update-statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates company statistics like total clients, products, etc.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Updating company statistics...');

        // Get all active companies from fel_company
        $companies = DB::table('fel_company')->whereNull('deleted_at')->pluck('company_id');

        $bar = $this->output->createProgressBar(count($companies));

        foreach ($companies as $companyId) {
            $total_clients = DB::table('clients')->whereNull('deleted_at')->whereCompanyId($companyId)->count();    
            $total_products = DB::table('products')->whereNull('deleted_at')->whereCompanyId($companyId)->count();    
            $total_branches = DB::table('fel_branches')->whereCompanyId($companyId)->count();    
            $total_users = DB::table('company_user')->whereNull('deleted_at')->whereCompanyId($companyId)->count();    
            
            // Get due invoices sum and count
            $client_ids = DB::table('fel_company')->where('company_id', $companyId)->pluck('client_id')->filter();
            
            $due_invoices_amount = 0;
            $due_invoices_count = 0;

            if ($client_ids->count() > 0) {
                $clientId = $client_ids->first();
                $due_invoices = DB::table('invoices')->whereClientId($clientId)->where('balance','>',0)->select('balance')->get();
                $due_invoices_amount = $due_invoices->sum('balance');
                $due_invoices_count = $due_invoices->count();
            }

            CompanyStatistic::updateOrCreate(
                ['company_id' => $companyId],
                [
                    'total_clients' => $total_clients,
                    'total_products' => $total_products,
                    'total_branches' => $total_branches,
                    'total_users' => $total_users,
                    'total_due_invoices_amount' => $due_invoices_amount,
                    'total_due_invoices_count' => $due_invoices_count
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nCompany statistics updated successfully.");

        return 0;
    }
}
