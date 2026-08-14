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

use App\Models\Invoice;
use Livewire\Component;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Collection;

class InvoicesAdminTable extends Component
{
    public $invoices;

    public $currentCursor = null;

    public $nextCursor;

    public $hasMorePages;

    public $onFirstPage;

    public int $per_page = 10;

    public $company_id = null;

    public $search = "";

    public $prefactura = "inactive";
    public $estado = "";

    public function updatedEstado()
    {
        $this->loadInvoices(null);
    }

    public function updatedSearch()
    {
        $this->loadInvoices(null);
    }
    public function updatedPerPage()
    {
        $this->loadInvoices(null);
    }
    public function updatedPrefactura()
    {
        $this->loadInvoices(null);
    }

    public function mount($company_id=null)
    {
        $this->company_id = $company_id;

        $this->sort_asc = false;

        $this->sort_field = 'date';

        $this->invoices = new Collection();

        $this->currentCursor = "";

        $this->loadInvoices(true);
    }

    public function changePage($next = true)
    {
        if ($next) {

            if ($this->hasMorePages !== null  && !$this->hasMorePages) {
                return;
            }
        } else {
            if ($this->onFirstPage) {
                return;
            }
        }
        $this->loadInvoices($next);
    }

    public function loadInvoices($next=null)
    {
        $cursor = null;

        if($next!==null) {
            $cursor = $next ? $this->nextCursor : $this->previousCursor;
        }

        $invoices = Invoice::query()
                            ->join('fel_invoice_requests', 'fel_invoice_requests.id_origin', 'invoices.id')
                            ->whereExists(function ($query) {
                                $query->from('fel_invoice_requests')
                                ->whereColumn('fel_invoice_requests.id_origin', 'invoices.id')
                                ->when($this->prefactura ,function($q){
                                    if($this->prefactura == "active"){
                                        $q->where('fel_invoice_requests.numeroFactura', '=', 0);
                                    }else {
                                        return $q->where('fel_invoice_requests.numeroFactura', '!=', 0);
                                    }
                                })
                                ->when($this->estado, function($q) {
                                    if ($this->estado == "904") {
                                        return $q->whereIn('fel_invoice_requests.codigoEstado', [904, 908]);
                                    }
                                    return $q->where('fel_invoice_requests.codigoEstado', $this->estado);
                                });
                            })
                            ->leftJoin('companies', 'companies.id', 'invoices.company_id')
                            ->when($this->company_id, function ($query) {
                                if (!empty($this->company_id)) {
                                    $query->where('invoices.company_id', $this->company_id);
                                }
                            });
        
        if ($this->search != "") {
            if(strlen($this->search) >= 49){
                $invoices = $invoices->where('fel_invoice_requests.cuf',$this->search);
            }else if(  preg_match('/[0-9]/',$this->search)) {
                $invoices = $invoices->where('fel_invoice_requests.numeroDocumento', $this->search);
            }else {
                info("es otro tipo de variable  " . gettype($this->search));
            }
            
        }
    
        $invoices = $invoices->orderBy('invoices.id','desc');
        $columns = ['invoices.id', 'invoices.status_id', 'invoices.pdf_url', 'fel_invoice_requests.codigoEstado','fel_invoice_requests.prefactura_number', 'fel_invoice_requests.fechaEmision', 'fel_invoice_requests.nombreRazonSocial', 'fel_invoice_requests.numeroDocumento', 'invoices.amount', 'fel_invoice_requests.numeroFactura', \DB::raw('json_extract(companies.settings,"$.name") as company_name'), 'companies.id as companyid'];
        if (!is_null($cursor)){
            $invoices = $invoices->cursorPaginate($this->per_page,$columns , 'cursor', Cursor::fromEncoded($cursor));
        } else {
            
            $invoices = $invoices->cursorPaginate($this->per_page, $columns, 'cursor');
        }
     
        $this->invoices = collect($invoices->items())->map(function($invoice) {
            return $invoice->getAttributes();
        })->toArray();
        if ($this->hasMorePages = $invoices->hasMorePages()) {
            $this->nextCursor = $invoices->nextCursor()->encode();
        }
        if (!($this->onFirstPage = $invoices->onFirstPage())) {
            $this->previousCursor = $invoices->previousCursor()->encode();
            $this->currentCursor = $invoices->cursor()->encode();
        }
    }

    public function viewPdf($invoice_id)
    {
        try {
            $invoice = Invoice::find($invoice_id);
            if ($invoice) {
                $file_path = $invoice->getPdfUrl();
                $url = !empty($file_path) ? \Storage::disk(config('filesystems.default'))->url($file_path) : '';
                $this->dispatchBrowserEvent('open-pdf', ['url' => $url, 'id' => $invoice_id]);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al obtener PDF: ' . $e->getMessage());
        }
    }

    public function regeneratePdf($invoice_id)
    {
        try {
            $invoice = Invoice::find($invoice_id);
            if ($invoice) {
                // Delete old PDF to ensure regeneration isn't cached
                $invoice->service()->deletePdf();
                
                // Dispatch Invoice Ninja standard job for PDF generation
                \App\Jobs\Entity\CreateEntityPdf::dispatch($invoice);
                session()->flash('message', 'Generación de PDF solicitada para la factura #' . $invoice->number);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al regenerar PDF: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.invoices-admin-table');
    }
}
