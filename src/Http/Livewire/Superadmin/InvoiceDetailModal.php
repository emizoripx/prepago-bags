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
    public $emittedByUser = null;
    public $revocatedByUser = null;
    public $assignedUserName = null;

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
            'emittedByUser',
            'revocatedByUser',
            'assignedUserName',
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
        $this->invoice = Invoice::with(['client.contacts', 'client.fel_client', 'payments.user', 'company', 'user'])->find($this->invoice_id);

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
                'created_at' => $this->formatDateSafe($payment->created_at, 'd/m/Y H:i', null),
                'user_name' => $payment->user ? ($payment->user->first_name . ' ' . $payment->user->last_name) : null,
                'user_email' => $payment->user ? $payment->user->email : null,
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
            $rawErrors = is_array($this->felInvoice->errores) 
                ? $this->felInvoice->errores 
                : (is_string($this->felInvoice->errores) ? json_decode($this->felInvoice->errores, true) : null);
            
            if (is_array($rawErrors)) {
                $this->errors = array_values(array_filter($rawErrors));
            } elseif (!empty($rawErrors)) {
                $this->errors = [$rawErrors];
            } else {
                $this->errors = [];
            }
        } else {
            $this->errors = [];
        }

        // Load Extras & Sector Specific Attributes from fel_invoice_requests
        $extrasCollected = [];
        if ($this->felInvoice) {
            // 1. Unpack JSON and Array fields
            $jsonFields = [
                'extras',
                'otrosDatos',
                'costosGastosNacionales',
                'costosGastosInternacionales',
                'totalGastosNacionalesFob',
                'totalGastosInternacionales',
                'data_specific_by_sector',
                'external_invoice_data',
            ];

            foreach ($jsonFields as $field) {
                if (!empty($this->felInvoice->{$field})) {
                    $raw = is_array($this->felInvoice->{$field}) ? $this->felInvoice->{$field} : json_decode($this->felInvoice->{$field}, true);
                    if (is_array($raw)) {
                        foreach ($raw as $k => $v) {
                            if ($v !== null && $v !== '') {
                                if (is_array($v) && !isset($v[0])) {
                                    foreach ($v as $subK => $subV) {
                                        if ($subV !== null && $subV !== '') {
                                            $extrasCollected[$k . ' (' . $subK . ')'] = $subV;
                                        }
                                    }
                                } else {
                                    $extrasCollected[$k] = $v;
                                }
                            }
                        }
                    }
                }
            }

            // 2. All model attributes on fel_invoice_requests not in the base excluded list
            $baseAttributes = [
                'id', 'id_origin', 'company_id', 'created_at', 'updated_at', 'deleted_at',
                'detalles', 'errores', 'cuf', 'xml_url', 'urlSin', 'numeroFactura', 'prefactura_number',
                'codigoEstado', 'estado', 'fechaEmision', 'montoTotal', 'montoTotalSujetoIva', 'montoTotalMoneda',
                'codigoMoneda', 'tipoCambio', 'codigoMetodoPago', 'codigoLeyenda', 'codigoPuntoVenta',
                'codigoSucursal', 'codigoTipoDocumentoIdentidad', 'numeroDocumento', 'complemento',
                'nombreRazonSocial', 'codigoCliente', 'emailCliente', 'telefonoCliente', 'usuario',
                'emitted_by', 'revocated_by', 'emission_type', 'typeDocument', 'document_number',
                'factura_original_id', 'recurring_id_origin', 'traslado_id', 'sector_transporte',
                'transporte_id', 'type_document_sector_id', 'extras', 'otrosDatos', 'costosGastosNacionales',
                'costosGastosInternacionales', 'totalGastosNacionalesFob', 'totalGastosInternacionales',
                'data_specific_by_sector', 'external_invoice_data'
            ];

            foreach ($this->felInvoice->getAttributes() as $attrKey => $attrVal) {
                if (!in_array($attrKey, $baseAttributes) && $attrVal !== null && $attrVal !== '') {
                    $extrasCollected[$attrKey] = $attrVal;
                }
            }
        }
        $this->extras = $extrasCollected;

        // Resolve emitted_by
        if ($this->felInvoice && !empty($this->felInvoice->emitted_by)) {
            if (is_numeric($this->felInvoice->emitted_by)) {
                $u = \App\Models\User::find($this->felInvoice->emitted_by);
                $this->emittedByUser = $u ? trim($u->first_name . ' ' . $u->last_name) . ' (' . $u->email . ')' : 'Usuario #' . $this->felInvoice->emitted_by;
            } else {
                $this->emittedByUser = $this->felInvoice->emitted_by;
            }
        } else {
            $this->emittedByUser = null;
        }

        // Resolve revocated_by
        if ($this->felInvoice && !empty($this->felInvoice->revocated_by)) {
            if (is_numeric($this->felInvoice->revocated_by)) {
                $u = \App\Models\User::find($this->felInvoice->revocated_by);
                $this->revocatedByUser = $u ? trim($u->first_name . ' ' . $u->last_name) . ' (' . $u->email . ')' : 'Usuario #' . $this->felInvoice->revocated_by;
            } else {
                $this->revocatedByUser = $this->felInvoice->revocated_by;
            }
        } else {
            $this->revocatedByUser = null;
        }

        // Resolve assigned_user for client
        if ($this->client && !empty($this->client->assigned_user_id)) {
            $u = \App\Models\User::find($this->client->assigned_user_id);
            $this->assignedUserName = $u ? trim($u->first_name . ' ' . $u->last_name) . ' (' . $u->email . ')' : 'Usuario #' . $this->client->assigned_user_id;
        } else {
            $this->assignedUserName = null;
        }
    }

    public function formatDateSafe($date, $format = 'd/m/Y H:i', $default = '-')
    {
        if (empty($date)) {
            return $default;
        }
        try {
            if ($date instanceof \Carbon\Carbon || $date instanceof \DateTimeInterface) {
                return $date->format($format);
            }
            if (is_numeric($date)) {
                return \Carbon\Carbon::createFromTimestamp($date)->format($format);
            }
            return \Carbon\Carbon::parse($date)->format($format);
        } catch (\Throwable $e) {
            return (string) $date;
        }
    }

    public function getPaymentMethodDescription($code)
    {
        $methods = [
            1 => 'Efectivo',
            2 => 'Tarjeta (Débito/Crédito)',
            3 => 'Cheque',
            4 => 'Vales',
            5 => 'Otros',
            6 => 'Pago Posterior (A Crédito)',
            7 => 'Transferencia Bancaria',
            8 => 'Depósito en Cuenta',
            9 => 'Transferencia Swift',
            10 => 'Billetera Móvil',
            11 => 'Pago Digital / QR',
            27 => 'Tarjeta Prepago',
            31 => 'Canal Electrónico',
        ];
        return $methods[$code] ?? ($code ? "Código $code" : 'Efectivo');
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
