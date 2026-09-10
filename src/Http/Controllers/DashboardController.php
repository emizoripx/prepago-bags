<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use EmizorIpx\PrepagoBags\Models\PartnerConfiguration;

use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Models\FelCompanyDocumentSector;
use EmizorIpx\PrepagoBags\Models\PostpagoPlan;
use Illuminate\Routing\Controller;
use App\Models\Company;
use DB;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use EmizorIpx\PrepagoBags\Models\PrepagoBagsPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->guard('admin')->user();
            if (!$user || $user->is_superadmin != 1) {
                auth()->guard('admin')->logout();
                return redirect()->route('admin.catchall')->withErrors(['email' => 'Acceso denegado: Se requieren permisos de Super Administrador.']);
            }
            return $next($request);
        });
    }

    public function administrators()
    {
        $administrators = User::where('is_superadmin', 1)
            ->orderBy('id', 'desc')
            ->get();

        $currentAdmin = auth()->guard('admin')->user();

        return view('prepagobags::superadmin.administrators', compact('administrators', 'currentAdmin'));
    }

    public function storeAdministrator(Request $request)
    {
        $email = trim(strtolower($request->input('email')));

        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $email)->first();

        if ($user) {
            if ($user->is_superadmin == 1) {
                return redirect()->route('admin.administrators')->with('error', "El usuario {$email} ya cuenta con permisos de Super Administrador.");
            }

            $user->is_superadmin = 1;
            $user->save();

            $nombre = trim($user->first_name . ' ' . $user->last_name) ?: $email;
            return redirect()->route('admin.administrators')->with('message', "El usuario {$nombre} ({$email}) fue promovido a Super Administrador exitosamente.");
        }

        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'password' => 'required|string|min:8',
        ]);

        $account = Account::first();

        $newUser = new User();
        $newUser->account_id = $account ? $account->id : null;
        $newUser->first_name = $request->input('first_name');
        $newUser->last_name = $request->input('last_name');
        $newUser->email = $email;
        $newUser->password = Hash::make($request->input('password'));
        $newUser->is_superadmin = 1;
        $newUser->email_verified_at = Carbon::now();
        $newUser->phone = $request->input('phone', '');
        $newUser->save();

        return redirect()->route('admin.administrators')->with('message', "Nuevo Super Administrador {$newUser->first_name} {$newUser->last_name} ({$email}) creado exitosamente.");
    }

    public function toggleAdministrator(Request $request, $id)
    {
        $currentAdmin = auth()->guard('admin')->user();

        $user = User::findOrFail($id);

        if ($user->id === $currentAdmin->id) {
            return redirect()->route('admin.administrators')->with('error', 'Por seguridad, no puedes revocar tus propios permisos de Super Administrador mientras estás en sesión.');
        }

        $newStatus = $user->is_superadmin == 1 ? 0 : 1;
        $user->is_superadmin = $newStatus;
        $user->save();

        $statusLabel = $newStatus == 1 ? 'activados' : 'revocados';
        $nombre = trim($user->first_name . ' ' . $user->last_name) ?: $user->email;

        return redirect()->route('admin.administrators')->with('message', "Permisos de Super Administrador {$statusLabel} para {$nombre} ({$user->email}).");
    }


    public function dashboard()
    {
        $enrolled_quantity = Company::count();
        $trial_quantity = Company::count();
        $request_quantity = Company::count();

        return view('prepagobags::superadmin.dashboard', compact('enrolled_quantity', 'trial_quantity', 'request_quantity'));
    }

    public function partnerSettings()
    {
        $testingConfig = PartnerConfiguration::getConfig('testing');
        $productionConfig = PartnerConfiguration::getConfig('production');

        $partner_token_testing = $testingConfig['partner_token'] ?? '';
        $api_url_testing = $testingConfig['api_url'] ?? '';
        $partner_token_production = $productionConfig['partner_token'] ?? '';
        $api_url_production = $productionConfig['api_url'] ?? '';

        $partner_logs = [];
        $partner_webhook_count = 0;
        $partner_api_count = 0;
        
        $partner_webhooks = [
            url('api/webhooks/partner/events'),
            url('webhook/status-invoice'),
            url('webhook/update-branch'),
            url('webhook/update-pos'),
            url('webhook/update-parametrics'),
            url('webhook/status-package'),
            url('webhook/update-templates'),
            url('webhook/update-documents'),
        ];

        return view('prepagobags::superadmin.partner-settings', compact('partner_token_testing', 'api_url_testing', 'partner_token_production', 'api_url_production', 'partner_logs', 'partner_webhook_count', 'partner_api_count', 'partner_webhooks'));
    }

    public function companies()
    {
        return view('prepagobags::superadmin.companies');
    }

    public function invoices(Request $request, $company_id = null)
    {
        return view('prepagobags::superadmin.invoices', ["company_id" => $company_id]);
        
    }

    public function profile($company_id)
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
            ->firstOrFail();

        \View::share('company', $company);

        return view('prepagobags::superadmin.company-profile', compact('company'));
    }

    public function impersonate($company_id)
    {
        // Find the owner of this company
        $userCompany = DB::table('company_user')
            ->where('company_id', $company_id)
            ->where('is_owner', 1)
            ->whereNull('deleted_at')
            ->first();

        if (!$userCompany) {
            return redirect()->back()->with('error', 'No owner found for this company.');
        }

        $user = \App\Models\User::find($userCompany->user_id);
        
        if ($user) {
            Auth::login($user, true);
            return redirect('/')->with(['login' => 'true']); // Redirects to the main invoice ninja dashboard
        }

        return redirect()->back()->with('error', 'User not found.');
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
                ->select('users.id','users.first_name', 'users.last_name','users.phone','users.email','users.email_verified_at','users.confirmation_code','users.last_login','users.remember_token','users.has_password','users.is_superadmin','users.created_at','users.oauth_provider_id')
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
        $company = AccountPrepagoBags::whereCompanyId($company_id)->first();
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

    public function markVerified(Request $request, $id)
    {
        $user = \App\Models\User::find($id);

        if (!empty($user)) {

            $user->email_verified_at = now()->setTimezone(config('app.timezone'))->format("Y-m-d H:i:s");
            $user->save();
            
            return response()->json(["status"=>true, "messages"=> "actualizado satisfactoriamente", "user" => $user->email_verified_at]);    
        }
    }

    public function updatePartnerSettings(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'api_url_testing' => 'nullable|url',
            'partner_token_testing' => 'nullable|string',
            'api_url_production' => 'nullable|url',
            'partner_token_production' => 'nullable|string',
        ]);

        try {
            $apiUrlTesting = trim($request->input('api_url_testing', ''));
            $partnerTokenTesting = trim($request->input('partner_token_testing', ''));
            $apiUrlProduction = trim($request->input('api_url_production', ''));
            $partnerTokenProduction = trim($request->input('partner_token_production', ''));

            PartnerConfiguration::updateConfig(
                'testing',
                $apiUrlTesting,
                $partnerTokenTesting
            );

            PartnerConfiguration::updateConfig(
                'production',
                $apiUrlProduction,
                $partnerTokenProduction
            );

            return redirect()->back()->with('message', 'Configuración de Partner actualizada exitosamente en Base de Datos.');
        } catch (\Exception $e) {
            \Log::error("Error guardando Partner Settings: " . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar configuración: ' . $e->getMessage());
        }
    }

    public function testPartnerConnection(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'partner_token' => 'required|string',
            'api_url' => 'required|url',
        ]);

        try {
            $apiUrl = rtrim(trim($request->input('api_url')), '/');
            $client = new \GuzzleHttp\Client([
                'base_uri' => $apiUrl,
                'timeout'  => 5.0,
                'verify'   => false, // Optional for local testing with self-signed SSL
            ]);

            // Realizamos un ping a la raiz, asumiendo que el dominio debe responder algo (404, 200, etc) y no lanzar error de red/DNS.
            $response = $client->request('GET', '/');
            
            return response()->json([
                'success' => true,
                'message' => 'Conexión exitosa. El Host es accesible.'
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // If it's an HTTP error (4xx, 5xx) but connection worked, it's still "reachable" to some extent.
            if ($e->hasResponse()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Host accesible (Respondió HTTP ' . $e->getResponse()->getStatusCode() . ').'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'No se pudo conectar al Host: ' . $e->getMessage()
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Error de conexión: ' . $ex->getMessage()
            ]);
        }
    }
}
