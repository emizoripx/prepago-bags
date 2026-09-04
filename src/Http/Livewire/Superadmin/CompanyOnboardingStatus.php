<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use EmizorIpx\PrepagoBags\Services\PartnerOnboardingService;
use App\Models\Company;
use Livewire\Component;

class CompanyOnboardingStatus extends Component
{
    public $company_id;
    public $tenant_id;
    public $phase;
    public $status = null;
    public $loading = false;
    public $error = null;

    public function mount($company_id, $tenant_id, $phase)
    {
        $this->company_id = $company_id;
        $this->tenant_id = $tenant_id;
        $this->phase = $phase;
    }

    public function checkStatus()
    {
        $this->loading = true;
        $this->error = null;
        
        try {
            $partnerPhase = in_array($this->phase, ['Testing', 'Piloto testing', 'testing', 'piloto']) ? 'testing' : 'production';
            $partnerService = new PartnerOnboardingService($partnerPhase);
            
            $response = $partnerService->getOnboardingStatus($this->tenant_id);
            
            if (isset($response['status']) && $response['status']) {
                $this->status = $response['onboarding'] ?? null;
            } else {
                $this->error = $response['message'] ?? 'Error desconocido al obtener el status';
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        }
        
        $this->loading = false;
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.company-onboarding-status');
    }
}
