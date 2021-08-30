<?php

namespace EmizorIpx\PrepagoBags\Http\Middleware;

use App\Models\CompanyToken;
use Closure;
use Illuminate\Http\Request;
use stdClass;

class VerifyAccountEnabled
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
            

            $company = $company_token->company;


            if ($company->company_detail->enabled == 'inactive' && ($request->isMethod('post') || $request->isMethod('put')) && !$request->is('api/v1/refresh')){
                $error = [
                    'message' => 'Su cuenta esta suspendida',
                    'errors' => new stdClass,
                ];

                return response()->json($error, 403);

            }
        }

        return $next($request);
    }
}
