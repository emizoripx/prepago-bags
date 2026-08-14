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
