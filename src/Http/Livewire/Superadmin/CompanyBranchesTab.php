<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use EmizorIpx\ClientFel\Models\FelBranch;
use EmizorIpx\ClientFel\Models\FelPOS;
use Hashids\Hashids;

class CompanyBranchesTab extends Component
{
    use WithPagination;

    public $company_id;
    public $search = '';

    public $showPosModal = false;
    public $selectedBranch = null;
    public $branchPosList = [];

    public function mount($company_id)
    {
        $this->company_id = $company_id;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openPosModal($branchId)
    {
        $branch = FelBranch::where('company_id', $this->company_id)->find($branchId);
        if (!$branch) {
            session()->flash('sync_error', "Sucursal no encontrada.");
            return;
        }

        $this->selectedBranch = $branch;
        $this->branchPosList = FelPOS::where('company_id', $this->company_id)
            ->where(function ($q) use ($branch) {
                $q->where('branch_id', $branch->id)
                  ->orWhere('branch_id', $branch->codigo);
            })
            ->orderBy('codigo', 'asc')
            ->get();

        $this->showPosModal = true;
    }

    public function closePosModal()
    {
        $this->showPosModal = false;
        $this->selectedBranch = null;
        $this->branchPosList = [];
    }

    public function render()
    {
        $branches = FelBranch::where('company_id', $this->company_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('codigo', 'like', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                      ->orWhere('zona', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Preload POS counts en una sola consulta agregada (evita N+1)
        $collection = $branches->getCollection();
        $branchIds = $collection->pluck('id')->filter()->values()->toArray();
        $branchCodigos = $collection->pluck('codigo')->filter()->values()->toArray();
        $allBranchKeys = array_unique(array_merge($branchIds, $branchCodigos));

        $posCounts = [];
        if (!empty($allBranchKeys)) {
            $posCounts = FelPOS::where('company_id', $this->company_id)
                ->whereIn('branch_id', $allBranchKeys)
                ->selectRaw('branch_id, count(*) as total')
                ->groupBy('branch_id')
                ->pluck('total', 'branch_id')
                ->toArray();
        }

        $branches->getCollection()->transform(function ($b) use ($posCounts) {
            $b->pos_count = ($posCounts[$b->id] ?? null) ?: ($posCounts[$b->codigo] ?? 0);
            return $b;
        });

        return view('prepagobags::components.superadmin.company-branches-tab', compact('branches'));
    }

    public function syncBranches()
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
                $partnerService->syncCompanyDetailsAndBranches($tenantKey, $this->company_id);
                session()->flash('sync_message', "Sucursales y puntos de venta sincronizados correctamente desde el Partner.");
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
                    ->getBranches($companyPrepago->is_postpago ?? false);

                session()->flash('sync_message', "Sucursales y puntos de venta sincronizados correctamente.");
            }
            $this->resetPage();
        } catch (\Exception $e) {
            \Log::error("Error sincronizando sucursales en CompanyBranchesTab: " . $e->getMessage());
            session()->flash('sync_error', "Error al sincronizar sucursales: " . $e->getMessage());
        }
    }
}
