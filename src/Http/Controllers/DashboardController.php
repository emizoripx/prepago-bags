<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use EmizorIpx\PrepagoBags\Http\Resources\CompanyDocumentSectorResource;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Models\PostpagoPlan;
use Illuminate\Routing\Controller;
use DB;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPayment;

class DashboardController extends Controller
{
    public function clientsList(){

        $search = request('search' , "");
        $phase = request('phase' , "");

        $user_hash = [ 'hash' => request()->header('user')];


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
                    ->paginate(15);

        
        return view('prepagobags::ListClients', compact('clientsPrepago', "search", "phase", "user_hash"));

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
        
        $document_sectors = FelCompanyDocumentSector::whereCompanyId($companyid)->get([ 'fel_doc_sector_id','invoice_number_available']);

        $postpago_plans = PostpagoPlan::pluck('name', 'id');

        return view('prepagobags::components.phaseProduction',["company" => $data, "document_sectors" => $document_sectors, "arrayNames" => TypeDocumentSector::ARRAY_NAMES, 'plans' => $postpago_plans]);
    }

    public function showInformation($companyId)
    {
        $company = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
        ->select(
            'companies.id',
            'fel_company.company_id',
            'fel_company.client_id',
            'fel_company.production',
            'fel_company.is_postpago',
            'companies.settings'
        )
            ->where('fel_company.deleted_at', '=', null)
            ->where('companies.id', $companyId)
            ->first();

        $document_sectors = FelCompanyDocumentSector::whereCompanyId($companyId)->get(['invoice_number_available', 'accumulative', 'duedate', 'fel_doc_sector_id', 'counter', 'postpago_limit','postpago_exceded_limit','postpago_counter']);

        $prepago_bags_payments = PrepagoBagsPayment::join('prepago_bags','prepago_bags.id','=','prepago_bags_payments.prepago_bag_id')
                                                    ->where('prepago_bags_payments.company_id',$companyId)
                                                    ->where('prepago_bags_payments.status_code','!=',-1)
                                                    ->groupBy('sector_document_type_code')
                                                    ->select(DB::raw('sector_document_type_code,max(prepago_bags_payments.paid_on) as purchase_last_date'))
                                                    ->pluck('purchase_last_date','sector_document_type_code');
        
        $sum_all_documents = 0;
        $sum_all_clients = 0;
        $sum_all_products = 0;
        $sum_all_branches = 0;
        $sum_all_users = 0;

        $post_pago_plan_companies = [];
        $due_invoices = [];

        if ($company->is_postpago) {
            $sum_all_documents = collect($document_sectors)->sum("postpago_counter");
            $sum_all_clients = DB::table('clients')->whereNull('deleted_at')->whereCompanyId($companyId)->count();    
            $sum_all_products = DB::table('products')->whereNull('deleted_at')->whereCompanyId($companyId)->count();    
            $sum_all_branches = DB::table('fel_branches')->whereCompanyId($companyId)->count();    
            $sum_all_users = DB::table('company_user')->whereNull('deleted_at')->whereCompanyId($companyId)->count();    
            
            $post_pago_plan_companies = PostpagoPlanCompany::whereCompanyId($companyId)->first();
            $due_invoices = DB::table('invoices')->whereClientId($company->client_id)->where('balance','>',0)->get(['number','date','balance']);

        }

        $arrayNames = [
            1 => "Factura compra venta",
            2 => "Recibo de Alquiler de Bienes Inmuebles",
            3 => "Factura comercial de exportación",
            4 => "Factura Comercial de Exportación en Libre Consignación",
            5 => "Factura de Zona Franca",
            6 => "Factura de Servicio Turístico y Hospedaje",
            7 => "Factura de Comercialización de Alimentos – Seguridad ",
            8 => "Factura de tasa cero por venta de libros y transporte internacional de carga",
            9 => "Factura de Compra y Venta de Moneda Extranjera ",
            10 => "Factura Dutty Free",
            11 => "Factura sectores educativos",
            12 => "Factura de Comercialización de Hidrocarburos",
            13 => "Servicios básicos",
            14 => "Factura Productos Alcanzados por el ICE",
            15 => "Factura Entidades Financieras",
            16 => "Factura de hoteles",
            17 => "Factura de Hospitales/Clínicas",
            18 => "Factura de Juegos de Azar",
            19 => "Factura Hidrocarburos",
            20 => "Factura de exportación de minerales",
            21 => "Factura de venta interna de minerales",
            22 => "Factura telecomunicaciones",
            23 => "Factura Prevalorada",
            24 => "Nota débito crédito",
            25 => "Factura de Productos Nacionales",
            26 => "Factura de Productos Nacionales - ICE",
            27 => "Factura Regimen 7RG",
            28 => "Factura Comercial de Exportación de Servicios"
        ];

        return view('prepagobags::components.information' , compact('company', 'document_sectors','arrayNames', 'post_pago_plan_companies', 'sum_all_documents','sum_all_clients','sum_all_products','sum_all_branches','sum_all_users','due_invoices', 'prepago_bags_payments') );
    }

    public function showFormLikend($company_id){
        $company = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
                    ->select(
                        'companies.id', 
                        'fel_company.company_id',
                        'fel_company.client_id',
                        'fel_company.production', 
                        'companies.settings'
                        )
                    ->where('fel_company.deleted_at', '=', null)
                    ->where('companies.id',$company_id)
                    ->first();
        
        $clients = DB::table('fel_clients')
                        ->join('clients', 'fel_clients.id_origin', '=', 'clients.id')
                        ->leftJoin('fel_company', 'clients.id', '=', 'fel_company.client_id')
                        ->where('fel_clients.company_id', config('prepagobag.company_admin_id'))
                        ->whereNull('fel_company.client_id')
                        ->pluck('fel_clients.business_name', 'clients.id');
        
        return view('prepagobags::components.linkedModal', compact('company', 'clients'));
    }
}
