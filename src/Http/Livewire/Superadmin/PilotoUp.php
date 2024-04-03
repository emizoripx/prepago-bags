<?php

namespace App\Http\Livewire\Superadmin;

use App\Models\Company;
use LivewireUI\Modal\ModalComponent;

class PilotoUp extends ModalComponent
{
    public $company_id = null;

    public function mount($company_id)
    {
        info("ingresando ..  a piloto up ");
        $this->company_id = $company_id;
        $this->getCompany();
    }
    public function getCompany()
    {
        $this->company = Company::find($this->company_id);
    }

    public function render()
    {
        return view('prepagobags:components.superadmin.piloto-up');
    }
}