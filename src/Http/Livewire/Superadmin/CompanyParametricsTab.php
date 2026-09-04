<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use EmizorIpx\ClientFel\Models\FelActivity;
use EmizorIpx\ClientFel\Models\Parametric\SINProduct;
use EmizorIpx\ClientFel\Models\FelCaption;

class CompanyParametricsTab extends Component
{
    use WithPagination;

    public $company_id;
    public $search = '';
    public $parametricType = 'activities'; // activities, sinproducts, captions

    public function mount($company_id)
    {
        $this->company_id = $company_id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingParametricType()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function setParametricType($type)
    {
        $this->parametricType = $type;
        $this->search = '';
        $this->resetPage();
    }

    public function forceSyncParametrics()
    {
        try {
            $company = \App\Models\Company::find($this->company_id);
            if (!$company) return;
            
            $typeMap = [
                'activities' => [\EmizorIpx\ClientFel\Utils\TypeParametrics::ACTIVIDADES, \EmizorIpx\ClientFel\Utils\TypeParametrics::ACTIVIDADES_DOCUMENTO_SECTOR],
                'sinproducts' => [\EmizorIpx\ClientFel\Utils\TypeParametrics::PRODUCTOS_SIN],
                'captions' => [\EmizorIpx\ClientFel\Utils\TypeParametrics::LEYENDAS]
            ];
            $specific = $typeMap[$this->parametricType] ?? null;
            if (!$specific) {
                return;
            }
            
            $companyPrepago = \EmizorIpx\PrepagoBags\Models\AccountPrepagoBags::where('company_id', $this->company_id)->first();
            if (!$companyPrepago) {
                session()->flash('sync_error', "No se encontró configuración prepago para esta empresa.");
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
                    session()->flash('sync_error', "No se encontraron credenciales FEL locales para esta empresa.");
                    return;
                }
                $host = $companyPrepago->fel_company_token->getHost();
                $token = $companyPrepago->fel_company_token->getAccessToken();
            }

            $parametricService = new \EmizorIpx\ClientFel\Services\Parametrics\Parametric(
                $token,
                $host,
                $tenantKey
            );

            foreach ($specific as $parametric) {
                $parametricService->get($parametric, '', 'true');
                $response = $parametricService->getResponse();
                
                if (is_array($response)) {
                    \EmizorIpx\ClientFel\Models\FelParametric::saveParametrics($parametric, $this->company_id, $response, true);
                }
            }
            
            session()->flash('sync_message', 'Paramétricas descargadas y actualizadas correctamente de manera síncrona.');
        } catch (\Exception $e) {
            \Log::warning("Force Sync Parametrics from Tab: " . $e->getMessage());
            session()->flash('sync_error', "Error forzando sincronización: " . $e->getMessage());
        }
    }

    public function render()
    {
        $data = null;

        if ($this->parametricType === 'activities') {
            $data = FelActivity::where('company_id', $this->company_id)
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('codigo', 'like', '%' . $this->search . '%')
                          ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate(15);
        } elseif ($this->parametricType === 'sinproducts') {
            $data = SINProduct::where('company_id', $this->company_id)
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('codigo', 'like', '%' . $this->search . '%')
                          ->orWhere('codigoActividad', 'like', '%' . $this->search . '%')
                          ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate(15);
        } elseif ($this->parametricType === 'captions') {
            $data = FelCaption::where('company_id', $this->company_id)
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('codigoActividad', 'like', '%' . $this->search . '%')
                          ->orWhere('descripcion', 'like', '%' . $this->search . '%');
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate(15);
        }

        return view('prepagobags::components.superadmin.company-parametrics-tab', [
            'data' => $data
        ]);
    }
}
