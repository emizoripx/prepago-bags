<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function clientsList(){
        
     
        $clientsPrepago = AccountPrepagoBags::join('company_user', 'company_user.company_id','=','account_prepago_bags.company_id')
                                            ->join('companies', 'account_prepago_bags.company_id', '=', 'companies.id')
                                            ->Join('users','users.id','=','company_user.user_id')
                    ->select('companies.id','account_prepago_bags.company_id' ,'companies.settings' ,'account_prepago_bags.invoice_number_available','account_prepago_bags.production', 'account_prepago_bags.is_postpago', 'account_prepago_bags.enabled', 'account_prepago_bags.phase','users.email')
                    ->where('account_prepago_bags.delete', '=', false)
                    ->where('company_user.is_owner', 1)
                    ->orderBy('companies.id','desc')
                    ->simplePaginate(30);


        return view('prepagobags::ListClients', compact('clientsPrepago'));
        // return $clientsPrepago;

    }

    public function showForm($companyid)
    {
        $data = AccountPrepagoBags::join('companies', 'account_prepago_bags.company_id', '=', 'companies.id')
                            ->select(
                                'companies.id', 
                                'account_prepago_bags.company_id', 
                                'companies.settings'
                                )
                            ->where('account_prepago_bags.delete', '=', false)
                            ->where('companies.id',$companyid)
                            ->first();

        return view('prepagobags::components.modal',["company" => $data]);
    }
    public function showForm2($companyid)
    {
        $data = AccountPrepagoBags::join('companies', 'account_prepago_bags.company_id', '=', 'companies.id')
                            ->select(
                                'companies.id', 
                                'account_prepago_bags.company_id', 
                                'companies.settings'
                                )
                            ->where('account_prepago_bags.delete', '=', false)
                            ->where('companies.id',$companyid)
                            ->first();

        return view('prepagobags::components.modal2',["company" => $data]);
    }
}
