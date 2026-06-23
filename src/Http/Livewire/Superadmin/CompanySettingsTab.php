<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use EmizorIpx\PrepagoBags\Models\AccountPrepagoBags;
use Livewire\Component;
use DB;

class CompanySettingsTab extends Component
{
    public $company_id;
    public $level_invoice_number_generation;
    public $invoice_generators = [];

    protected $rules = [
        'level_invoice_number_generation' => 'required|integer',
        'invoice_generators.*' => 'required|integer'
    ];

    public function mount($company_id)
    {
        $this->company_id = $company_id;
        $company = AccountPrepagoBags::where('company_id', $company_id)->first();
        $this->level_invoice_number_generation = $company->level_invoice_number_generation ?? 0;

        $generators = DB::table('invoice_generator_number')->whereCompanyId($company_id)->orderBy('code')->get();
        foreach ($generators as $gen) {
            $this->invoice_generators[$gen->code] = $gen->number_counter;
        }
    }

    public function saveSettings()
    {
        $this->validate();

        $company = AccountPrepagoBags::where('company_id', $this->company_id)->first();
        $company->level_invoice_number_generation = $this->level_invoice_number_generation;
        $company->save();

        switch ($this->level_invoice_number_generation) {
            case 1:
                $codes = DB::table('fel_branches')->whereCompanyId($this->company_id)->pluck("codigo");
                break;
            case 2:
                $codes = DB::table('fel_branches')->selectRaw(DB::raw('concat(ifnull(fel_branches.codigo,0),"-",ifnull(tmp.codigo,0)) as codigo'))
                ->leftJoin(DB::raw(' (select 0  as codigo, ' . $this->company_id . ' as company_id union select codigo, company_id from fel_pos where company_id = ' . $this->company_id . ') as tmp'), 'tmp.company_id', 'fel_branches.company_id')
                ->where('fel_branches.company_id', $this->company_id)
                ->pluck("codigo");
                break;
            case 3:
                $codes = DB::table('fel_branches')
                ->selectRaw(DB::raw('concat(ifnull(fel_branches.codigo,2),"-",ifnull(tmp.codigo,0), "-", ifnull(fel_sector_document_types.codigo,2)) as codigo '))
                ->leftJoin(DB::raw(' (select 0  as codigo, '.$this->company_id.' as company_id union select codigo, company_id from fel_pos where company_id = '.$this->company_id.') as tmp'), 'tmp.company_id', 'fel_branches.company_id')
                ->leftJoin('fel_sector_document_types', 'fel_sector_document_types.codigoSucursal', 'fel_branches.codigo')
                ->where('fel_branches.company_id', $this->company_id)
                ->where('fel_sector_document_types.company_id', $this->company_id)
                ->pluck("codigo");
                break;
            default:
                $codes = [];
                break;
        }

        if (!empty($codes)) {
            $existence_codes = DB::table('invoice_generator_number')->whereCompanyId($this->company_id)->whereIn('code', $codes)->pluck('code')->toArray();
            $create_new_codes = [];
            foreach ($codes as $code ) {
                if(!in_array($code, $existence_codes)) {
                    $create_new_codes[] = [
                        "company_id" => $this->company_id,
                        "code" => $code,
                        "number_counter" => 1
                    ];
                }
            }
            if(!empty($create_new_codes)) {
                DB::table('invoice_generator_number')->insert($create_new_codes);
            }
        }

        if (!empty($this->invoice_generators)) {
            foreach ($this->invoice_generators as $key => $value) {
                DB::table('invoice_generator_number')->whereCode($key)->whereCompanyId($this->company_id)->update(['number_counter' => $value]);
            }
        }

        // Re-fetch to update view if new codes were created
        $generators = DB::table('invoice_generator_number')->whereCompanyId($this->company_id)->orderBy('code')->get();
        foreach ($generators as $gen) {
            $this->invoice_generators[$gen->code] = $gen->number_counter;
        }

        session()->flash('message', 'Configuraciones actualizadas exitosamente.');
    }

    public function render()
    {
        $sector_documents = DB::table('fel_sector_document_types')->selectRaw('distinct codigo, documentoSector')->whereCompanyId($this->company_id)->pluck('documentoSector','codigo')->toArray();
        return view('prepagobags::components.superadmin.company-settings-tab', compact('sector_documents'));
    }
}
