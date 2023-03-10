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
use Illuminate\Http\Request;
class DashboardController extends Controller
{
    public function clientsList(){

        $search = request('search' , "");
        $phase = request('phase' , "");

        $user_hash = [ 'hash' => request()->header('user')];


        if ($phase != "Testing" && $phase != "Production") $phase == "";

        
        $clientsPrepago = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
                    ->select('companies.id','fel_company.company_id' ,'companies.settings','companies.created_at' ,'fel_company.production', 'fel_company.is_postpago', 'fel_company.enabled', 'fel_company.phase')
                    ->orderBy('companies.id','desc')
                    ->when($search, function ($query, $search) {
                        return $query->whereRaw(\DB::raw('MATCH (companies.settings) AGAINST ("'.$search. '" IN NATURAL LANGUAGE MODE)'));
                    })
                    ->when($phase, function ($query, $phase) {
                        return $query->where('fel_company.phase', $phase);
                    })->paginate(15);
        
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
            28 => "Factura Comercial de Exportación de Servicios",
            29 => "Factura Comercial de Exportación de Servicios",
            34 => "Factura compra venta Bonificaciones",
            35 => "Factura compra venta Bonificaciones",
            38 => "Factura compra venta Bonificaciones",
            46 => "Factura compra venta Bonificaciones",
            51 => "Factura compra venta Bonificaciones",
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

    public function showFormSuspend($company_id){
        $company = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
                    ->select(
                        'companies.id', 
                        'fel_company.company_id',
                        'fel_company.enabled',
                        'companies.settings'
                        )
                    ->where('fel_company.deleted_at', '=', null)
                    ->where('companies.id',$company_id)
                    ->first();
        return view('prepagobags::components.suspendModal', compact('company'));
    }
    public function showFormUp($company_id){
        $company = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
                    ->select(
                        'companies.id', 
                        'fel_company.company_id',
                        'fel_company.enabled',
                        'companies.settings'
                        )
                    ->where('fel_company.deleted_at', '=', null)
                    ->where('companies.id',$company_id)
                    ->first();
        return view('prepagobags::components.upClientModal', compact('company'));
    }
    public function showUsers($company_id)
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
            ->where('companies.id', $company_id)
            ->first();

        $users = \DB::table('company_user')
                ->join('users','users.id','company_user.user_id')
                ->whereCompanyId($company_id)
                ->select('users.first_name', 'users.last_name','users.phone','users.email','users.email_verified_at','users.confirmation_code','users.last_login','users.remember_token','users.has_password','users.is_superadmin','users.created_at','users.oauth_provider_id')
                ->get();
        

        return view('prepagobags::components.users', compact('users','company'));
    }
    public function showSettings($company_id)
    {

        $company = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
        ->select(
            'companies.id',
            'fel_company.company_id',
            'fel_company.client_id',
            'fel_company.production',
            'fel_company.is_postpago',
            'companies.settings',
            'fel_company.level_invoice_number_generation'
        )
            ->where('fel_company.deleted_at', '=', null)
            ->where('companies.id', $company_id)
            ->first();
        $invoice_generators = \DB::table('invoice_generator_number')->whereCompanyId($company_id)->orderBy('code')->get();
        $sector_documents  = \DB::table('fel_sector_document_types')->selectRaw('distinct codigo, documentoSector')->whereCompanyId($company_id)->pluck('documentoSector','codigo')->toArray();
        return view('prepagobags::components.settings', compact('company', 'invoice_generators', 'sector_documents'));

    }
    public function updateSettings(Request $request, $company_id)
    {

        info("actualizando settings   " ,$request->only(['invoice_generators']));
        $company = AccountPrepagoBags::find($company_id);
        $company->level_invoice_number_generation = $request->level_invoice_number_generation;
        $company->save();

        switch ($request->level_invoice_number_generation) {
            case 1:
                $codes = \DB::table('fel_branches')->whereCompanyId($company_id)->pluck("codigo");
                break;
            case 2:
                $codes = \DB::table('fel_branches')->selectRaw(\DB::raw('concat(ifnull(fel_branches.codigo,0),"-",ifnull(tmp.codigo,0)) as codigo'))
                ->leftJoin(\DB::raw(' (select 0  as codigo, ' . $company_id . ' as company_id union select codigo, company_id from fel_pos where company_id = ' . $company_id . ') as tmp'), 'tmp.company_id', 'fel_branches.company_id')
                ->where('fel_branches.company_id',$company_id)
                ->pluck("codigo");

                break;
            case 3:
                $codes = \DB::table('fel_branches')
                ->selectRaw(\DB::raw('concat(ifnull(fel_branches.codigo,2),"-",ifnull(tmp.codigo,0), "-", ifnull(fel_sector_document_types.codigo,2)) as codigo '))
                ->leftJoin(\DB::raw(' (select 0  as codigo, '.$company_id.' as company_id union select codigo, company_id from fel_pos where company_id = '.$company_id.') as tmp'), 'tmp.company_id', 'fel_branches.company_id')
                ->leftJoin('fel_sector_document_types', 'fel_sector_document_types.codigoSucursal', 'fel_branches.codigo')
                ->where('fel_branches.company_id',$company_id)
                ->where('fel_sector_document_types.company_id',$company_id)
                ->pluck("codigo");

                break;
            default:
                $codes = [];
                break;
        }

        if (!empty($codes)) {

            $existence_codes = \DB::table('invoice_generator_number')->whereCompanyId($company_id)->whereIn('code',$codes)->pluck('code')->toArray();

            $create_new_codes = [];
            foreach ($codes as $code ) {
                if(!in_array($code, $existence_codes)) {
                    $create_new_codes[] = [
                        "company_id"=>$company_id,
                        "code"=>$code,
                        "number_counter"=>1
                    ];
                }
            }
            if(!empty($create_new_codes)) {
                \DB::table('invoice_generator_number')->insert($create_new_codes);
            }

        }
        if (!is_null($request->invoice_generators)) {

            foreach ($request->invoice_generators as $key => $value) {
                \DB::table('invoice_generator_number')->whereCode($key)->whereCompanyId($company_id)->update(['number_counter' => $value]);
            }
        }

        return redirect()->route('dashboard.getClients');
    }
}
