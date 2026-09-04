<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use App\Models\Company;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use EmizorIpx\PrepagoBags\Services\PartnerOnboardingService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateToPartnerModal extends Component
{
    use WithFileUploads;

    public $open = false;
    public $company_id;
    public $company_name;
    public $tenant_key;
    public $tenant_key_input = '';
    public $target_environment = 'testing'; // 'testing' | 'production'
    
    // Step and async status
    public $step = 1; // 1: Input Form, 2: Progress Checklist
    public $status = 'IDLE'; // IDLE, SYNCHRONIZING, COMPLETED, FAILED
    public $status_message = '';
    public $error_message = null;

    // Tenant key verification states
    public $is_verifying_tenant = false;
    public $tenant_verified = false;
    public $tenant_verification_message = '';
    public $tenant_error = null;

    // Existing data inspection flags
    public $has_existing_credentials = false;
    public $has_existing_certificate = false;
    public $has_existing_sin_token = false;
    public $has_existing_cuis = false;
    public $has_existing_parametrics = false;
    public $show_credentials_form = false;

    // Progress checks
    public $is_registered = false;
    public $credentials_uploaded = false;
    public $operations_started = false;
    public $parametrics_synced = false;

    // Partner configuration form
    public $system_code;
    public $sin_token;
    
    // Certificate
    public $certificate_file;
    public $certificate_password;
    public $is_already_partner = false;

    protected $listeners = ['openMigrateToPartnerModal' => 'openModal'];

    public function openModal($company_id)
    {
        $company = Company::find($company_id);
        if (!$company) return;

        $this->reset([
            'company_id', 'company_name', 'tenant_key', 'tenant_key_input', 'system_code', 'sin_token', 
            'certificate_file', 'certificate_password',
            'is_verifying_tenant', 'tenant_verified', 'tenant_verification_message', 'tenant_error',
            'has_existing_credentials', 'has_existing_certificate', 'has_existing_sin_token',
            'has_existing_cuis', 'has_existing_parametrics', 'show_credentials_form',
            'step', 'status', 'status_message', 'error_message',
            'is_registered', 'is_already_partner', 'credentials_uploaded', 'operations_started', 'parametrics_synced'
        ]);
        
        $this->company_id = $company->id;
        $this->company_name = $company->settings->name ?? 'Compañía sin nombre';
        $this->tenant_key = $company->settings->tenant_id ?? null;
        $this->is_already_partner = ($company->settings->partner_onboarding_status ?? null) === 'COMPLETED';
        
        // Determinar entorno inicial con fallback inteligente según tokens configurados
        $accountPrepago = AccountPrepagoBags::where('company_id', $company->id)->first();
        $phase = $accountPrepago->phase ?? 'Testing';
        $isTesting = in_array(strtolower($phase), ['testing', 'piloto testing', 'piloto']);
        $this->target_environment = $isTesting ? 'testing' : 'production';

        $prodService = new PartnerOnboardingService('production');
        $testService = new PartnerOnboardingService('testing');

        if ($this->target_environment === 'production' && !$prodService->hasToken() && $testService->hasToken()) {
            $this->target_environment = 'testing';
        }

        $this->step = 1;
        $this->status = 'IDLE';
        $this->open = true;

        // Si ya cuenta con tenant_id previo, pre-cargar y verificar
        if (!empty($this->tenant_key)) {
            $this->tenant_key_input = $this->tenant_key;
            $this->verifyTenantKey();
        }
    }

    public function updatedTargetEnvironment()
    {
        $this->tenant_error = null;
        if (!empty($this->tenant_key_input)) {
            $this->verifyTenantKey();
        }
    }

    public function closeModal()
    {
        $this->open = false;
    }

    public function toggleCredentialsForm()
    {
        $this->show_credentials_form = !$this->show_credentials_form;
    }

    public function verifyTenantKey()
    {
        $this->tenant_key_input = trim($this->tenant_key_input);
        if (empty($this->tenant_key_input)) {
            $this->resetTenantVerification();
            return;
        }

        $this->is_verifying_tenant = true;
        $this->tenant_error = null;
        $this->tenant_verified = false;

        $partnerService = new PartnerOnboardingService($this->target_environment);
        
        if (!$partnerService->hasToken()) {
            $this->tenant_error = "No hay token configurado para el entorno Partner ({$this->target_environment}).";
            $this->is_verifying_tenant = false;
            return;
        }

        $remoteStatus = $partnerService->checkCompanyStatus($this->tenant_key_input);

        if (!$remoteStatus) {
            $this->tenant_error = "No se encontró la compañía con el Tenant Key '{$this->tenant_key_input}' en el Core del Partner ({$this->target_environment}).";
            $this->has_existing_credentials = false;
            $this->tenant_verified = false;
            $this->is_verifying_tenant = false;
            return;
        }

        // Si se encontró con éxito en el Core:
        $this->tenant_verified = true;
        $this->tenant_key = $this->tenant_key_input;

        $onboarding = $remoteStatus['onboarding'] ?? $remoteStatus;
        $companyData = $onboarding['company'] ?? [];

        $this->has_existing_credentials = false;
        $this->has_existing_certificate = false;
        $this->has_existing_sin_token = false;
        $this->has_existing_cuis = false;
        $this->has_existing_parametrics = false;

        // Validar usando la estructura plana de booleanos (API Actual)
        if (isset($companyData['credentials_valid']) && $companyData['credentials_valid'] === true) {
            $this->has_existing_credentials = true;
            $this->has_existing_certificate = true;
            $this->has_existing_sin_token = true;
        }

        if (isset($companyData['parametrics_synced']) && $companyData['parametrics_synced'] === true) {
            $this->has_existing_parametrics = true;
        }

        if (!empty($remoteStatus['is_ready']) || !empty($onboarding['is_ready'])) {
            $this->has_existing_credentials = true;
            $this->has_existing_cuis = true;
            $this->has_existing_parametrics = true;
        }

        // Parsear CUIS activo desde la sucursal 0, POS 0
        if (isset($onboarding['branches']) && is_array($onboarding['branches']) && count($onboarding['branches']) > 0) {
            $firstBranch = $onboarding['branches'][0];
            if (isset($firstBranch['pos']) && is_array($firstBranch['pos']) && count($firstBranch['pos']) > 0) {
                $firstPos = $firstBranch['pos'][0];
                if (isset($firstPos['cuis_active']) && $firstPos['cuis_active'] === true) {
                    $this->has_existing_cuis = true;
                }
            }
        }

        $this->tenant_verification_message = "Tenant Key encontrado y verificado en el Core.";
        $this->is_verifying_tenant = false;
    }

    public function resetTenantVerification()
    {
        $this->tenant_key_input = '';
        $this->tenant_key = null;
        $this->tenant_verified = false;
        $this->tenant_error = null;
        $this->tenant_verification_message = '';
        $this->has_existing_credentials = false;
        $this->has_existing_certificate = false;
        $this->has_existing_sin_token = false;
        $this->has_existing_cuis = false;
        $this->has_existing_parametrics = false;
    }

    public function migrate()
    {
        // Validar únicamente si no cuenta con credenciales previas o si decidió cambiarlas
        $rules = [];
        if (!$this->has_existing_credentials || $this->show_credentials_form) {
            if (!$this->has_existing_sin_token || !empty($this->sin_token)) {
                $rules['sin_token'] = 'required|string';
            }
            if (!$this->has_existing_certificate || !empty($this->certificate_file)) {
                $rules['certificate_file'] = 'required|file|max:2048';
                $rules['certificate_password'] = 'required|string';
            }
        }
        
        if (!empty($rules)) {
            $this->validate($rules);
        }

        if ($this->is_already_partner && empty($this->certificate_file) && empty($this->sin_token)) {
            $this->addError('general', 'Debe ingresar un nuevo Certificado o un nuevo Token SIN para actualizar.');
            return;
        }

        try {
            $this->error_message = null;
            $company = Company::findOrFail($this->company_id);
            $accountPrepago = AccountPrepagoBags::where('company_id', $company->id)->first();
            
            $partnerService = new PartnerOnboardingService($this->target_environment);
            
            if (!$partnerService->hasToken()) {
                throw new \Exception("No hay token configurado para el entorno Partner ({$this->target_environment}). Configúralo en Settings.");
            }

            $this->step = 2;
            $this->status = 'SYNCHRONIZING';
            $this->status_message = 'Iniciando migración a Partner...';

            $tenantId = $this->tenant_key ?? ($company->settings->tenant_id ?? ($company->settings->id_number ?? $company->id));
            $isUniper = isset($company->settings->is_uniper) ? (int)$company->settings->is_uniper : 0;
            $modalityCode = (int)($accountPrepago->modality_code ?? ($company->settings->modality_code ?? 1));

            // Si ya era partner, evitamos hacer todo el onboarding
            if ($this->is_already_partner) {
                $this->is_registered = true;
                $this->has_existing_cuis = true;
                $this->has_existing_parametrics = true;
                $this->operations_started = true;
                $this->parametrics_synced = true;
                $this->tenant_verified = true;
                $this->status = 'COMPLETED';
                $this->status_message = '¡Credenciales actualizadas exitosamente!';
            }

            // Si ya fue verificado en el Core con su Tenant Key, omitimos el registro inicial
            if ($this->tenant_verified && !empty($this->tenant_key)) {
                $tenantId = $this->tenant_key;
                $this->is_registered = true;
            } else {
                // Settings for Migration
                $settings = [
                    'environment_code' => $this->target_environment === 'testing' ? 1 : 2,
                    'type_document_sectors_codes' => [1],
                    'document_type' => 5, // NIT
                    'modality_code' => $modalityCode,
                    'is_uniper' => $isUniper,
                    'platform' => 'emizor5',
                    'branch_code' => 0,
                    'municipality' => 'La Paz',
                    'zone' => 'S/N',
                    'address' => 'S/N',
                    'phone' => '00000000',
                ];
                if (!empty($this->system_code)) {
                    $settings['system_code'] = [(string)$this->system_code];
                }
                if (!empty($this->sin_token)) {
                    $settings['sin_token'] = $this->sin_token;
                }

                // 1. Registrar Empresa en Partner API
                $registerResponse = $partnerService->registerCompany($company, $settings);
                $tenantId = $registerResponse['tenant_id'] ?? ($registerResponse['company_key'] ?? $tenantId);
                $this->is_registered = true;
            }

            // 2. Subir Credenciales (solo si se cargaron nuevas o se actualizó el token)
            if (!empty($this->certificate_file) || !empty($this->sin_token)) {
                $partnerService->uploadCredentials(
                    $tenantId, 
                    $this->certificate_file, 
                    $this->certificate_password, 
                    $this->sin_token, 
                    $this->system_code
                );
                $this->credentials_uploaded = true;
            } else {
                $this->credentials_uploaded = $this->has_existing_credentials;
            }
            
            // 3. Begin Operations (Solicitud de CUIS si aún no lo tiene)
            if (!$this->has_existing_cuis) {
                try {
                    $partnerService->beginOperations($tenantId);
                } catch (\Exception $e) {
                    Log::warning("Migrate beginOperations: " . $e->getMessage());
                }
            }
            $this->operations_started = true;

            // 4. Sincronizar Paramétricas (si aún no están sincronizadas)
            if (!$this->has_existing_parametrics) {
                try {
                    $partnerService->syncParametrics($tenantId);
                } catch (\Exception $e) {
                    Log::warning("Migrate syncParametrics: " . $e->getMessage());
                }
            } else {
                $this->parametrics_synced = true;
            }

            // 5. Actualizar los flags de la compañía local
            $companySettings = $company->settings;
            $companySettings->is_b2b2b_partner = true;
            $companySettings->tenant_id = $tenantId;
            $companySettings->partner_onboarding_status = ($this->parametrics_synced && $this->operations_started) ? 'COMPLETED' : 'SYNCHRONIZING';
            $company->settings = $companySettings;
            $company->save();

            if ($companySettings->partner_onboarding_status === 'COMPLETED') {
                $this->status = 'COMPLETED';
                $this->status_message = '¡Compañía migrada y lista para operar como Partner!';
            } else {
                $this->status_message = 'Empresa vinculada a Partner. Sincronizando catálogos en segundo plano...';
            }

        } catch (\Exception $e) {
            Log::error("Error en MigrateToPartnerModal::migrate: " . $e->getMessage());
            $this->status = 'FAILED';
            $this->error_message = $e->getMessage();
            $this->addError('general', $e->getMessage());
        }
    }

    public function forceSyncParametrics()
    {
        try {
            $company = \App\Models\Company::find($this->company_id);
            if (!$company) return;
            
            $companyPrepago = \EmizorIpx\PrepagoBags\Models\AccountPrepagoBags::where('company_id', $this->company_id)->first();
            if (!$companyPrepago) {
                $this->addError('general', "No se encontró configuración prepago para esta empresa.");
                return;
            }

            $env = $company->settings->environment_code ?? 1;
            $envString = $env == 1 ? 'testing' : 'production';
            $partnerConfig = \EmizorIpx\PrepagoBags\Models\PartnerConfiguration::getConfig($envString);
            
            $host = '';
            $token = '';
            $tenantKey = null;

            if (!empty($company->settings->is_b2b2b_partner)) {
                $host = rtrim($partnerConfig['api_url'] ?? '', '/');
                $token = $partnerConfig['partner_token'] ?? '';
                $tenantKey = $company->settings->tenant_id ?? ($company->settings->id_number ?? $company->id);
            } else {
                if (!$companyPrepago->fel_company_token) {
                    $this->addError('general', "No se encontraron credenciales FEL locales para esta empresa.");
                    return;
                }
                $host = rtrim($partnerConfig['api_url'] ?? '', '/');
                if (empty($host)) {
                    $host = $companyPrepago->fel_company_token->getHost();
                }
                $token = $companyPrepago->fel_company_token->getAccessToken();
            }

            $parametricService = new \EmizorIpx\ClientFel\Services\Parametrics\Parametric(
                $token,
                $host,
                $tenantKey
            );

            $parametrics = \EmizorIpx\ClientFel\Utils\TypeParametrics::getAll();

            foreach ($parametrics as $parametric) {
                $parametricService->get($parametric, '', 'true');
                $response = $parametricService->getResponse();
                if (is_array($response)) {
                    \EmizorIpx\ClientFel\Models\FelParametric::saveParametrics($parametric, $this->company_id, $response, true);
                }
            }
            
            // Re-evaluar estado local
            if ($this->step === 1) {
                $this->verifyTenantKey();
            } else {
                $this->checkStatus();
            }
        } catch (\Exception $e) {
            \Log::warning("Force Sync Parametrics: " . $e->getMessage());
            $this->addError('general', "Error forzando sincronización: " . $e->getMessage());
        }
    }

    public function checkStatus()
    {
        if ($this->step !== 2 || $this->status !== 'SYNCHRONIZING') {
            return;
        }

        try {
            $company = Company::find($this->company_id);
            if (!$company) return;

            $onboardingStatus = $company->settings->partner_onboarding_status ?? null;

            // Check if webhook already completed it
            if ($onboardingStatus === 'COMPLETED') {
                $this->parametrics_synced = true;
                $this->status = 'COMPLETED';
                $this->status_message = '¡Sincronización completada exitosamente!';
                return;
            }

            // Alternatively poll Partner API if needed
            $tenantId = $company->settings->tenant_id ?? $company->settings->id_number;

            $partnerService = new PartnerOnboardingService($this->target_environment);
            if ($partnerService->hasToken() && !empty($tenantId)) {
                $statusRes = $partnerService->getOnboardingStatus($tenantId);
                
                $onboarding = $statusRes['onboarding'] ?? $statusRes;
                $isReady = $onboarding['is_ready'] ?? false;
                $parametricsSynced = $onboarding['company']['parametrics_synced'] ?? false;

                if ($isReady || $parametricsSynced) {
                    $this->parametrics_synced = true;
                    $this->status = 'COMPLETED';
                    $this->status_message = '¡Sincronización completada exitosamente!';

                    $settings = $company->settings;
                    $settings->partner_onboarding_status = 'COMPLETED';
                    $company->settings = $settings;
                    $company->save();
                }
            }

        } catch (\Exception $e) {
            Log::warning("MigrateToPartnerModal polling warning: " . $e->getMessage());
        }
    }

    public function finishMigration()
    {
        return redirect()->route('admin.companies.profile', ['company_id' => $this->company_id])
                         ->with('message', 'Compañía migrada exitosamente a Partner.');
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.migrate-to-partner-modal');
    }
}
