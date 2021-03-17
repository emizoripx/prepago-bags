<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function clientsList(){
        
     
        $clientsPrepago = AccountPrepagoBags::rightJoin('companies', 'account_prepago_bags.company_id', '=', 'companies.id')
                                            ->leftJoin('users','users.account_id','=','companies.account_id')
                                            // ->leftJoin('company_user', 'company_user.user_id','=','users.id')
                    ->select('account_prepago_bags.id', 'companies.settings' ,'account_prepago_bags.production', 'account_prepago_bags.is_postpago', 'account_prepago_bags.enabled', 'account_prepago_bags.phase')
                    ->where('account_prepago_bags.delete', '=', false)
                    // ->where('company_user.is_owner', 1)
                    ->simplePaginate(30);


        return view('prepagobags::ListClients', compact('clientsPrepago'));

    }
}
