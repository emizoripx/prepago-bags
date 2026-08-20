<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use App\Jobs\Util\UploadAvatar;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadCompanyLogoModal extends Component
{
    use WithFileUploads;

    public $isOpen = false;
    public $company_id;
    public $company;
    public $logo;
    public $current_logo_url;

    protected $listeners = [
        'openUploadCompanyLogoModal' => 'openModal',
        'closeUploadCompanyLogoModal' => 'closeModal',
    ];

    public function mount($company_id = null)
    {
        if ($company_id) {
            $this->company_id = $company_id;
            $this->loadCompany();
        }
    }

    public function openModal($company_id)
    {
        $this->company_id = $company_id;
        $this->logo = null;
        $this->loadCompany();
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->logo = null;
        $this->resetErrorBag();
    }

    public function loadCompany()
    {
        $this->company = Company::find($this->company_id);
        if ($this->company) {
            $settings = $this->company->settings;
            $logo = $settings->company_logo ?? null;
            if ($logo && !str_starts_with($logo, 'http')) {
                $logo = url($logo);
            }
            $this->current_logo_url = $logo;
        }
    }

    public function save()
    {
        $this->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
        ], [
            'logo.required' => 'Debes seleccionar o arrastrar un archivo de imagen.',
            'logo.image' => 'El archivo debe ser una imagen válida.',
            'logo.mimes' => 'Formatos permitidos: JPG, PNG, GIF, SVG, WEBP.',
            'logo.max' => 'El tamaño máximo permitido es de 5MB.',
        ]);

        try {
            $company = Company::findOrFail($this->company_id);

            $path = (new UploadAvatar($this->logo->getRealPath(), $company->company_key))->handle();

            if (!$path) {
                $storedPath = $this->logo->store('companies/' . $company->company_key, config('filesystems.default', 'public'));
                $path = Storage::disk(config('filesystems.default', 'public'))->url($storedPath);
            }

            $settings = $company->settings;
            $settings->company_logo = $path;
            $company->settings = $settings;
            $company->save();

            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Logo de la empresa actualizado correctamente']);
            $this->emit('logoUpdated');

            return redirect()->route('admin.companies.profile', $this->company_id);

        } catch (\Exception $e) {
            Log::error("Error al subir logo de empresa: " . $e->getMessage());
            $this->addError('logo', 'Error al procesar la imagen: ' . $e->getMessage());
        }
    }

    public function deleteLogo()
    {
        try {
            $company = Company::findOrFail($this->company_id);
            $settings = $company->settings;
            $settings->company_logo = '';
            $company->settings = $settings;
            $company->save();

            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => 'Logo eliminado correctamente']);
            $this->emit('logoUpdated');

            return redirect()->route('admin.companies.profile', $this->company_id);

        } catch (\Exception $e) {
            Log::error("Error al eliminar logo: " . $e->getMessage());
            $this->addError('logo', 'Error al eliminar logo: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.upload-company-logo-modal');
    }
}
