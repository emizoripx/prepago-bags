<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use EmizorIpx\PrepagoBags\Http\Resources\CompanyDocumentSectorResource;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function clientsList(){

        $search = request('search' , "");
        $phase = request('phase' , "");

        if ($phase != "Testing" && $phase != "Production") $phase == "";

        
        $clientsPrepago = AccountPrepagoBags::join('company_user', 'company_user.company_id','=','fel_company.company_id')
                                            ->join('companies', 'fel_company.company_id', '=', 'companies.id')
                                            ->Join('users','users.id','=','company_user.user_id')
                    ->select('companies.id','fel_company.company_id' ,'companies.settings','companies.created_at' ,'fel_company.production', 'fel_company.is_postpago', 'fel_company.enabled', 'fel_company.phase','users.email')
                    ->where('fel_company.deleted_at', '=', null)
                    ->where('company_user.is_owner', 1)
                    ->orderBy('companies.id','desc')
                    ->when($search, function ($query, $search) {
                        return $query->where('users.email','like', "%".$search."%");
                    })
                    ->when($phase, function ($query, $phase) {
                        return $query->where('fel_company.phase', $phase);
                    })
                    ->paginate(30);

        
        return view('prepagobags::ListClients', compact('clientsPrepago', "search", "phase"));

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

    public function showInformation($companyId)
    {
        $company = AccountPrepagoBags::join('company_user', 'company_user.company_id', '=', 'fel_company.company_id')
        ->join('companies', 'fel_company.company_id', '=', 'companies.id')
        ->Join('users', 'users.id', '=', 'company_user.user_id')
        ->select('companies.id', 'fel_company.company_id', 'companies.settings', 'fel_company.production', 'fel_company.is_postpago', 'fel_company.enabled', 'fel_company.phase', 'users.email', 'companies.created_at')
        ->where('fel_company.deleted_at', '=', null)
            ->where('company_user.is_owner', 1)
            ->orderBy('companies.id', 'desc')
            ->where('companies.id', $companyId)
            ->first();
        $document_sectors = FelCompanyDocumentSector::whereFelCompanyId($companyId)->get(['invoice_number_available', 'accumulative', 'duedate', 'fel_doc_sector_id', 'counter']);

        
        $branches_number = \DB::table('fel_branches')->whereCompanyId($companyId)->count();
        $arrayNames = [
            1 => "Factura compra venta",
            3 => "Factura comercial de exportación",
            8 => "Factura de tasa cero por venta de libros y transporte internacional de carga",
            11 => "Factura sectores educativos",
            13 => "Servicios básicos",
            15 => "Factura Entidades Financieras",
            16 => "Factura de hoteles",
            20 => "Factura de exportación de minerales",
            21 => "Factura de venta interna de minerales",
            22 => "Factura telecomunicaciones",
            24 => "Nota débito crédito"
        ];

        return view('prepagobags::components.information' , compact('company', 'document_sectors','arrayNames', 'branches_number') );
    }
}
