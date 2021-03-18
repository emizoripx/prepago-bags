<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use App\Jobs\Company\CreateCompanyToken;
use App\Models\Company;
use App\Models\User;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use EmizorIpx\PrepagoBags\Services\PurgeCompanyDataService;
use EmizorIpx\PrepagoBags\Traits\DecodeIdsTrait;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class CompanyAccountController extends Controller
{


    use DecodeIdsTrait;

    protected $purgeFeldataService;
    protected $credentials_repo;
    protected $accountPrepagoBagsService;
    
    public function __construct(PurgeCompanyDataService $purgeFeldataService, FelCredentialRepository $credentials_repo, AccountPrepagoBagService $accountPrepagoBagService)
    {
        $this->purgeFeldataService = $purgeFeldataService;
        $this->credentials_repo = $credentials_repo;
        $this->accountPrepagoBagsService = $accountPrepagoBagService;
    }

    public function pilotUp(Request $request){

        $data = $request->all();
        try {
            $company_id = $data['company_id'];

            $companyAccount = AccountPrepagoBags::where('company_id', $company_id)->firstOrFail();

            if(!$companyAccount){
                throw new PrepagoBagsException('La compañia no existe');
            }

            $this->purgeFeldataService->setCompanyId($company_id);

            // ELIMINA LOS DATOS DE PRUEBA
            $this->purgeFeldataService
            ->purgeInvoices()
            ->purgeSyncProducts()
            ->purgeSinProducts()
            ->purgeSectorDocuments()
            ->purgeActivities()
            ->purgeCaptions()
            ->purgeClients();

            // PURGAR DATOS DE EMIZOR5
            $company = Company::where('id', $company_id)->firstOrFail();
            \Log::debug('Company......');
            \Log::debug($company);
            $company->invoices()->forceDelete();
            $company->clients()->forceDelete();
            $company->products()->forceDelete();
            $company->save();

            // ACTUALIZA LAS CREDENCIALES Y OBTIENE LAS PARAMETRICAS
            $this->credentials_repo
            ->setCredentials($data['client_id'], $data['client_secret'])
            ->setHost(config('app.host_demo'))
            ->setCompanyId($company_id)
            ->register()
            ->syncParametrics();
            

            // CAMBIA A PRUEBAS PILOTO
            $companyAccount->update([
                'phase' => 'Piloto testing'
            ]);

            // AGREGA UNA BOLSA GRATIS
            $this->accountPrepagoBagsService->addBagGift($company_id);

            $usersToken = $company->tokens;
            foreach($usersToken as $token){
                \Log::debug('tokens');
                \Log::debug($token);
                $token->token = Str::random(64);
                $token->save();
            }
            
            return response()->json([
                'success' => true
            ]);

        } catch(ClientFelException $ex){
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }catch (PrepagoBagsException $e) {
            return response()->json([
                'success' => false,
                'msg' => $e->getMessage()
            ]);
        }

        
    }
}
