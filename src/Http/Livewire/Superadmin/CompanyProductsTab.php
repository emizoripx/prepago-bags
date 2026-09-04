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
        $products = Product::leftJoin('fel_sync_products', function ($join) {
                $join->on('products.id', '=', 'fel_sync_products.id_origin')
                     ->whereNull('fel_sync_products.deleted_at');
            })
            ->where('products.company_id', $this->company_id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('products.product_key', 'like', '%' . $this->search . '%')
                      ->orWhere('products.notes', 'like', '%' . $this->search . '%')
                      ->orWhere('fel_sync_products.codigo_producto_sin', 'like', '%' . $this->search . '%')
                      ->orWhere('fel_sync_products.codigo_actividad_economica', 'like', '%' . $this->search . '%');
                });
            })
            ->select(
                'products.id',
                'products.product_key',
                'products.notes',
                'products.cost',
                'products.price',
                'products.created_at',
                'fel_sync_products.codigo_producto_sin',
                'fel_sync_products.codigo_actividad_economica',
                'fel_sync_products.codigo_unidad',
                'fel_sync_products.nombre_unidad'
            )
            ->orderBy('products.id', 'desc')
            ->paginate(15);

        return view('prepagobags::components.superadmin.company-products-tab', compact('products'));
    }
}
