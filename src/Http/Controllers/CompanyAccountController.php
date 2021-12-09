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
                'phase' => 'Piloto testing',
                'prefactura_number_counter' => 0,
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

        \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> data : " . json_encode($data));

        try{

            $company_id = $data['company_id'];
            $company = Company::where('id', $company_id)->firstOrFail();

            \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> Change company : # " . $company_id . " to Plan  " . $data['account_type']);

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
            \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> test data IMPUESTOS deleted  : ");
            // PURGAR DATOS DE EMIZOR5
            
            $company->invoices()->forceDelete();
            $company->clients()->forceDelete();
            $company->products()->forceDelete();
            $company->save();
            \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> invoices, clients, products deleted");
            // RESET COUNTERS
            $this->purgeFeldataService->resetNumbersCounter($company);
            \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> counters reset");
            // ACTUALIZA LAS CREDENCIALES Y OBTIENE LAS PARAMETRICAS
            $this->credentials_repo
            ->setCredentials($data['client_id'], $data['client_secret'])
            ->setHost(config('clientfel.host_production'))
            ->setCompanyId($company_id)
            ->register()
            ->updateFelCompany()
            ->syncParametrics();

            \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> credentials to connect to fel set up");
            

            // PASAR A PRODUCCION
            $companyAccount->update([
                'production' => true,
                'phase' => 'Production',
                'is_postpago' => $data['account_type'] ? true : false,
                'counter_users' => $data['account_type'] ? 1 : 0,
                'prefactura_number_counter' => 0,
            ]);
            \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> production PLAN setup DB saved");
            // AGREGA UNA BOLSA GRATIS
            // if(!$companyAccount->is_postpago){
            //     \Log::debug('Es Prepago');
            //     $this->accountPrepagoBagsService->addBagGift($company_id);
            // }

            if($companyAccount->is_postpago){
                
                $companyAccount->service()
                ->registerCompanySectorDocuments()
                ->resetInvoiceAvailable()
                ->resetCounter()
                ->savePostpagoPlan($data['plan_id'], $data['enable_overflow'] ?? null , $data['start_date'] );
                \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> Plan POSTPAGO setup correctly");
                // TODO: sync branches

                $this->credentials_repo->getBranches(true);
                \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> PLAN POSTPAGO : Get branches from FEL done");
            } else{
                $company->company_detail->service()
                ->registerCompanySectorDocuments()
                ->addBagGift()
                ->saveDuedateAndInvoiceAvailable($data['duedate'], $data['invoice_number_available']);
                \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> Plan PREPAGO setup correctly");
                $this->credentials_repo->getBranches();
                \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> PLAN PREPAGO : Get branches from FEL done");
            }

            $usersToken = $company->tokens;
            foreach($usersToken as $token){
                $token->token = Str::random(64);
                $token->save();
            }
            \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> reset tokens done");
            \Log::debug(" PRODUCTION-UP >>>>>>>>>>>>> complete!");
            return response()->json([
                'success' => true
            ]);

        } catch(ClientFelException $ex){
            \Log::emergency(" PRODUCTION-UP >>>>>>>>>>>>> ERROR File: " . $ex->getFile() . " Line: " . $ex->getLine() . " Message: " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
            ]);
        }catch (PrepagoBagsException $ex) {
            \Log::emergency(" PRODUCTION-UP >>>>>>>>>>>>> ERROR File: " . $ex->getFile() . " Line: " . $ex->getLine() . " Message: " . $ex->getMessage());
            return response()->json([
                'success' => false,
                'msg' => $ex->getMessage()
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
