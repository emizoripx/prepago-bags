<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function clientsList(){
        

        $clientsPrepago = DB::table('account_prepago_bags')
                    ->join('companies', 'account_prepago_bags.company_id', '=', 'companies.id')
                    ->select('account_prepago_bags.id', 'companies.settings' ,'account_prepago_bags.production', 'account_prepago_bags.is_postpago', 'account_prepago_bags.enabled')
                    ->where('account_prepago_bags.delete', '=', false)
                    ->get();

        return view('prepagobags::ListClients', compact('clientsPrepago'));
        // return $clientsPrepago;

    }
}
