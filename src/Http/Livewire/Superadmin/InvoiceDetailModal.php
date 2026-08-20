<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use App\Models\Invoice;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Utils\TypeDocumentSector;
use Livewire\Component;

class InvoiceDetailModal extends Component
{
    public $isOpen = false;
    public $activeTab = 'general'; // 'general', 'items', 'payments', 'client', 'technical'
    public $invoice_id = null;
    public $invoice = null;
    public $felInvoice = null;
    public $client = null;
    public $payments = [];
    public $items = [];
    public $errors = [];
    public $extras = [];

    protected $listeners = [
        'openInvoiceDetailModal' => 'openModal',
        'refreshInvoiceDetail' => 'reloadInvoice'
    ];

    public function openModal($invoice_id, $tab = 'general')
    {
        $this->invoice_id = $invoice_id;
        $this->activeTab = $tab;
        $this->loadInvoiceData();
        $this->isOpen = true;
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset([
            'invoice_id',
            'invoice',
            'felInvoice',
            'client',
            'payments',
            'items',
            'errors',
            'extras',
            'activeTab'
        ]);
    }

    public function reloadInvoice()
    {
        if ($this->invoice_id) {
            $this->loadInvoiceData();
        }
    }

    protected function loadInvoiceData()
    {
        $this->invoice = Invoice::with(['client.fel_client', 'payments', 'company'])->find($this->invoice_id);

        if (!$this->invoice) {
            return;
        }

        $this->felInvoice = FelInvoiceRequest::where('id_origin', $this->invoice_id)->first();

        // Load Client Info
        $this->client = $this->invoice->client;

        // Load Payments
        $this->payments = $this->invoice->payments ? $this->invoice->payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'number' => $payment->number,
                'amount' => $payment->pivot->amount ?? $payment->amount,
                'date' => $payment->date,
                'type_id' => $payment->type_id,
                'type_name' => $this->getPaymentTypeName($payment->type_id),
                'status_id' => $payment->status_id,
                'status_name' => $this->getPaymentStatusName($payment->status_id),
                'transaction_reference' => $payment->transaction_reference,
            ];
        })->toArray() : [];

        // Load Line Items
        if ($this->felInvoice && !empty($this->felInvoice->detalles)) {
            $rawDetalles = is_array($this->felInvoice->detalles) ? $this->felInvoice->detalles : json_decode($this->felInvoice->detalles, true);
            $this->items = is_array($rawDetalles) ? $rawDetalles : [];
        } elseif (!empty($this->invoice->line_items)) {
            $rawLineItems = is_array($this->invoice->line_items) ? $this->invoice->line_items : json_decode($this->invoice->line_items, true);
            $this->items = is_array($rawLineItems) ? $rawLineItems : [];
        } else {
            $this->items = [];
        }

        // Load Errors
        if ($this->felInvoice && !empty($this->felInvoice->errores)) {
            $rawErrors = is_array($this->felInvoice->errores) ? $this->felInvoice->errores : json_decode($this->felInvoice->errores, true);
            $this->errors = is_array($rawErrors) ? $rawErrors : [$rawErrors];
        } else {
            $this->errors = [];
        }

        // Load Extras
        if ($this->felInvoice && !empty($this->felInvoice->extras)) {
            $rawExtras = is_array($this->felInvoice->extras) ? $this->felInvoice->extras : json_decode($this->felInvoice->extras, true);
            $this->extras = is_array($rawExtras) ? $rawExtras : [];
        } else {
            $this->extras = [];
        }
    }

    public function reemitFromModal()
    {
        if (!$this->felInvoice) {
            session()->flash('detail_error', 'No se encontró el registro fiscal para reemitir.');
            return;
        }

        try {
            if ($this->felInvoice->numeroFactura == 0 && $this->invoice) {
                $this->felInvoice->setNumeroFactura($this->invoice->number);
                $this->felInvoice = $this->felInvoice->fresh();
            }

            $this->felInvoice->setAccessToken()->sendInvoiceToFel();
            $this->felInvoice = $this->felInvoice->fresh();

            if ($this->felInvoice->codigoEstado == 690) {
                session()->flash('detail_success', 'Factura emitida exitosamente con estado VÁLIDA.');
            } elseif ($this->felInvoice->codigoEstado == 902) {
                session()->flash('detail_error', 'Factura rechazada por el SIAT.');
            } else {
                session()->flash('detail_info', 'Factura enviada al SIAT. Estado: ' . ($this->felInvoice->estado ?? 'PENDIENTE'));
            }

            $this->loadInvoiceData();
            $this->emit('refreshInvoices');
        } catch (\Exception $e) {
            session()->flash('detail_error', 'Error al emitir factura: ' . $e->getMessage());
        }
    }

    public function regeneratePdfFromModal()
    {
        if (!$this->invoice) return;

        try {
            $this->invoice->service()->deletePdf();
            \App\Jobs\Entity\CreateEntityPdf::dispatch($this->invoice);
            session()->flash('detail_success', 'Generación de PDF solicitada para la factura.');
            $this->loadInvoiceData();
            $this->emit('refreshInvoices');
        } catch (\Exception $e) {
            session()->flash('detail_error', 'Error al regenerar PDF: ' . $e->getMessage());
        }
    }

    public function getSectorNameProperty()
    {
        if (!$this->felInvoice || empty($this->felInvoice->type_document_sector_id)) {
            return 'Factura Compra Venta';
        }
        return TypeDocumentSector::ARRAY_NAMES[$this->felInvoice->type_document_sector_id] ?? 'Sector #' . $this->felInvoice->type_document_sector_id;
    }

    protected function getPaymentTypeName($type_id)
    {
        $types = [
            1 => 'Tarjeta de Crédito',
            2 => 'Transferencia Bancaria',
            3 => 'PayPal',
            4 => 'Criptomoneda',
            5 => 'Cheque',
            6 => 'Efectivo',
            7 => 'Tarjeta de Débito',
            8 => 'QR Simple / BCP',
            9 => 'Otro',
        ];
        return $types[$type_id] ?? 'Método #' . $type_id;
    }

    protected function getPaymentStatusName($status_id)
    {
        $statuses = [
            1 => 'Pendiente',
            2 => 'Cancelado',
            3 => 'Fallido',
            4 => 'Completado',
            5 => 'Reembolso Parcial',
            6 => 'Reembolsado',
        ];
        return $statuses[$status_id] ?? 'Estado #' . $status_id;
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.invoice-detail-modal');
    }
}
