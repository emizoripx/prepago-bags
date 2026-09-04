<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use App\Models\Company;
use App\Models\User;
use App\Models\CompanyUser;
use App\Jobs\Account\CreateAccount;
use App\Jobs\Company\CreateCompany;
use EmizorIpx\PrepagoBags\Services\PartnerOnboardingService;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithFileUploads;
use DB;

class CreateCompanyModal extends Component
{
    use WithFileUploads;

    public $open = false;

    // Form fields
    public $company_name;
    public $id_number; // NIT
    public $first_name;
    public $last_name;
    public $email;
    public $password;
    
    // Partner configuration
    public $phase = 'testing'; // testing, piloto, production
    public $billing_type = 'computarizada'; // computarizada, electronica
    public $document_type = 5; // 5 = NIT
    public $system_code;
    public $is_unipersonal = false;
    public $sin_token;
    
    // Certificate
    public $certificate_file;
    public $certificate_password;

    protected $listeners = ['openCreateCompanyModal' => 'openModal'];

    protected $rules = [
        'company_name' => 'required|string|max:255',
        'id_number' => 'required|string|max:50',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email',
        'password' => 'nullable|string|min:6',
        'phase' => 'required|in:testing,piloto,production',
    ];

    public function mount()
    {
        // default values can go here
    }

    public function openModal()
    {
        $this->reset([
            'company_name', 'id_number', 'first_name', 'last_name', 
            'email', 'password', 'phase', 'billing_type', 'document_type', 
            'system_code', 'is_unipersonal', 'sin_token', 'certificate_file', 'certificate_password'
        ]);
        
        $this->phase = 'testing';
        $this->billing_type = 'computarizada';
        $this->document_type = 5;
        $this->is_unipersonal = false;
        
        $this->open = true;
    }

    public function closeModal()
    {
        $this->open = false;
    }

    public function save()
    {
        $this->validate();

        // Additional validation for Piloto/Production
        if (in_array($this->phase, ['piloto', 'production'])) {
            $this->validate([
                'system_code' => 'required|string',
                'sin_token' => 'required|string',
                'document_type' => 'required|numeric'
            ]);

            if ($this->billing_type == 'electronica') {
                $this->validate([
                    'certificate_file' => 'required|file|max:2048', // max 2MB
                    'certificate_password' => 'required|string'
                ]);
            }
        }

        try {
            DB::beginTransaction();

            $user = User::where('email', $this->email)->first();
            $company = null;

            if ($user) {
                // User exists, create company using standard CreateCompany job and link
                $flatRequest = [
                    'name' => $this->company_name,
                    'razon_social' => $this->company_name,
                    'nit' => $this->id_number,
                ];
                $company = (new CreateCompany($flatRequest, $user->account))->handle();
                
                CompanyUser::firstOrCreate([
                    'user_id' => $user->id,
                    'company_id' => $company->id,
                ], [
                    'is_owner' => 1,
                    'is_admin' => 1
                ]);
            } else {
                // User does not exist, use CreateAccount job but prevent login modification if possible
                // CreateAccount currently calls auth()->login(). To prevent changing superadmin session,
                // we'll capture current user ID and restore it.
                $currentUser = auth()->user();
                
                $requestData = [
                    'company' => [
                        'name' => $this->company_name,
                        'id_number' => $this->id_number,
                    ],
                    'user' => [
                        'first_name' => $this->first_name,
                        'last_name' => $this->last_name,
                        'email' => $this->email,
                        'password' => $this->password ? $this->password : '12345678', // fallback password
                    ]
                ];
                
                // CreateAccount requires the arrays flattened based on form logic in AccountController
                $flatRequest = [
                    'name' => $this->company_name,
                    'id_number' => $this->id_number,
                    'first_name' => $this->first_name,
                    'last_name' => $this->last_name,
                    'email' => $this->email,
                    'password' => $this->password ? $this->password : '12345678',
                    'terms_of_service' => true,
                    'privacy_policy' => true
                ];

                $account = (new CreateAccount($flatRequest, request()->ip()))->handle();
                
                // Restore Superadmin Session
                if ($currentUser) {
                    auth()->login($currentUser, true);
                }

                if ($account instanceof \App\Models\Account) {
                    $company = $account->companies()->first();
                } else {
                    throw new \Exception("Error al crear la cuenta local. ". json_encode($account));
                }
            }

            // Asegurar que settings locales contengan name y id_number
            $companySettings = $company->settings;
            $companySettings->name = $this->company_name;
            $companySettings->id_number = $this->id_number;
            $company->settings = $companySettings;
            $company->save();

            // Partner Onboarding Flow
            $partnerPhase = in_array($this->phase, ['testing', 'piloto']) ? 'testing' : 'production'; 
            $partnerService = new PartnerOnboardingService($partnerPhase);
            
            // Prepare Settings
            $settings = [
                'name' => $this->company_name,
                'business_name' => $this->company_name,
                'nit' => (string) $this->id_number,
                'environment_code' => $this->phase === 'testing' ? 1 : 2,
                'system_code' => empty($this->system_code) ? [] : [(string)$this->system_code],
                'is_uniper' => $this->is_unipersonal ? 1 : 0,
                'type_document_sectors_codes' => [1],
                'document_type' => empty($this->document_type) ? 5 : (int)$this->document_type,
                'modality_code' => $this->billing_type === 'electronica' ? 1 : 2,
                'sin_token' => empty($this->sin_token) ? '' : $this->sin_token,
                'platform' => 'emizor5',
                
                // Branch Data Defaults (required by Partner API for Casa Matriz)
                'branch_code' => 0,
                'municipality' => 'La Paz',
                'zone' => 'S/N',
                'address' => 'S/N',
                'phone' => '00000000',
            ];

            if ($partnerService->hasToken()) {
                // 1. Register Company
                $registerResponse = $partnerService->registerCompany($company, $settings);
                $tenantId = $registerResponse['tenant_id'] ?? ($registerResponse['company_key'] ?? $this->id_number);

                // 2. Configure Piloto / Production fields
                if (in_array($this->phase, ['piloto', 'production'])) {

                    // Upload Credentials if needed (Certificate or SIN Token)
                    $certificate = ($this->billing_type === 'electronica' && $this->certificate_file) ? $this->certificate_file : null;

                    if ($certificate || !empty($this->sin_token)) {
                        $partnerService->uploadCredentials(
                            $tenantId, 
                            $certificate, 
                            $this->certificate_password, 
                            $this->sin_token, 
                            $this->system_code
                        );
                    }
                    
                    // 3. Begin Operations
                    $partnerService->beginOperations($tenantId);
                    
                    // 4. Sync Parametrics
                    $partnerService->syncParametrics($tenantId);
                }

                // Local Initialization for Partner Companies
                try {
                    // Marcar empresa como cubierta por el partner B2B2B
                    $companySettings = $company->settings;
                    $companySettings->is_b2b2b_partner = true;
                    $companySettings->tenant_id = $tenantId;
                    $company->settings = $companySettings;
                    $company->save();

                    if (in_array($this->phase, ['piloto', 'production'])) {
                        try {
                            $partnerService->syncCompanyDetailsAndBranches($tenantId, $company->id);
                            $partnerService->syncTemplates($tenantId, $company->id);
                        } catch (\Exception $syncEx) {
                            \Log::warning("CreateCompanyModal: no se pudo sincronizar sucursales/templates: " . $syncEx->getMessage());
                        }
                    } else {
                        // Registrar sucursal por defecto en testing
                        \EmizorIpx\ClientFel\Models\FelBranch::updateOrCreate(
                            [
                                "company_id" => $company->id,
                                "codigo" => 0,
                            ],
                            [
                                "descripcion" => "Casa Matriz",
                                "zona" => "S/N",
                                "telefono" => "00000000",
                                "pais" => "Bolivia",
                                "ciudad" => "La Paz",
                                "municipio" => "La Paz"
                            ]
                        );
                    }

                    $company->company_detail->service()->registerCompanySectorDocuments()->addBagGift();
                } catch(\Exception $ex) {
                    \Log::debug("Problemas al inicializar detalles de compania localmente: " . $ex->getMessage());
                }

            } else {
                // 5. Sync local data using client-fel (Legacy Fallback)
                try {
                    $credential_repo = new \EmizorIpx\ClientFel\Repository\FelCredentialRepository();
                    $credential_repo->setCredentials(config('clientfel.client_id_demo'), config('clientfel.client_secret_demo'))
                                    ->setHost(config('clientfel.host_demo'))
                                    ->setCompanyId($company->id)
                                    ->register()
                                    ->updateFelCompany()
                                    ->syncParametrics()
                                    ->getBranches()
                                    ->getTemplates();
                    $company->company_detail->service()->registerCompanySectorDocuments()->addBagGift();
                } catch(\Exception $ex) {
                    \Log::debug("Problemas al sincronizar client-fel localmente en modo fallback: " . $ex->getMessage());
                }
            }

            DB::commit();

            $this->closeModal();
            $this->emit('companyCreated');
            
            // Success alert
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Compañía creada y provisionada exitosamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', $e->getMessage());
        }
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.create-company-modal');
    }
}
