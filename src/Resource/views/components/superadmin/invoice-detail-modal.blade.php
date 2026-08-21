@php
    $rawSettings = $invoice->company->settings ?? null;
    $companySettings = is_string($rawSettings) ? json_decode($rawSettings) : (is_object($rawSettings) ? $rawSettings : null);
    $companyName = $companySettings->name ?? ($invoice->company->name ?? 'Emisor');
    $companyNit = $companySettings->id_number ?? ($felInvoice->nitEmisor ?? '-');

    $identityTypes = [
        1 => 'CI',
        2 => 'CEX',
        3 => 'PAS',
        4 => 'OD',
        5 => 'NIT'
    ];
    $docTypeCode = $felInvoice->codigoTipoDocumentoIdentidad ?? 5;
    $docTypeLabel = $identityTypes[$docTypeCode] ?? 'NIT';

    $amountVal = (float)($invoice->amount ?? ($felInvoice->montoTotal ?? 0));
    $balanceVal = (float)($invoice->balance ?? 0);
    $paidVal = (float)($invoice->paid_to_date ?? 0);
    $hasValidErrors = !empty($errors) && is_array($errors) && count(array_filter($errors)) > 0;

    $creatorName = $invoice && $invoice->user ? trim(($invoice->user->first_name ?? '') . ' ' . ($invoice->user->last_name ?? '')) : null;
    $creatorEmail = $invoice && $invoice->user ? $invoice->user->email : null;

    $statusBadgeClass = 'bg-slate-100 text-slate-700 ring-1 ring-slate-400/20';
    $statusLabel = 'Desconocido';
    if ($felInvoice) {
        if ($felInvoice->codigoEstado == 690) {
            $statusBadgeClass = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20';
            $statusLabel = 'VÁLIDA';
        } elseif ($felInvoice->codigoEstado == 902) {
            $statusBadgeClass = 'bg-red-50 text-red-700 ring-1 ring-red-600/20';
            $statusLabel = 'RECHAZADA';
        } elseif ($felInvoice->codigoEstado == 905 || $felInvoice->codigoEstado == 691) {
            $statusBadgeClass = 'bg-slate-100 text-slate-700 ring-1 ring-slate-600/20';
            $statusLabel = 'ANULADA';
        } elseif ($felInvoice->codigoEstado == 904 || $felInvoice->codigoEstado == 908) {
            $statusBadgeClass = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20';
            $statusLabel = 'PENDIENTE';
        }
    }
@endphp

<div x-data="{ copiedCuf: false }" 
     x-show="$wire.isOpen" 
     style="display: none;"
     class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-3 sm:p-4 overflow-hidden"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="relative w-full max-w-6xl bg-slate-100 rounded-2xl shadow-2xl border border-slate-300" 
         style="height: 90vh; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden;"
         @click.away="$wire.closeModal()">
        
        <!-- Header Bar (Backoffice Compact Toolbar - Static / Pinned) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between px-6 py-3.5 border-b border-slate-200 bg-white gap-3 shadow-sm"
             style="flex-shrink: 0; z-index: 10;">
            <div class="flex items-center gap-3.5">
                <div class="p-2.5 bg-sky-100 text-sky-700 rounded-xl flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2.5 flex-wrap">
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

                        <!-- SIAT Status Badge -->
                        @if($felInvoice)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $statusBadgeClass }}">
                                SIAT: {{ $statusLabel }}
                            </span>
                        @endif

                        <!-- Payment Status Badge -->
                        @if($invoice)
                            @if($balanceVal <= 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Pagado
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    Por cobrar (Bs. {{ number_format($balanceVal, 2) }})
                                </span>
                            @endif
                        @endif

                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-medium border border-slate-200">
                            {{ $this->sectorName }}
                        </span>
                    </div>

                    <p class="text-sm text-slate-500 mt-1">
                        <span class="font-semibold text-slate-800">{{ $companyName }}</span>
                        @if($felInvoice && $felInvoice->fechaEmision)
                            <span class="mx-1.5">•</span>
                            <span>Emisión SIAT: {{ $this->formatDateSafe($felInvoice->fechaEmision, 'd/m/Y H:i') }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Top Actions & Close Button -->
            <div class="flex items-center gap-2.5 flex-wrap">
                @if($felInvoice && $felInvoice->urlSin)
                    <a href="{{ $felInvoice->urlSin }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-xl text-xs font-bold transition-colors shadow-sm gap-1.5">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        Portal SIN
                    </a>
                @endif

                @if($felInvoice && $felInvoice->xml_url)
                    <a href="{{ $felInvoice->xml_url }}" target="_blank" download class="inline-flex items-center px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 rounded-xl text-xs font-bold transition-colors shadow-sm gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        XML
                    </a>
                @endif

                <button type="button" wire:click="closeModal" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>

        <!-- Flash Messages Container -->
        <div style="flex-shrink: 0;">
            @if(session()->has('detail_error'))
                <div class="mx-6 mt-4 p-3.5 bg-red-100 border border-red-300 text-red-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2.5 flex-shrink-0 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                    {{ session('detail_error') }}
                </div>
            @endif

            @if(session()->has('detail_success'))
                <div class="mx-6 mt-4 p-3.5 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2.5 flex-shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM13.707 9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    {{ session('detail_success') }}
                </div>
            @endif

            @if(session()->has('detail_info'))
                <div class="mx-6 mt-4 p-3.5 bg-sky-100 border border-sky-300 text-sky-800 rounded-xl text-sm font-semibold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2.5 flex-shrink-0 text-sky-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    {{ session('detail_info') }}
                </div>
            @endif
        </div>

        <!-- Main Body: Scrollable Content Container -->
        <div class="p-6 space-y-6"
             style="flex: 1 1 0%; min-height: 0; max-height: 100%; overflow-y: auto; overflow-x: hidden;">

            <!-- GRID PRINCIPAL (Dividido en 5 partes verticales: 4 partes a la izquierda y la 5ta parte para campos adicionales) -->
            <div style="display: grid; grid-template-columns: minmax(0, 4fr) minmax(240px, 1fr); gap: 20px; align-items: start;">

                <!-- COLUMNA IZQUIERDA (4 de 5 partes): Línea de Tiempo, Cliente Receptor, Datos Fiscales SIAT, Ítems Facturados, Totales y Notas -->
                <div class="space-y-5" style="min-width: 0;">

                    <!-- LÍNEA DE TIEMPO HORIZONTAL (Solo en las 4 primeras partes) -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-900">
                                        Línea de Tiempo y Trazabilidad
                                    </h4>
                                    <p class="text-xs text-slate-500">Flujo cronológico de creación, emisión fiscal y pagos</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-slate-600 bg-slate-100 border border-slate-200 px-3 py-1 rounded-full">
                                {{ 2 + count($payments) + ($felInvoice && ($felInvoice->codigoEstado == 905 || $felInvoice->codigoEstado == 691) ? 1 : 0) }} hitos
                            </span>
                        </div>

                        <div class="overflow-x-auto pb-2">
                            <div class="flex items-center min-w-max gap-4 justify-between px-2">

                                <!-- Hito 1: Creación en Sistema (Origen) -->
                                <div class="flex flex-col items-center text-center max-w-[210px] flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full border-2 border-slate-800 bg-slate-50 flex items-center justify-center shadow-sm text-slate-800 mb-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div class="px-2.5 py-0.5 rounded-full border border-slate-800 bg-white font-mono text-[11px] font-bold text-slate-900 mb-1 shadow-xs">
                                        {{ $this->formatDateSafe($invoice->created_at ?? ($invoice->date ?? null), 'd/m/Y H:i', ($invoice->date ?? '-')) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-900">Creación Sistema</span>
                                    <span class="text-[11px] text-slate-500 truncate max-w-[190px]" title="{{ $creatorName ?: ($creatorEmail ?: 'Sistema') }}">
                                        Por: <strong class="text-slate-700">{{ $creatorName ?: ($creatorEmail ?: 'Sistema') }}</strong>
                                    </span>
                                </div>

                                <!-- Conector -->
                                <div class="flex-1 h-0.5 min-w-[32px] max-w-[70px] bg-slate-300"></div>

                                <!-- Hito 2: Emisión Fiscal SIAT -->
                                <div class="flex flex-col items-center text-center max-w-[230px] flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full border-2 border-slate-800 bg-white flex items-center justify-center shadow-sm mb-2">
                                        @if($felInvoice && $felInvoice->codigoEstado == 690)
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @elseif($felInvoice && $felInvoice->codigoEstado == 902)
                                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        @else
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="px-2.5 py-0.5 rounded-full border border-slate-800 bg-white font-mono text-[11px] font-bold text-slate-900 mb-1 shadow-xs">
                                        {{ $this->formatDateSafe($felInvoice->fechaEmision ?? null, 'd/m/Y H:i', 'SIAT') }}
                                    </div>
                                    <div class="flex items-center gap-1.5 justify-center">
                                        <span class="text-xs font-bold text-slate-900">Emisión SIAT</span>
                                        <span class="px-1.5 py-0.2 rounded text-[10px] font-bold {{ $statusBadgeClass }}">{{ $statusLabel }}</span>
                                    </div>
                                    <span class="text-[11px] text-slate-500 truncate max-w-[210px]" title="{{ $emittedByUser ?: ($creatorName ?: 'Sistema') }}">
                                        Emitido: <strong class="text-slate-700">{{ $emittedByUser ?: ($creatorName ?: 'Sistema') }}</strong>
                                    </span>
                                </div>

                                <!-- Hitos de Pagos (Si existen) -->
                                @foreach($payments as $p)
                                    <div class="flex-1 h-0.5 min-w-[32px] max-w-[70px] bg-emerald-400"></div>

                                    <div class="flex flex-col items-center text-center max-w-[210px] flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full border-2 border-emerald-600 bg-emerald-50 flex items-center justify-center shadow-sm text-emerald-600 mb-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div class="px-2.5 py-0.5 rounded-full border border-emerald-600 bg-white font-mono text-[11px] font-bold text-emerald-700 mb-1 shadow-xs">
                                            {{ $this->formatDateSafe($p['created_at'] ?? ($p['date'] ?? null), 'd/m/Y H:i', ($p['date'] ?? '-')) }}
                                        </div>
                                        <span class="text-xs font-bold text-slate-900">Pago: <strong class="font-mono text-emerald-700">Bs. {{ number_format($p['amount'] ?? 0, 2) }}</strong></span>
                                        <span class="text-[11px] text-slate-500 truncate max-w-[190px]">
                                            {{ $p['type_name'] ?? 'Pago' }}
                                            @if(!empty($p['transaction_reference']))
                                                • Ref: <strong class="font-mono">{{ $p['transaction_reference'] }}</strong>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach

                                <!-- Hito de Anulación (Si aplica) -->
                                @if($felInvoice && ($felInvoice->codigoEstado == 905 || $felInvoice->codigoEstado == 691))
                                    <div class="flex-1 h-0.5 min-w-[32px] max-w-[70px] bg-rose-400"></div>

                                    <div class="flex flex-col items-center text-center max-w-[220px] flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full border-2 border-rose-600 bg-rose-50 flex items-center justify-center shadow-sm text-rose-600 mb-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </div>
                                        <div class="px-2.5 py-0.5 rounded-full border border-rose-600 bg-white font-mono text-[11px] font-bold text-rose-700 mb-1 shadow-xs">
                                            {{ $this->formatDateSafe($felInvoice->updated_at, 'd/m/Y H:i', '-') }}
                                        </div>
                                        <div class="flex items-center gap-1.5 justify-center">
                                            <span class="text-xs font-bold text-rose-700">Factura Anulada</span>
                                            <span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-800 text-[10px] font-bold">SIAT {{ $felInvoice->codigoEstado }}</span>
                                        </div>
                                        <span class="text-[11px] text-slate-500 truncate max-w-[200px]">
                                            Por: <strong class="text-slate-700">{{ $revocatedByUser ?: ($felInvoice->revocated_by ?: 'Usuario') }}</strong>
                                        </span>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    <!-- Panel 1: Cliente Receptor & Datos Fiscales SIAT -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Col 1: Datos del Cliente Receptor -->
                            <div class="space-y-3 border-b md:border-b-0 md:border-r border-slate-100 pb-4 md:pb-0 md:pr-6">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700">Cliente Receptor</h4>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div>
                                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Señor(es) / Razón Social:</span>
                                        <p class="font-bold text-slate-900 text-base mt-0.5" title="{{ $felInvoice->nombreRazonSocial ?? ($client->name ?? 'Cliente General') }}">
                                            {{ $felInvoice->nombreRazonSocial ?? ($client->name ?? 'Cliente General') }}
                                        </p>
                                    </div>
                                    <div class="flex justify-between items-center pt-1">
                                        <span class="text-xs font-medium text-slate-500">Documento:</span>
                                        <span class="font-bold text-slate-900 font-mono text-sm">
                                            <span class="bg-slate-100 px-2 py-0.5 rounded text-xs font-bold text-slate-700 mr-1 border border-slate-200">{{ $docTypeLabel }}</span>
                                            {{ $felInvoice->numeroDocumento ?? ($client->id_number ?? '-') }}
                                            @if($felInvoice && $felInvoice->complemento)
                                                - {{ $felInvoice->complemento }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">Código / N° Cliente:</span>
                                        <span class="font-semibold text-slate-800 font-mono">{{ $felInvoice->codigoCliente ?? ($client->number ?? '-') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center truncate">
                                        <span class="text-xs font-medium text-slate-500">Correo Electrónico:</span>
                                        <span class="font-medium text-slate-800 truncate ml-2" title="{{ $felInvoice->emailCliente ?? ($client->contacts[0]->email ?? '-') }}">
                                            {{ $felInvoice->emailCliente ?? ($client->contacts[0]->email ?? '-') }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">Teléfono:</span>
                                        <span class="font-medium text-slate-800">{{ $felInvoice->telefonoCliente ?? ($client->phone ?? ($client->contacts[0]->phone ?? '-')) }}</span>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-xs font-medium text-slate-500 flex-shrink-0 mr-2">Dirección:</span>
                                        <span class="font-medium text-slate-800 text-right text-xs">
                                            {{ $felInvoice->direccion ?? ($felInvoice->direccionComprador ?? ($client->address1 ?? '-')) }}
                                            @if($client && $client->city)
                                                , {{ $client->city }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                                        <span class="text-xs font-semibold text-slate-500">Asignado a:</span>
                                        <span class="font-medium text-slate-700 text-xs bg-slate-100 px-2 py-0.5 rounded border border-slate-200">{{ $assignedUserName ?: 'No asignado' }}</span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 pt-1">
                                        <div class="p-2 bg-slate-50 rounded-lg border border-slate-100">
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Balance Cliente</span>
                                            <span class="font-bold text-slate-800 font-mono text-xs">Bs. {{ number_format($client->balance ?? 0, 2) }}</span>
                                        </div>
                                        <div class="p-2 bg-slate-50 rounded-lg border border-slate-100">
                                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Pagado Histórico</span>
                                            <span class="font-bold text-emerald-700 font-mono text-xs">Bs. {{ number_format($client->paid_to_date ?? 0, 2) }}</span>
                                        </div>
                                    </div>
                                    @if($client && (!empty($client->public_notes) || !empty($client->private_notes)))
                                        <div class="pt-2 text-xs space-y-1">
                                            @if(!empty($client->public_notes))
                                                <div class="p-2 bg-sky-50 text-sky-900 rounded border border-sky-100">
                                                    <span class="font-bold block text-[10px] uppercase text-sky-700">Nota Pública Cliente:</span>
                                                    {{ $client->public_notes }}
                                                </div>
                                            @endif
                                            @if(!empty($client->private_notes))
                                                <div class="p-2 bg-amber-50 text-amber-900 rounded border border-amber-100">
                                                    <span class="font-bold block text-[10px] uppercase text-amber-700">Nota Privada Cliente:</span>
                                                    {{ $client->private_notes }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Col 2: Datos Fiscales SIAT & Emisión -->
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <div class="p-1.5 bg-sky-50 text-sky-600 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                                    </div>
                                    <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700">Datos Fiscales SIAT</h4>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">N° Factura:</span>
                                        <span class="font-bold text-slate-900 font-mono text-base">#{{ $felInvoice && $felInvoice->numeroFactura != 0 ? $felInvoice->numeroFactura : ($invoice->number ?? '-') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">NIT Emisor:</span>
                                        <span class="font-bold text-slate-800 font-mono">{{ $companyNit }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">Sucursal / Punto de Venta:</span>
                                        <span class="font-semibold text-slate-800">
                                            Sucursal {{ $felInvoice->codigoSucursal ?? '0' }} • PV {{ $felInvoice->codigoPuntoVenta ?? '0' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">Método de Pago:</span>
                                        <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-xs border border-slate-200">
                                            {{ $this->getPaymentMethodDescription($felInvoice->codigoMetodoPago ?? 1) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">Tipo de Cambio:</span>
                                        <span class="font-semibold text-slate-800 font-mono">
                                            Bs. {{ number_format($felInvoice->tipoCambio ?? ($invoice->exchange_rate ?? 1), 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">Modalidad:</span>
                                        <span class="font-medium text-slate-800">{{ $felInvoice->emission_type ?? 'En línea' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium text-slate-500">Emitido por:</span>
                                        <span class="font-semibold text-slate-800 text-xs truncate max-w-[200px]" title="{{ $emittedByUser ?: ($creatorName ?: 'Sistema') }}">
                                            {{ $emittedByUser ?: ($creatorName ?: 'Sistema') }}
                                        </span>
                                    </div>
                                    @if($felInvoice && !empty($felInvoice->revocated_by))
                                        <div class="flex justify-between items-center p-1.5 bg-rose-50 rounded border border-rose-200">
                                            <span class="text-xs font-bold text-rose-700">Anulado por:</span>
                                            <span class="font-bold text-rose-900 text-xs">{{ $revocatedByUser ?: $felInvoice->revocated_by }}</span>
                                        </div>
                                    @endif
                                    <div class="pt-1.5">
                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block mb-1">CUF Autorización:</span>
                                        <div class="flex items-center justify-between gap-2 bg-slate-50 border border-slate-200 px-3 py-1.5 rounded-xl">
                                            <span class="text-xs font-mono font-bold text-slate-800 truncate select-all" title="{{ $felInvoice->cuf ?? '' }}">
                                                {{ !empty($felInvoice->cuf) ? (substr($felInvoice->cuf, 0, 12) . '...' . substr($felInvoice->cuf, -8)) : 'Sin CUF' }}
                                            </span>
                                            @if($felInvoice && $felInvoice->cuf)
                                                <button type="button" 
                                                        @click="navigator.clipboard.writeText('{{ $felInvoice->cuf }}'); copiedCuf = true; setTimeout(() => copiedCuf = false, 2500)"
                                                        class="text-xs font-bold text-sky-600 hover:text-sky-800 transition-colors flex-shrink-0 underline decoration-sky-300"
                                                        title="Copiar CUF completo">
                                                    <span x-show="!copiedCuf">Copiar</span>
                                                    <span x-show="copiedCuf" style="display: none;" class="text-emerald-600 font-bold">¡Copiado!</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Panel 2: SIAT Errors / Observaciones (Solo si hay errores) -->
                    @if($hasValidErrors)
                        <div class="p-5 bg-red-50 border border-red-200 rounded-2xl shadow-sm">
                            <div class="flex items-start gap-3.5">
                                <div class="p-2 bg-red-100 text-red-600 rounded-xl mt-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-red-900 uppercase tracking-wider">Observaciones y Errores SIAT</h4>
                                    <div class="mt-2 space-y-1.5">
                                        @foreach($errors as $err)
                                            <div class="flex items-start gap-2.5 text-sm text-red-800">
                                                @if(is_array($err))
                                                    <span class="inline-block px-2 py-0.5 rounded bg-red-200 font-mono font-bold text-red-900 text-xs">[{{ $err['code'] ?? 'Error' }}]</span>
                                                    <span class="font-medium">{{ $err['description'] ?? json_encode($err) }}</span>
                                                @else
                                                    <span class="font-medium">• {{ $err }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Panel 3: Detalle de Ítems / Productos Facturados -->
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700 flex items-center gap-2">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                Detalle de Ítems Facturados ({{ count($items) }})
                            </h4>
                            <span class="text-xs font-medium text-slate-500">Moneda: <strong class="text-slate-800 font-mono">{{ ($felInvoice->codigoMoneda ?? 1) == 2 ? 'USD' : 'BOB' }}</strong></span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200 text-sm">
                                <thead class="bg-slate-50/50">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Código</th>
                                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Descripción</th>
                                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Cantidad</th>
                                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">P. Unit. (Bs.)</th>
                                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Desc. (Bs.)</th>
                                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Subtotal (Bs.)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-100">
                                    @forelse($items as $item)
                                        @php
                                            $itemCode = $item['codigoProducto'] ?? ($item['product_key'] ?? '-');
                                            $itemDesc = $item['descripcion'] ?? ($item['notes'] ?? ($item['product_name'] ?? 'Ítem sin descripción'));
                                            $itemQty = $item['cantidad'] ?? ($item['quantity'] ?? 1);
                                            $itemPrice = $item['precioUnitario'] ?? ($item['cost'] ?? 0);
                                            $itemDiscount = $item['montoDescuento'] ?? ($item['discount'] ?? 0);
                                            $itemSubtotal = $item['subTotal'] ?? ($item['line_total'] ?? ($itemQty * $itemPrice - $itemDiscount));
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs font-semibold text-slate-600">{{ $itemCode }}</td>
                                            <td class="px-5 py-3.5 text-slate-800 font-medium max-w-xs">
                                                <div class="truncate" title="{{ $itemDesc }}">{{ $itemDesc }}</div>
                                            </td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-right font-mono text-slate-700">{{ number_format($itemQty, 2) }}</td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-right font-mono text-slate-700">{{ number_format($itemPrice, 2) }}</td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-right font-mono text-slate-500">{{ number_format($itemDiscount, 2) }}</td>
                                            <td class="px-5 py-3.5 whitespace-nowrap text-right font-mono font-bold text-slate-900">Bs. {{ number_format($itemSubtotal, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-5 py-8 text-center text-slate-400 italic">No hay ítems registrados en esta factura</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Sub-bar Totales Numéricos de Factura -->
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-slate-500">Modalidad:</span>
                                <span class="text-xs font-semibold text-slate-800">{{ $felInvoice->emission_type ?? 'En línea' }}</span>
                            </div>

                            <div class="flex items-center gap-6 flex-wrap">
                                <div class="text-right">
                                    <span class="text-[11px] text-slate-500 uppercase tracking-wider block font-semibold">Sujeto a IVA:</span>
                                    <span class="text-sm font-bold text-slate-800 font-mono">Bs. {{ number_format($felInvoice->montoTotalSujetoIva ?? ($invoice->amount ?? 0), 2) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] text-slate-500 uppercase tracking-wider block font-semibold">Pagado:</span>
                                    <span class="text-sm font-bold text-emerald-700 font-mono">Bs. {{ number_format($paidVal, 2) }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] text-slate-500 uppercase tracking-wider block font-semibold">Balance:</span>
                                    <span class="text-sm font-bold {{ $balanceVal > 0 ? 'text-rose-600' : 'text-slate-700' }} font-mono">
                                        Bs. {{ number_format($balanceVal, 2) }}
                                    </span>
                                </div>
                                <div class="px-4 py-2 bg-sky-600 text-white rounded-xl shadow-sm flex items-center gap-2">
                                    <span class="font-semibold uppercase tracking-wider text-xs">Total Facturado:</span>
                                    <span class="font-bold text-lg font-mono">Bs. {{ number_format($amountVal, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Panel 4: Términos, Notas y Campos Personalizados de la Factura -->
                    @if($invoice && (!empty($invoice->terms) || !empty($invoice->public_notes) || !empty($invoice->private_notes) || !empty($invoice->custom_value1) || !empty($invoice->custom_value2) || !empty($invoice->custom_value3) || !empty($invoice->custom_value4) || !empty($invoice->document_search) || !empty($felInvoice->document_search)))
                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-3">
                            <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                                <div class="p-1 bg-slate-100 text-slate-700 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700">Términos, Notas y Metadatos de la Factura</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                @if(!empty($invoice->terms))
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                        <span class="font-bold text-slate-500 uppercase tracking-wider block text-[10px] mb-1">Términos y Condiciones:</span>
                                        <p class="text-slate-800 font-medium whitespace-pre-line">{{ $invoice->terms }}</p>
                                    </div>
                                @endif

                                @if(!empty($invoice->public_notes))
                                    <div class="p-3 bg-sky-50/70 rounded-xl border border-sky-200/80">
                                        <span class="font-bold text-sky-700 uppercase tracking-wider block text-[10px] mb-1">Nota Pública:</span>
                                        <p class="text-slate-800 font-medium whitespace-pre-line">{{ $invoice->public_notes }}</p>
                                    </div>
                                @endif

                                @if(!empty($invoice->private_notes))
                                    <div class="p-3 bg-amber-50/70 rounded-xl border border-amber-200/80">
                                        <span class="font-bold text-amber-800 uppercase tracking-wider block text-[10px] mb-1">Nota Privada (Backoffice):</span>
                                        <p class="text-slate-800 font-medium whitespace-pre-line">{{ $invoice->private_notes }}</p>
                                    </div>
                                @endif

                                @if(!empty($invoice->document_search) || !empty($felInvoice->document_search))
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                                        <span class="font-bold text-slate-500 uppercase tracking-wider block text-[10px] mb-1">Document Search / Búsqueda:</span>
                                        <p class="text-slate-800 font-mono">{{ $invoice->document_search ?? $felInvoice->document_search }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Campos Custom 1-4 -->
                            @if(!empty($invoice->custom_value1) || !empty($invoice->custom_value2) || !empty($invoice->custom_value3) || !empty($invoice->custom_value4))
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-slate-100">
                                    @if(!empty($invoice->custom_value1))
                                        <div class="p-2 bg-slate-50 rounded-lg border border-slate-200">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Campo Custom 1</span>
                                            <span class="font-semibold text-slate-800 text-xs">{{ $invoice->custom_value1 }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($invoice->custom_value2))
                                        <div class="p-2 bg-slate-50 rounded-lg border border-slate-200">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Campo Custom 2</span>
                                            <span class="font-semibold text-slate-800 text-xs">{{ $invoice->custom_value2 }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($invoice->custom_value3))
                                        <div class="p-2 bg-slate-50 rounded-lg border border-slate-200">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Campo Custom 3</span>
                                            <span class="font-semibold text-slate-800 text-xs">{{ $invoice->custom_value3 }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($invoice->custom_value4))
                                        <div class="p-2 bg-slate-50 rounded-lg border border-slate-200">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase block">Campo Custom 4</span>
                                            <span class="font-semibold text-slate-800 text-xs">{{ $invoice->custom_value4 }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                <!-- COLUMNA DERECHA (5ta parte): CAMPOS ADICIONALES Y DATOS SECTORIALES (Formato Key-Value Table con Scroll) -->
                <div class="space-y-4" style="min-width: 0;">
                    
                    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                            <div class="flex items-center gap-2">
                                <div class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-extrabold uppercase tracking-wider text-slate-800">
                                        Campos Sectoriales
                                    </h4>
                                    <p class="text-[11px] text-slate-500">Parámetros de factura fiscal</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2.5 py-0.5 rounded-full">
                                {{ count($extras) }} campos
                            </span>
                        </div>

                        <!-- Key-Value Structured Table con Scroll Vertical Dedicado -->
                        @if(!empty($extras))
                            <div class="border border-slate-200 rounded-xl divide-y divide-slate-100 bg-white overflow-y-auto"
                                 style="max-height: 520px;">
                                @foreach($extras as $key => $val)
                                    <div class="p-3 hover:bg-slate-50/80 transition-colors">
                                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-0.5">
                                            {{ \Illuminate\Support\Str::headline($key) }}
                                        </span>
                                        <div class="font-mono text-xs font-semibold text-slate-900 select-all break-words">
                                            {{ is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : ($val !== '' && $val !== null ? $val : '-') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-6 text-center bg-slate-50 rounded-xl border border-slate-200">
                                <p class="text-xs text-slate-500 font-medium">No se registraron parámetros sectoriales adicionales para esta factura.</p>
                            </div>
                        @endif

                        @if(!empty($invoice->document_data) || !empty($felInvoice->document_data))
                            <div class="pt-2 border-t border-slate-100">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider block mb-2">Document Data (JSON Estructurado):</span>
                                <pre class="p-3 bg-slate-900 text-slate-200 rounded-xl text-xs font-mono overflow-x-auto max-h-44">{{ json_encode($invoice->document_data ?? $felInvoice->document_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </div>

        <!-- Footer Actions Bar (Static / Pinned) -->
        <div class="px-6 py-3.5 bg-white border-t border-slate-200 flex items-center justify-between shadow-sm"
             style="flex-shrink: 0; z-index: 10;">
            <div class="text-sm text-slate-500 font-medium">
                <span>Factura ID: <strong class="font-mono text-slate-800">#{{ $invoice->id ?? '-' }}</strong></span>
                <span class="mx-2.5">•</span>
                <span>FEL ID: <strong class="font-mono text-slate-800">#{{ $felInvoice->id ?? '-' }}</strong></span>
            </div>

            <button type="button" wire:click="closeModal" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-bold rounded-xl shadow-sm transition-colors">
                Cerrar Ventana
            </button>
        </div>
    </div>
</div>
