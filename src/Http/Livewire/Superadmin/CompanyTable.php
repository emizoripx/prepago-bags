<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2023. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use App\Models\Company;
use App\Utils\Traits\WithSorting;
use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyTable extends Component
{
    use WithPagination, WithSorting;

    public string $phase = "todos";
    public string $search = "";

    public int $per_page = 10;


    public function mount()
    {
        $this->sort_asc = false;

        $this->sort_field = 'companies.created_at';

        $this->getCompanies();
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPhase()
    {
        $this->resetPage();
    }


    public function getCompanies()
    {
        $query = AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
                    ->join('company_user', 'company_user.company_id', 'companies.id')
                    ->leftJoin('users', 'users.id', 'company_user.user_id')
                    ->where('company_user.is_owner', 1)
                    ->select('companies.id', \DB::raw('json_extract(companies.settings,"$.name") as company_name'), \DB::raw('json_extract(companies.settings,"$.id_number") as company_id_number'), 'fel_company.company_id' ,'companies.created_at' ,'fel_company.production', 'fel_company.is_postpago', 'fel_company.enabled','fel_company.phase',  'users.first_name', 'users.last_name', 'users.phone', 'users.email');

        if ($this->search != "") {
            $query = $query->where(function($q) {
                return  $q->whereRaw(\DB::raw('MATCH (companies.settings) AGAINST ("' . $this->search . '" IN NATURAL LANGUAGE MODE)'))
                            ->orWhere('users.email', 'like', '%' . $this->search . '%')
                            ->orWhere('users.first_name', 'like', '%' . $this->search . '%')
                            ->orWhere('users.last_name', 'like', '%' . $this->search . '%');
            } ); 
        }

        if($this->phase != "todos") {
            $query = $query->where('fel_company.phase',$this->phase);
        }

        $query = $query->orderBy($this->sort_field, $this->sort_asc ? 'asc' : 'desc');
        return $query->paginate($this->per_page);
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.company-table', ['companies' => $this->getCompanies()]);
    }
}
