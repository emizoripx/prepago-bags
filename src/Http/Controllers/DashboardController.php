<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function clientsList(){
        
     
        $clientsPrepago = AccountPrepagoBags::join('company_user', 'company_user.company_id','=','fel_company.company_id')
                                            ->join('companies', 'fel_company.company_id', '=', 'companies.id')
                                            ->Join('users','users.id','=','company_user.user_id')
                    ->select('companies.id','fel_company.company_id' ,'companies.settings' ,'fel_company.production', 'fel_company.is_postpago', 'fel_company.enabled', 'fel_company.phase','users.email')
                    ->where('fel_company.deleted_at', '=', null)
                    ->where('company_user.is_owner', 1)
                    ->orderBy('companies.id','desc')
                    ->simplePaginate(30);


        return view('prepagobags::ListClients', compact('clientsPrepago'));
        // return $clientsPrepago;

    }

    public function showForm($companyid)
    {
        $data = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
                            ->select(
                                'companies.id', 
                                'fel_company.company_id', 
                                'companies.settings'
                                )
                            ->where('fel_company.deleted_at', '=', null)
                            ->where('companies.id',$companyid)
                            ->first();

        return view('prepagobags::components.modal',["company" => $data]);
    }
    public function showForm2($companyid)
    {
        $data = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
                            ->select(
                                'companies.id', 
                                'fel_company.company_id', 
                                'companies.settings'
                                )
                            ->where('fel_company.deleted_at', '=', null)
                            ->where('companies.id',$companyid)
                            ->first();

        return view('prepagobags::components.modal2',["company" => $data]);
    }
}
