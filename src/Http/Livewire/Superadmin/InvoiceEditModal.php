<?php

namespace EmizorIpx\PrepagoBags\Http\Livewire\Superadmin;

use App\Models\Invoice;
use Livewire\Component;

class InvoiceEditModal extends Component
{
    public $isOpen = false;
    public $invoice_id;
    public $invoice;
    
    // Fields
    public $public_notes;
    public $private_notes;
    public $terms;
    
    // JSON Extras
    public $extras = [];
    public $new_extra_key = '';
    public $new_extra_value = '';

    protected $listeners = ['openEditInvoiceModal' => 'openModal'];

    public function openModal($id)
    {
        $this->invoice_id = $id;
        $this->invoice = Invoice::with('fel_invoice')->find($id);
        
        if ($this->invoice) {
            $this->public_notes = $this->invoice->public_notes;
            $this->private_notes = $this->invoice->private_notes;
            $this->terms = $this->invoice->terms;
            
            $this->extras = [];
            if ($this->invoice->fel_invoice && $this->invoice->fel_invoice->extras) {
                $raw_extras = json_decode($this->invoice->fel_invoice->extras, true);
                if (is_array($raw_extras)) {
                    foreach ($raw_extras as $k => $v) {
                        $this->extras[] = ['key' => $k, 'value' => $v];
                    }
                }
            }
            
            $this->isOpen = true;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->reset(['invoice_id', 'invoice', 'public_notes', 'private_notes', 'terms', 'extras', 'new_extra_key', 'new_extra_value']);
    }

    public function addExtra()
    {
        if (!empty($this->new_extra_key)) {
            $this->extras[] = ['key' => $this->new_extra_key, 'value' => $this->new_extra_value];
            $this->new_extra_key = '';
            $this->new_extra_value = '';
        }
    }

    public function removeExtra($index)
    {
        unset($this->extras[$index]);
        $this->extras = array_values($this->extras);
    }

    public function save()
    {
        if (!$this->invoice) return;

        // Update Invoice standard fields
        $this->invoice->public_notes = $this->public_notes;
        $this->invoice->private_notes = $this->private_notes;
        $this->invoice->terms = $this->terms;
        $this->invoice->save();

        // Update FEL Invoice Extras
        if ($this->invoice->fel_invoice) {
            $extras_obj = new \stdClass();
            foreach ($this->extras as $extra) {
                if (!empty($extra['key'])) {
                    $extras_obj->{$extra['key']} = $extra['value'];
                }
            }
            
            $this->invoice->fel_invoice->extras = json_encode($extras_obj);
            $this->invoice->fel_invoice->save();
        }

        session()->flash('message', 'Factura actualizada exitosamente.');
        $this->closeModal();
        
        // Refresh the parent table
        $this->emit('refreshInvoices');
    }

    public function render()
    {
        return view('prepagobags::components.superadmin.invoice-edit-modal');
    }
}
