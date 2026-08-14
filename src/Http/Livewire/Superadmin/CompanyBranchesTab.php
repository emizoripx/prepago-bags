<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use EmizorIpx\ClientFel\Models\FelBranch;
use Hashids\Hashids;

class CompanyBranchesTab extends Component
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

        return view('prepagobags::components.superadmin.company-branches-tab', compact('branches'));
    }
}
