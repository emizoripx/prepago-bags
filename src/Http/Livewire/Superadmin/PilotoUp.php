<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use App\Models\Company;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Services\PurgeCompanyDataService;
use EmizorIpx\PrepagoBags\Services\PartnerOnboardingService;
use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
use Illuminate\Support\Str;
use LivewireUI\Modal\ModalComponent;
use Illuminate\Support\Facades\Log;

class PilotoUp extends ModalComponent
{
    public $company_id = null;
    public $company;

    public function mount($company_id)
    {
        info("ingresando ..  a piloto up ");
        $this->company_id = $company_id;
        $this->getCompany();
    }

    public function getCompany()
    {
        $this->company = Company::find($this->company_id);
    }

    public function update()
    {
        try {
            $company = Company::where('id', $this->company_id)->firstOrFail();
            $companyAccount = AccountPrepagoBags::where('company_id', $this->company_id)->firstOrFail();

            $purgeFeldataService = app(PurgeCompanyDataService::class);
            $purgeFeldataService->setCompanyId($this->company_id);

            // ELIMINA LOS DATOS DE PRUEBA
            $purgeFeldataService
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
            $company->invoices()->forceDelete();
            $company->clients()->forceDelete();
            $company->products()->forceDelete();
            $company->save();

            // RESET COUNTERS
            $purgeFeldataService->resetNumbersCounter($company);

            // ACTUALIZA CON PARTNER API O FALLBACK A CREDENCIALES DEMO
            $partnerService = new PartnerOnboardingService('testing');
            if ($partnerService->hasToken()) {
                $registerResponse = $partnerService->registerCompany($company, [
                    'environment_code' => 1,
                ]);
                $tenantId = $registerResponse['tenant_id'] ?? ($company->settings->id_number ?? $company->id);
                $partnerService->beginOperations($tenantId);
                $partnerService->syncParametrics($tenantId);
            } else {
                $credentials_repo = app(FelCredentialRepository::class);
                $credentials_repo
                    ->setCredentials(config('clientfel.client_id_demo'), config('clientfel.client_secret_demo'))
                    ->setHost(config('clientfel.host_demo'))
                    ->setCompanyId($this->company_id)
                    ->register()
                    ->updateFelCompany()
                    ->syncParametrics()
                    ->getBranches();
            }

            // CAMBIA A PRUEBAS PILOTO
            $companyAccount->update([
                'phase' => 'Piloto testing',
                'prefactura_number_counter' => 0,
            ]);

            $company->company_detail->service()->registerCompanySectorDocuments()->addBagGift();

            $usersToken = $company->tokens;
            foreach ($usersToken as $token) {
                $token->token = Str::random(64);
                $token->save();
            }

            $this->closeModal();
            $this->emit('companyCreated');
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Empresa migrada a Pruebas Piloto exitosamente']);

        } catch (\Exception $ex) {
            Log::error("Error en PilotoUp update: " . $ex->getMessage());
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Error al pasar a piloto: ' . $ex->getMessage()]);
        }
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.piloto-up');
    }
}