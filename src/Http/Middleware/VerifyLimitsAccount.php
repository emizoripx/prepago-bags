<?php

namespace EmizorIpx\PrepagoBags\Http\Middleware;

use App\Models\CompanyToken;
use Closure;
use Illuminate\Http\Request;
use DB;
use EmizorIpx\PrepagoBags\Models\PostpagoPlanCompany;
use stdClass;

class VerifyLimitsAccount
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->header('X-API-TOKEN') && ($company_token = CompanyToken::with(['company'])->whereRaw('BINARY `token`= ?', [$request->header('X-API-TOKEN')])->first())){
            $company_detail = $company_token->company->company_detail;
            $postpago_plan = PostpagoPlanCompany::where('company_id', $company_token->company_id)->first();

            
            if($company_detail->production && $company_detail->is_postpago && $postpago_plan){
                $postpago_service = $postpago_plan->service();
                $path = $request->path();

                switch (substr($path,7)) {
                    case 'products':
                        if( $request->routeIs('api.products.store') && $postpago_service->verifyLimitProducts($company_detail->counter_products) && !$postpago_plan->enable_overflow ){
                            
                            return response()->json([
                                'message' => 'Ya llego al límite de Productos',
                                'errors' => new stdClass,
                            ], 403);
                        }
                        break;

                    case 'clients':
                        if( $request->routeIs('api.clients.store') && $postpago_service->verifyLimitClients($company_detail->counter_clients) && !$postpago_plan->enable_overflow ){

                            return response()->json([
                                'message' => 'Ya llego al límite de Clientes',
                                'errors' => new stdClass,
                            ], 403);
                        }
                        break;
                    case 'users':
                        if( $request->routeIs('api.users.store') && $postpago_service->verifyLimitUsers($company_detail->counter_users) && !$postpago_plan->enable_overflow ){

                            return response()->json([
                                'message' => 'Ya llego al límite de Usuarios',
                                'errors' => new stdClass,
                            ], 403);
                        }
                        break;
                    case 'branches':
                        if( $request->routeIs('api.branches.store') && $postpago_service->verifyLimitClients($company_detail->counter_clients) && !$postpago_plan->enable_overflow ){

                            return response()->json([
                                'message' => 'Ya llego al límite de Sucursales',
                                'errors' => new stdClass,
                            ], 403);
                        }
                        break;
                    
                    default:
                        # code...
                        break;
                }
            }



        }

        return $next($request);
    }
}
