<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CompanyTemplatesTab extends Component
{
    use WithPagination;

    public $company_id;
    public $search = '';

    public function mount($company_id)
    {
        $this->company_id = $company_id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $templates = DB::table('fel_templates')
            ->where('company_id', $this->company_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('display_name', 'like', '%' . $this->search . '%')
                      ->orWhere('blade_resource', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('prepagobags::components.superadmin.company-templates-tab', compact('templates'));
    }

    public function syncTemplates()
    {
        try {
            $company = \App\Models\Company::find($this->company_id);
            if (!$company) {
                session()->flash('sync_error', "No se encontró la empresa.");
                return;
            }

            $companyPrepago = \EmizorIpx\PrepagoBags\Models\AccountPrepagoBags::where('company_id', $this->company_id)->first();
            $isPartner = !empty($company->settings->is_b2b2b_partner);

            if ($isPartner) {
                $phase = ($companyPrepago && $companyPrepago->phase === 'Production') ? 'production' : 'testing';
                $partnerService = new \EmizorIpx\PrepagoBags\Services\PartnerOnboardingService($phase);
                
                if (!$partnerService->hasToken()) {
                    session()->flash('sync_error', "El Partner Token para ambiente '$phase' no está configurado.");
                    return;
                }

                $tenantKey = $company->settings->tenant_id ?? ($company->settings->id_number ?? $company->id);
                $partnerService->syncTemplates($tenantKey, $this->company_id);
                session()->flash('sync_message', "Plantillas sincronizadas correctamente desde el Partner.");
            } else {
                if (!$companyPrepago || !$companyPrepago->fel_company_token) {
                    session()->flash('sync_error', "No se encontraron credenciales FEL locales para esta empresa.");
                    return;
                }

                $credentials_repo = new \EmizorIpx\ClientFel\Repository\FelCredentialRepository();
                $credentials_repo
                    ->setCredentials($companyPrepago->fel_company_token->client_id, $companyPrepago->fel_company_token->client_secret)
                    ->setHost($companyPrepago->fel_company_token->getHost())
                    ->setCompanyId($this->company_id)
                    ->getTemplates();

                session()->flash('sync_message', "Plantillas sincronizadas correctamente.");
            }
            $this->resetPage();
        } catch (\Exception $e) {
            \Log::error("Error sincronizando plantillas en CompanyTemplatesTab: " . $e->getMessage());
            session()->flash('sync_error', "Error al sincronizar plantillas: " . $e->getMessage());
        }
    }

    public function downloadTemplate($id)
    {
        $template = DB::table('fel_templates')->find($id);

        if (!$template) {
            $this->addError('download', 'Plantilla no encontrada.');
            return;
        }

        try {
            // Check S3 first if configured
            if (config('filesystems.default') === 's3' || !empty(config('filesystems.disks.template-s3.key'))) {
                try {
                    if (\Illuminate\Support\Facades\Storage::disk('template-s3')->exists($template->blade_resource)) {
                        return \Illuminate\Support\Facades\Storage::disk('template-s3')->download($template->blade_resource);
                    }
                } catch (\Throwable $th) {
                    \Log::warning("Error verificando plantilla en S3: " . $th->getMessage());
                }
            }

            // Check if the view exists in resources/views
            $localViewPath = resource_path('views/' . $template->blade_resource);
            
            if (file_exists($localViewPath)) {
                return response()->download($localViewPath);
            }

            // Fallback: check if it's stored in storage/app
            if (\Illuminate\Support\Facades\Storage::exists($template->blade_resource)) {
                return \Illuminate\Support\Facades\Storage::download($template->blade_resource);
            }

            $this->addError('download', 'El archivo de la plantilla no se encontró físicamente ni en S3 ni localmente.');
        } catch (\Throwable $th) {
            \Log::error("Error descargando plantilla: " . $th->getMessage());
            $this->addError('download', 'Ocurrió un error al intentar descargar la plantilla (posible configuración incorrecta).');
        }
    }
}
