<div x-data="{ copiedCuf: false }" 
     x-show="$wire.isOpen" 
     style="display: none;"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/75 backdrop-blur-sm p-4 overflow-y-auto"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="relative w-full max-w-5xl my-8 bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh] overflow-hidden" 
         @click.away="$wire.closeModal()">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50/80">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-sky-100 text-sky-600 rounded-xl">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xl font-bold text-slate-900">
                            @if($felInvoice && $felInvoice->numeroFactura != 0)
                                Factura #{{ $felInvoice->numeroFactura }}
                            @elseif($felInvoice && $felInvoice->prefactura_number)
                                Prefactura #{{ $felInvoice->prefactura_number }}
                            @elseif($invoice)
                                Factura #{{ $invoice->number }}
                            @else
                                Detalle de Factura
                            @endif
                        </h3>

                        @if($felInvoice)
                            @php
                                $statusBadgeClass = 'bg-slate-100 text-slate-700 ring-1 ring-slate-400/20';
                                $statusLabel = 'Desconocido';
                                if($felInvoice->codigoEstado == 690) {
                                    $statusBadgeClass = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20';
                                    $statusLabel = 'VÁLIDA';
                                } elseif($felInvoice->codigoEstado == 902) {
                                    $statusBadgeClass = 'bg-red-50 text-red-700 ring-1 ring-red-600/20';
                                    $statusLabel = 'RECHAZADA';
                                } elseif($felInvoice->codigoEstado == 905) {
                                    $statusBadgeClass = 'bg-slate-100 text-slate-700 ring-1 ring-slate-600/20';
                                    $statusLabel = 'ANULADA';
                                } elseif($felInvoice->codigoEstado == 904 || $felInvoice->codigoEstado == 908) {
                                    $statusBadgeClass = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20';
                                    $statusLabel = 'PENDIENTE';
                                }
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusBadgeClass }}">
                                {{ $statusLabel }}
                            </span>
                        @endif

                        @if($invoice)
                            @if($invoice->balance <= 0)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20">
                                    Pagada
                                </span>
                            @elseif($invoice->balance > 0 && $invoice->balance < $invoice->amount)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 ring-1 ring-amber-600/20">
                                    Parcial (Pend: {{ number_format($invoice->balance, 2) }})
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 ring-1 ring-rose-600/20">
                                    Pendiente: {{ number_format($invoice->balance, 2) }}
                                </span>
                            @endif
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        {{ $this->sectorName }} 
                        @if($felInvoice && $felInvoice->fechaEmision)
                            • Emisión: {{ \Carbon\Carbon::parse($felInvoice->fechaEmision)->format('d/m/Y H:i') }}
                        @endif
                    </p>
                </div>
            </div>

            <button type="button" wire:click="closeModal" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Flash messages -->
        <div class="px-6 pt-3">
            @if (session()->has('detail_success'))
                <div class="p-3 mb-2 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('detail_success') }}
                </div>
            @endif
            @if (session()->has('detail_error'))
                <div class="p-3 mb-2 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    {{ session('detail_error') }}
                </div>
            @endif
            @if (session()->has('detail_info'))
                <div class="p-3 mb-2 text-sm text-sky-800 rounded-lg bg-sky-50 border border-sky-200 flex items-center">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    {{ session('detail_info') }}
                </div>
            @endif
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-slate-200 px-6 bg-white overflow-x-auto space-x-6 text-sm font-medium">
            <button type="button" wire:click="switchTab('general')" 
                    class="py-3 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'general' ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                General y Fiscal
            </button>
            <button type="button" wire:click="switchTab('items')" 
                    class="py-3 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'items' ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Ítems / Detalle ({{ count($items) }})
            </button>
            <button type="button" wire:click="switchTab('payments')" 
                    class="py-3 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'payments' ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                Pagos y Balance ({{ count($payments) }})
            </button>
            <button type="button" wire:click="switchTab('client')" 
                    class="py-3 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'client' ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Cliente Receptor
            </button>
            <button type="button" wire:click="switchTab('technical')" 
                    class="py-3 border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap {{ $activeTab === 'technical' ? 'border-sky-600 text-sky-600 font-semibold' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                Datos Técnicos / XML
            </button>
        </div>

        <!-- Body Content -->
        <div class="p-6 overflow-y-auto flex-1 space-y-6">

            <!-- TAB 1: GENERAL & FISCAL -->
            @if($activeTab === 'general')
                <div class="space-y-6">
                    <!-- CUF Box -->
                    @if($felInvoice && $felInvoice->cuf)
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-semibold uppercase text-slate-400 tracking-wider">Código Único de Factura (CUF)</span>
                                <p class="text-sm font-mono font-medium text-slate-800 break-all select-all mt-1">
                                    {{ $felInvoice->cuf }}
                                </p>
                            </div>
                            <button type="button" 
                                    @click="navigator.clipboard.writeText('{{ $felInvoice->cuf }}'); copiedCuf = true; setTimeout(() => copiedCuf = false, 2500)"
                                    class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-medium rounded-lg shadow-sm transition-all flex-shrink-0">
                                <span x-show="!copiedCuf" class="inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    Copiar CUF
                                </span>
                                <span x-show="copiedCuf" style="display: none;" class="inline-flex items-center text-emerald-600 font-semibold">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    ¡Copiado!
                                </span>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- SIAT Errors Alert if any -->
                    @if(!empty($errors))
                    <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-bold text-red-800">Observaciones / Errores del SIAT</h4>
                                <ul class="mt-1.5 list-disc list-inside text-xs text-red-700 space-y-1">
                                    @foreach($errors as $err)
                                        @if(is_array($err))
                                            <li><strong>[{{ $err['code'] ?? 'Error' }}]</strong> {{ $err['description'] ?? json_encode($err) }}</li>
                                        @else
                                            <li>{{ $err }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Grid: Resumen Fiscal y Comercial -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Card 1: Datos de Emisión -->
                        <div class="p-5 bg-white border border-slate-200 rounded-xl space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Información de Emisión</h4>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Tipo de Documento:</span>
                                <span class="font-medium text-slate-800">{{ $this->sectorName }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Tipo de Emisión:</span>
                                <span class="font-medium text-slate-800">{{ $felInvoice->emission_type ?? 'En línea' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Fecha de Emisión:</span>
                                <span class="font-medium text-slate-800">{{ $felInvoice->fechaEmision ?? ($invoice->date ?? '-') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Número de Factura:</span>
                                <span class="font-bold text-slate-900">{{ $felInvoice && $felInvoice->numeroFactura != 0 ? $felInvoice->numeroFactura : 'Prefactura #' . ($felInvoice->prefactura_number ?? '-') }}</span>
                            </div>
                        </div>

                        <!-- Card 2: Resumen Financiero -->
                        <div class="p-5 bg-white border border-slate-200 rounded-xl space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Totales e Impuestos</h4>
                            
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Monto Total:</span>
                                <span class="font-bold text-base text-slate-900">Bs. {{ number_format($invoice->amount ?? ($felInvoice->montoTotal ?? 0), 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Monto Sujeto a IVA:</span>
                                <span class="font-medium text-slate-800">Bs. {{ number_format($felInvoice->montoTotalSujetoIva ?? ($invoice->amount ?? 0), 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500">Monto Pagado:</span>
                                <span class="font-medium text-emerald-600">Bs. {{ number_format($invoice->paid_to_date ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm pt-1 border-t border-slate-100">
                                <span class="text-slate-600 font-semibold">Balance Pendiente:</span>
                                <span class="font-bold text-base {{ ($invoice->balance ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    Bs. {{ number_format($invoice->balance ?? 0, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- TAB 2: ITEMS / DETALLE -->
            @if($activeTab === 'items')
                <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs">Código</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs">Descripción</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs text-right">Cantidad</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs text-right">Precio Unit.</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs text-right">Descuento</th>
                                <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($items as $item)
                                @php
                                    $codigo = $item['codigoProducto'] ?? $item['product_key'] ?? '-';
                                    $desc = $item['descripcion'] ?? $item['notes'] ?? '-';
                                    $qty = $item['cantidad'] ?? $item['quantity'] ?? 1;
                                    $cost = $item['precioUnitario'] ?? $item['cost'] ?? 0;
                                    $descMonto = $item['montoDescuento'] ?? $item['discount'] ?? 0;
                                    $subtotal = $item['subTotal'] ?? ($qty * $cost - $descMonto);
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-mono text-xs text-slate-600">{{ $codigo }}</td>
                                    <td class="px-4 py-3 text-slate-800 font-medium">{{ $desc }}</td>
                                    <td class="px-4 py-3 text-slate-600 text-right">{{ number_format($qty, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-600 text-right">{{ number_format($cost, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-600 text-right">{{ number_format($descMonto, 2) }}</td>
                                    <td class="px-4 py-3 text-slate-900 font-bold text-right">{{ number_format($subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-sm">
                                        No hay ítems registrados en el detalle de la factura.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(!empty($items))
                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right font-bold text-slate-700">Total Facturado:</td>
                                <td class="px-4 py-3 text-right font-bold text-base text-slate-900">
                                    Bs. {{ number_format($invoice->amount ?? ($felInvoice->montoTotal ?? 0), 2) }}
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            @endif

            <!-- TAB 3: PAGOS & BALANCE -->
            @if($activeTab === 'payments')
                <div class="space-y-6">
                    <!-- Balance Summary Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl text-center">
                            <span class="text-xs uppercase font-bold text-slate-400">Monto Factura</span>
                            <p class="text-xl font-extrabold text-slate-900 mt-1">Bs. {{ number_format($invoice->amount ?? 0, 2) }}</p>
                        </div>
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center">
                            <span class="text-xs uppercase font-bold text-emerald-600">Total Pagado</span>
                            <p class="text-xl font-extrabold text-emerald-700 mt-1">Bs. {{ number_format($invoice->paid_to_date ?? 0, 2) }}</p>
                        </div>
                        <div class="p-4 {{ ($invoice->balance ?? 0) > 0 ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-slate-50 border-slate-200 text-slate-700' }} border rounded-xl text-center">
                            <span class="text-xs uppercase font-bold {{ ($invoice->balance ?? 0) > 0 ? 'text-rose-600' : 'text-slate-400' }}">Saldo Pendiente</span>
                            <p class="text-xl font-extrabold mt-1">Bs. {{ number_format($invoice->balance ?? 0, 2) }}</p>
                        </div>
                    </div>

                    <!-- Payments Table -->
                    <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                            <h4 class="text-xs font-bold uppercase text-slate-700">Historial de Pagos</h4>
                            <span class="text-xs font-medium text-slate-500">{{ count($payments) }} pagos registrados</span>
                        </div>
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                            <thead class="bg-white">
                                <tr>
                                    <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs"># Pago</th>
                                    <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs">Fecha</th>
                                    <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs">Método</th>
                                    <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs">Referencia</th>
                                    <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs">Estado</th>
                                    <th class="px-4 py-3 font-semibold text-slate-700 uppercase text-xs text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($payments as $p)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-medium text-slate-800">#{{ $p['number'] ?? $p['id'] }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $p['date'] ?? '-' }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $p['type_name'] }}</td>
                                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $p['transaction_reference'] ?: '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $p['status_id'] == 4 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                                {{ $p['status_name'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 font-bold text-slate-900 text-right">Bs. {{ number_format($p['amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-slate-400 text-sm">
                                            No se han registrado pagos para esta factura.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- TAB 4: CLIENTE RECEPTOR -->
            @if($activeTab === 'client')
                <div class="p-6 bg-white border border-slate-200 rounded-xl space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h4 class="text-lg font-bold text-slate-900">
                                {{ $felInvoice->nombreRazonSocial ?? ($client->name ?? 'Cliente General') }}
                            </h4>
                            <p class="text-xs text-slate-500 mt-0.5">
                                NIT / Documento: <strong>{{ $felInvoice->numeroDocumento ?? ($client->id_number ?? '-') }}</strong>
                                @if($felInvoice && $felInvoice->complemento)
                                    - {{ $felInvoice->complemento }}
                                @endif
                            </p>
                        </div>
                        @if($client)
                            <span class="inline-flex items-center px-3 py-1 bg-sky-50 text-sky-700 rounded-full text-xs font-semibold">
                                Cliente #{{ $client->id }}
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Razón Social Fiscal:</span>
                                <span class="font-medium text-slate-800">{{ $felInvoice->nombreRazonSocial ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">N° Documento Identidad:</span>
                                <span class="font-medium text-slate-800">{{ $felInvoice->numeroDocumento ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Código de Cliente:</span>
                                <span class="font-medium text-slate-800">{{ $felInvoice->codigoCliente ?? ($client->number ?? '-') }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Correo Electrónico:</span>
                                <span class="font-medium text-slate-800">{{ $felInvoice->emailCliente ?? ($client->contacts[0]->email ?? '-') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Teléfono:</span>
                                <span class="font-medium text-slate-800">{{ $felInvoice->telefonoCliente ?? ($client->phone ?? '-') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Dirección:</span>
                                <span class="font-medium text-slate-800">{{ $client->address1 ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- TAB 5: TÉCNICO & XML -->
            @if($activeTab === 'technical')
                <div class="space-y-6">
                    <div class="p-5 bg-white border border-slate-200 rounded-xl space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Enlaces y Recursos Fiscales</h4>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-slate-50 rounded-lg">
                            <div>
                                <span class="text-xs font-semibold text-slate-700">Verificación SIN (Servicio de Impuestos Nacionales)</span>
                                <p class="text-xs text-slate-500 mt-0.5">Enlace oficial de consulta con código QR / URL SIN.</p>
                            </div>
                            @if($felInvoice && $felInvoice->urlSin)
                                <a href="{{ $felInvoice->urlSin }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-medium transition-colors shadow-sm flex-shrink-0">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    Abrir en SIN
                                </a>
                            @else
                                <span class="text-xs text-slate-400 italic">No disponible</span>
                            @endif
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-slate-50 rounded-lg">
                            <div>
                                <span class="text-xs font-semibold text-slate-700">Archivo XML Fiscal Firmado</span>
                                <p class="text-xs text-slate-500 mt-0.5">Documento XML generado con firma digital SIAT.</p>
                            </div>
                            @if($felInvoice && $felInvoice->xml_url)
                                <a href="{{ $felInvoice->xml_url }}" target="_blank" download class="inline-flex items-center px-3 py-1.5 bg-slate-700 hover:bg-slate-800 text-white rounded-lg text-xs font-medium transition-colors shadow-sm flex-shrink-0">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Descargar XML
                                </a>
                            @else
                                <span class="text-xs text-slate-400 italic">No disponible</span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($extras))
                    <div class="p-5 bg-white border border-slate-200 rounded-xl space-y-2">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Parámetros Extras / Metadata</h4>
                        <div class="bg-slate-900 text-slate-200 font-mono text-xs p-4 rounded-lg overflow-x-auto">
                            <pre>{{ json_encode($extras, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- Footer Actions -->
        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                @if($felInvoice && $felInvoice->codigoEstado != 690)
                    <button type="button" wire:click="reemitFromModal" wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                        <svg class="w-4 h-4 mr-1.5" wire:loading.class="animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reemitir Factura a SIAT
                    </button>
                @endif

                <button type="button" wire:click="regeneratePdfFromModal" wire:loading.attr="disabled"
                        class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Regenerar PDF
                </button>
            </div>

            <button type="button" wire:click="closeModal" class="px-5 py-2 bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-xl shadow-sm transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>
