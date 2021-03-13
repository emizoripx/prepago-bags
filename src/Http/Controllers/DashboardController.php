<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function clientsList(){
        

        // $clientsPrepago = DB::table('account_prepago_bags')
        //             ->join('users', 'account_prepago_bags.account_id', '=', 'users.account_id')
        //             ->select('account_prepago_bags.id', 'users.email' ,'account_prepago_bags.production')
        //             ->where('account_prepago_bags.delete', '=', false)
        //             ->get();

        // // $clientsPostpago = DB::table('account_postpago')
        // //                     ->join('users', 'account_postpago.account_id', '=', 'users.account_id')
        // //                     ->where('account_postpago.enabled', '=' ,true)
        // //                     ->select('users.id','users.email' , 'account_postpago.production')
        // //                     ->get();
        
        // // Log::debug($clientsPostpago);
        // // Log::debug($clientsPrepago);
        // // $clientsPrepago[] = $clientsPostpago;
        // return view('prepagobags::ListClients', compact('clientsPrepago'));
        // // return $clientsPrepago;

    }
}
