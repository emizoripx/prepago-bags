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
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use Livewire\Component;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Collection;

class InvoicesAdminTable extends Component
{
    public $invoices;

    public $currentCursor = null;

    public $nextCursor;

    public $previousCursor;

    public $hasMorePages;

    public $onFirstPage;

    public int $per_page = 10;

    public $company_id = null;

    public $search = "";

    public $prefactura = "inactive";
    public $estado = "";
    public $payment_status = "";
    public $type_document_sector = "";

    protected $listeners = [
        'refreshInvoices' => 'reloadInvoicesListener'
    ];

    public function reloadInvoicesListener()
    {
        $this->loadInvoices(null);
    }

    public function updatedEstado()
    {
        $this->loadInvoices(null);
    }

    public function updatedPaymentStatus()
    {
        $this->loadInvoices(null);
    }

    public function updatedTypeDocumentSector()
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

    public function mount($company_id = null)
    {
        $this->company_id = $company_id;

        $this->invoices = new Collection();

        $this->currentCursor = "";

        $this->loadInvoices(true);
    }

    public function changePage($next = true)
    {
        if ($next) {
            if ($this->hasMorePages !== null && !$this->hasMorePages) {
                return;
            }
        } else {
            if ($this->onFirstPage) {
                return;
            }
        }
        $this->loadInvoices($next);
    }

    public function loadInvoices($next = null)
    {
        $cursor = null;

        if ($next !== null) {
            $cursor = $next ? $this->nextCursor : $this->previousCursor;
        }

        $invoices = Invoice::withTrashed()
            ->join('fel_invoice_requests', 'fel_invoice_requests.id_origin', 'invoices.id')
            ->whereExists(function ($query) {
                $query->from('fel_invoice_requests')
                    ->whereColumn('fel_invoice_requests.id_origin', 'invoices.id')
                    ->when($this->prefactura, function ($q) {
                        if ($this->prefactura == "active") {
                            $q->where('fel_invoice_requests.numeroFactura', '=', 0);
                        } else {
                            return $q->where('fel_invoice_requests.numeroFactura', '!=', 0);
                        }
                    })
                    ->when($this->estado, function ($q) {
                        if ($this->estado == "904") {
                            return $q->whereIn('fel_invoice_requests.codigoEstado', [904, 908]);
                        }
                        if ($this->estado == "905" || $this->estado == "691") {
                            return $q->whereIn('fel_invoice_requests.codigoEstado', [905, 691]);
                        }
                        return $q->where('fel_invoice_requests.codigoEstado', $this->estado);
                    })
                    ->when($this->type_document_sector, function ($q) {
                        return $q->where('fel_invoice_requests.type_document_sector_id', $this->type_document_sector);
                    });
            })
            ->leftJoin('companies', 'companies.id', 'invoices.company_id')
            ->when($this->company_id, function ($query) {
                if (!empty($this->company_id)) {
                    $query->where('invoices.company_id', $this->company_id);
                }
            })
            ->when($this->payment_status, function ($query) {
                if ($this->payment_status === 'paid') {
                    $query->where('invoices.balance', '<=', 0);
                } elseif ($this->payment_status === 'unpaid') {
                    $query->where('invoices.balance', '>', 0)->whereColumn('invoices.balance', '=', 'invoices.amount');
                } elseif ($this->payment_status === 'partial') {
                    $query->where('invoices.balance', '>', 0)->whereColumn('invoices.balance', '<', 'invoices.amount');
                } elseif ($this->payment_status === 'pending_balance') {
                    $query->where('invoices.balance', '>', 0);
                }
            });

        if (!empty(trim($this->search))) {
            $searchTerm = trim($this->search);
            $invoices = $invoices->where(function ($q) use ($searchTerm) {
                if (strlen($searchTerm) >= 40) {
                    $q->where('fel_invoice_requests.cuf', 'like', "%{$searchTerm}%");
                } else {
                    $q->where('fel_invoice_requests.numeroDocumento', 'like', "%{$searchTerm}%")
                      ->orWhere('fel_invoice_requests.nombreRazonSocial', 'like', "%{$searchTerm}%")
                      ->orWhere('fel_invoice_requests.numeroFactura', 'like', "%{$searchTerm}%")
                      ->orWhere('invoices.number', 'like', "%{$searchTerm}%");
                }
            });
        }

        $invoices = $invoices->orderBy('invoices.id', 'desc');

        $columns = [
            'invoices.id',
            'invoices.number as invoice_number',
            'invoices.status_id',
            'invoices.pdf_url',
            'invoices.amount',
            'invoices.balance',
            'invoices.paid_to_date',
            'invoices.client_id',
            'fel_invoice_requests.id as fel_invoice_id',
            'fel_invoice_requests.codigoEstado',
            'fel_invoice_requests.estado',
            'fel_invoice_requests.prefactura_number',
            'fel_invoice_requests.fechaEmision',
            'fel_invoice_requests.nombreRazonSocial',
            'fel_invoice_requests.numeroDocumento',
            'fel_invoice_requests.codigoTipoDocumentoIdentidad',
            'fel_invoice_requests.numeroFactura',
            'fel_invoice_requests.type_document_sector_id',
            'fel_invoice_requests.cuf',
            'fel_invoice_requests.emission_type',
            'fel_invoice_requests.xml_url',
            'fel_invoice_requests.urlSin',
            'fel_invoice_requests.errores',
            \DB::raw('json_extract(companies.settings,"$.name") as company_name'),
            'companies.id as companyid'
        ];

        if (!is_null($cursor)) {
            $invoices = $invoices->cursorPaginate($this->per_page, $columns, 'cursor', Cursor::fromEncoded($cursor));
        } else {
            $invoices = $invoices->cursorPaginate($this->per_page, $columns, 'cursor');
        }

        $this->invoices = collect($invoices->items())->map(function ($invoice) {
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

    public function reemitInvoice($invoice_id)
    {
        try {
            $felInvoiceRequest = FelInvoiceRequest::where('id_origin', $invoice_id)->first();
            $invoice = Invoice::find($invoice_id);

            if (!$felInvoiceRequest) {
                session()->flash('error', 'No se encontró el registro fiscal asociado a la factura #' . $invoice_id);
                return;
            }

            if ($felInvoiceRequest->numeroFactura == 0 && $invoice) {
                $felInvoiceRequest->setNumeroFactura($invoice->number);
                $felInvoiceRequest = $felInvoiceRequest->fresh();
            }

            $felInvoiceRequest->setAccessToken()->sendInvoiceToFel();
            $felInvoiceRequest = $felInvoiceRequest->fresh();

            if ($felInvoiceRequest->codigoEstado == 690) {
                session()->flash('message', 'Factura #' . ($felInvoiceRequest->numeroFactura ?? $invoice_id) . ' emitida con éxito. Estado: VÁLIDA (CUF generado).');
            } elseif ($felInvoiceRequest->codigoEstado == 902) {
                $errorMsg = 'Factura #' . ($felInvoiceRequest->numeroFactura ?? $invoice_id) . ' fue RECHAZADA por el SIAT.';
                if (!empty($felInvoiceRequest->errores)) {
                    $errors = is_array($felInvoiceRequest->errores) ? $felInvoiceRequest->errores : json_decode($felInvoiceRequest->errores, true);
                    if (is_array($errors) && isset($errors[0]['description'])) {
                        $errorMsg .= ' ' . $errors[0]['description'];
                    }
                }
                session()->flash('error', $errorMsg);
            } else {
                session()->flash('message', 'Factura #' . ($felInvoiceRequest->numeroFactura ?? $invoice_id) . ' enviada al SIAT. Estado: ' . ($felInvoiceRequest->estado ?? 'PENDIENTE'));
            }

            $this->loadInvoices(null);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al reemitir factura #' . $invoice_id . ': ' . $e->getMessage());
        }
    }

    public function openDetail($invoice_id, $tab = 'general')
    {
        $this->emit('openInvoiceDetailModal', $invoice_id, $tab);
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
                session()->flash('message', 'Generación de PDF solicitada para la factura #' . ($invoice->number ?? $invoice_id));
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
