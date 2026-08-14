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
