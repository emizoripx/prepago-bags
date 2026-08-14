<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;

class CompanyProductsTab extends Component
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
        $products = Product::with('fel_product')
            ->where('company_id', $this->company_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('product_key', 'like', '%' . $this->search . '%')
                      ->orWhere('notes', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('prepagobags::components.superadmin.company-products-tab', compact('products'));
    }
}
