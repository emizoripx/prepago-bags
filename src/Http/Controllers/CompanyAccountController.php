<?php

namespace EmizorIpx\PrepagoBags\Http\Controllers;

use App\Jobs\Company\CreateCompanyToken;
use App\Models\Company;
use App\Models\User;
use EmizorIpx\ClientFel\Exceptions\ClientFelException;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use EmizorIpx\PrepagoBags\Exceptions\PrepagoBagsException;
use EmizorIpx\PrepagoBags\Http\Requests\StoreLinkedClientRequest;
use EmizorIpx\PrepagoBags\Http\Requests\SuspendClientRequest;
use EmizorIpx\PrepagoBags\Http\Requests\UpClientRequest;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Services\AccountPrepagoBagService;
use EmizorIpx\PrepagoBags\Services\PurgeCompanyDataService;
use EmizorIpx\PrepagoBags\Traits\DecodeIdsTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class CompanyAccountController extends Controller
{


    use DecodeIdsTrait;

    protected $purgeFeldataService;
    protected $credentials_repo;
    // protected $accountPrepagoBagsService;
    
    public function __construct(PurgeCompanyDataService $purgeFeldataService, FelCredentialRepository $credentials_repo)
    {
        $this->purgeFeldataService = $purgeFeldataService;
        $this->credentials_repo = $credentials_repo;
        // $this->accountPrepagoBagsService = $accountPrepagoBagService;
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
            ->purgeClients()
            ->purgeBranches()
            ->purgePOS()
            ->purgeCompanyDocumentSector()
            ->purgeActivityDocumentSector();

            // PURGAR DATOS DE EMIZOR5
            $company = Company::where('id', $company_id)->firstOrFail();
            \Log::debug('Company......');
            \Log::debug($company);
            $company->invoices()->forceDelete();
            $company->clients()->forceDelete();
            $company->products()->forceDelete();
            $company->save();

            // RESET COUNTERS
            $this->purgeFeldataService->resetNumbersCounter($company);

            // ACTUALIZA LAS CREDENCIALES Y OBTIENE LAS PARAMETRICAS
            $this->credentials_repo
            ->setCredentials($data['client_id'], $data['client_secret'])
            ->setHost($data['host'])
            ->setCompanyId($company_id)
            ->register()
            ->updateFelCompany()
            ->syncParametrics()
            ->getBranches();
            

            // CAMBIA A PRUEBAS PILOTO
            $companyAccount->update([
                'phase' => 'Piloto testing'
            ]);

            // AGREGA UNA BOLSA GRATIS
            // $this->accountPrepagoBagsService->addBagGift($company_id); //Modificar

            $company->company_detail->service()->registerCompanySectorDocuments()->addBagGift();

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

    public function productionUp (Request $request){
        
        $data = $request->all();

        \Log::debug("Data Production Up >>>>>>>>>>>>>");
        \Log::debug($data);

        try{

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
            ->purgeClients()
            ->purgeBranches()
            ->purgePOS()
            ->purgeSectorDocuments()
            ->purgeActivityDocumentSector();

            // PURGAR DATOS DE EMIZOR5
            $company = Company::where('id', $company_id)->firstOrFail();
            \Log::debug('Company......');
            \Log::debug($company);
            $company->invoices()->forceDelete();
            $company->clients()->forceDelete();
            $company->products()->forceDelete();
            $company->save();

            // RESET COUNTERS
            $this->purgeFeldataService->resetNumbersCounter($company);

            // ACTUALIZA LAS CREDENCIALES Y OBTIENE LAS PARAMETRICAS
            $this->credentials_repo
            ->setCredentials($data['client_id'], $data['client_secret'])
            ->setHost(config('clientfel.host_production'))
            ->setCompanyId($company_id)
            ->register()
            ->updateFelCompany()
            ->syncParametrics();

            \Log::debug("Plan type >>>>>>>>>>>>>>>");
            \Log::debug($data['account_type']);

            // PASAR A PRODUCCION
            $companyAccount->update([
                'production' => true,
                'phase' => 'Production',
                'is_postpago' => $data['account_type'] ? true : false,
                'counter_users' => $data['account_type'] ? 1 : 0
            ]);

            // AGREGA UNA BOLSA GRATIS
            \Log::debug($companyAccount->is_postpago);
            // if(!$companyAccount->is_postpago){
            //     \Log::debug('Es Prepago');
            //     $this->accountPrepagoBagsService->addBagGift($company_id);
            // }

            if($companyAccount->is_postpago){

                $companyAccount->service()
                ->resetInvoiceAvailable()
                ->resetCounter()
                ->savePostpagoPlan($data['plan_id'], $data['enable_overflow'] ?? null , $data['start_date'] );

                // TODO: sync branches

                $this->credentials_repo->getBranches(true);

            } else{
                $company->company_detail->service()
                ->registerCompanySectorDocuments()
                ->addBagGift()
                ->saveDuedateAndInvoiceAvailable($data['duedate'], $data['invoice_number_available']);

                $this->credentials_repo->getBranches();
            }

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

    public function linkedClient(StoreLinkedClientRequest $request){

        try{

            $data = $request->all();

            AccountPrepagoBags::where('company_id', $data['company_id'])->update([
                'client_id' => $data['company_client_id']
            ]);

            return response()->json([
                'success' => true
            ]);

        } catch(Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }

    }

    public function suspendClient(SuspendClientRequest $request){

        try{
            $data = $request->all();

            AccountPrepagoBags::where('company_id', $data['company_id'])->update([
                'enabled' => 'inactive'
            ]);

            return response()->json([
                'success' => true
            ]);

        } catch(Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }

    }

    public function upClient( UpClientRequest $request ){
        try{
            $data = $request->all();

            AccountPrepagoBags::where('company_id', $data['company_id'])->update([
                'enabled' => 'active'
            ]);

            return response()->json([
                'success' => true
            ]);


        } catch (Exception $ex){
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }
    }
}
