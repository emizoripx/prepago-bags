@php
    $sectorNames = \EmizorIpx\ClientFel\Utils\TypeDocumentSector::ARRAY_NAMES ?? [];
@endphp

<div x-data="{ copiedCufId: null }" 
     class="container px-4 mx-auto @if(empty($company_id)) py-8 @endif">
        
    @if(empty($company_id))
    <!-- Section 1: Header & Breadcrumbs (Only show if NOT embedded in a company profile) -->
    <div class="mb-6">
        <nav class="flex text-sm text-slate-500 mb-3" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                        Inicio
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="ml-1 md:ml-2 text-slate-800 font-medium">Facturas</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h2 class="text-2xl font-bold leading-tight text-slate-900">Facturas</h2>
        <p class="text-sm text-slate-500 mt-1">Listado de documentos fiscales, balance de cobro y estado de emisión SIAT.</p>
    </div>
    @endif

    <!-- Section 2: Filters & Search -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-6">
        @component('prepagobags::components.shared.table-filters', [
            'searchPlaceholder' => 'Buscar por CUF, N° Factura, Documento o Razón Social'
        ])
            @slot('filters')
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Filter: Estado SIAT -->
                    <div class="relative w-full md:w-44">
                        <select wire:model="estado"
                            class="w-full rounded-xl border border-slate-200 appearance-none bg-slate-50 text-slate-600 text-xs py-2.5 px-3 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all font-medium">
                            <option value="" selected>Todos los estados SIAT</option>
                            <option value="690">Válido</option>
                            <option value="902">Rechazado</option>
                            <option value="905">Anulado</option>
                            <option value="904">Pendiente</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Filter: Prefacturas / Facturas -->
                    <div class="relative w-full md:w-36">
                        <select wire:model="prefactura"
                            class="w-full rounded-xl border border-slate-200 appearance-none bg-slate-50 text-slate-600 text-xs py-2.5 px-3 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all font-medium">
                            <option value="inactive" selected>Facturas</option>
                            <option value="active">Prefacturas</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Filter: Estado de Pago / Balance -->
                    <div class="relative w-full md:w-44">
                        <select wire:model="payment_status"
                            class="w-full rounded-xl border border-slate-200 appearance-none bg-slate-50 text-slate-600 text-xs py-2.5 px-3 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all font-medium">
                            <option value="" selected>Todos los pagos</option>
                            <option value="paid">Pagadas (Balance 0)</option>
                            <option value="pending_balance">Con balance pendiente</option>
                            <option value="partial">Pagos parciales</option>
                            <option value="unpaid">Completamente impagas</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            @endslot
        @endcomponent
    </div>
    
    <!-- Alerts / Flash Messages -->
    <div>
        @if (session()->has('message'))
            <div class="p-4 mb-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center" role="alert">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200 flex items-center" role="alert">
                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                {{ session('error') }}
            </div>
        @endif
    </div>

    <!-- Section 3: Data / Table -->
    <div class="flex flex-col">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="relative overflow-x-auto border border-slate-200 shadow-sm md:rounded-xl min-h-[350px] pb-24 bg-white">
                    @include('prepagobags::components.shared.table-spinner')
                    <table class="min-w-full divide-y divide-slate-200 transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none">
                        <thead class="bg-sky-600">
                            <tr>
                                @if($prefactura == "inactive")
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Estado SIAT
                                </th>
                                @endif
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    # Doc
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Tipo de Factura
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    CUF
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Fecha Emisión
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Razón Social
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    NIT / Doc
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Monto / Balance
                                </th>
                                @if(empty($company_id))
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Compañía
                                </th>
                                @endif
                                <th scope="col" class="relative py-3 px-3 text-center text-xs font-semibold uppercase text-white">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($invoices as $invoice)
                            @php
                                $sectorId = $invoice['type_document_sector_id'] ?? 1;
                                $sectorLabel = $sectorNames[$sectorId] ?? 'Compra Venta';
                                $cufFull = $invoice['cuf'] ?? '';
                                $cufShort = !empty($cufFull) ? (substr($cufFull, 0, 6) . '...' . substr($cufFull, -4)) : '';
                                $balanceVal = (float)($invoice['balance'] ?? 0);
                                $amountVal = (float)($invoice['amount'] ?? 0);

                                // Mapping Tipo Documento Identidad
                                $identityTypes = [
                                    1 => 'CI',
                                    2 => 'CEX',
                                    3 => 'PAS',
                                    4 => 'OD',
                                    5 => 'NIT'
                                ];
                                $docTypeCode = $invoice['codigoTipoDocumentoIdentidad'] ?? 5;
                                $docTypeLabel = $identityTypes[$docTypeCode] ?? 'NIT';

                                // Evaluación de Error / Rechazo
                                $hasError = ($invoice['codigoEstado'] == 902) || (!empty($invoice['errores']) && $invoice['codigoEstado'] != 690);
                                $parsedErrors = [];
                                if (!empty($invoice['errores'])) {
                                    if (is_array($invoice['errores'])) {
                                        $parsedErrors = $invoice['errores'];
                                    } else {
                                        $decoded = json_decode($invoice['errores'], true);
                                        $parsedErrors = is_array($decoded) ? $decoded : [['description' => (string)$invoice['errores']]];
                                    }
                                } elseif ($invoice['codigoEstado'] == 902) {
                                    $parsedErrors = [['code' => 902, 'description' => 'Factura rechazada por el Servicio de Impuestos Nacionales (SIN).']];
                                }
                            @endphp
                            <tr class="transition-colors @if($hasError) bg-rose-50/75 hover:bg-rose-100/90 border-l-4 border-l-rose-500 cursor-pointer @else hover:bg-slate-50 @endif"
                                @if($hasError)
                                wire:click="openDetail('{{ $invoice['id'] }}', 'general')"
                                title="Haga clic en la fila para ver los detalles y errores del SIAT"
                                @endif>
                                
                                <!-- Estado SIAT -->
                                @if($prefactura == "inactive")
                                <td class="px-3 py-3 text-xs whitespace-nowrap">
                                    @php
                                        $statusClass = 'bg-slate-50 text-slate-600 ring-1 ring-slate-500/20';
                                        $statusText = 'Desconocido';
                                        if($invoice['codigoEstado'] == 690) {
                                            $statusClass = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20';
                                            $statusText = 'Válido';
                                        } elseif($invoice['codigoEstado'] == 905 || $invoice['codigoEstado'] == 691) {
                                            $statusClass = 'bg-slate-100 text-slate-700 ring-1 ring-slate-600/20';
                                            $statusText = 'Anulado';
                                        } elseif($invoice['codigoEstado'] == 902) {
                                            $statusClass = 'bg-red-50 text-red-700 ring-1 ring-red-600/20';
                                            $statusText = 'Rechazado';
                                        } elseif($invoice['codigoEstado'] == 904 || $invoice['codigoEstado'] == 908) {
                                            $statusClass = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20';
                                            $statusText = 'Pendiente';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                @endif

                                <!-- Número de Factura -->
                                <td class="px-3 py-3 text-xs font-bold text-slate-900 whitespace-nowrap font-mono">
                                    #{{ $invoice['numeroFactura'] ?? '-' }}
                                </td>

                                <!-- Tipo de Factura Sectorial -->
                                <td class="px-3 py-3 text-xs text-slate-700 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-sky-50 text-sky-800 text-[11px] font-medium border border-sky-200/60" title="{{ $sectorLabel }}">
                                        {{ \Illuminate\Support\Str::limit($sectorLabel, 20, '...') }}
                                    </span>
                                </td>

                                <!-- CUF (Truncado + Copia 1-Click con Icono) -->
                                <td class="px-3 py-3 text-xs whitespace-nowrap">
                                    @if(!empty($cufFull))
                                        <div class="inline-flex items-center gap-1.5 bg-slate-50 hover:bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 shadow-sm transition-colors">
                                            <span class="font-mono text-[11px] font-medium text-slate-700 select-all" title="{{ $cufFull }}">{{ $cufShort }}</span>
                                            <button type="button" 
                                                    @click.stop="navigator.clipboard.writeText('{{ $cufFull }}'); copiedCufId = '{{ $invoice['id'] }}'; setTimeout(() => copiedCufId = null, 2000)"
                                                    class="inline-flex items-center justify-center p-1 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded transition-all focus:outline-none"
                                                    title="Copiar CUF completo al portapapeles">
                                                <svg x-show="copiedCufId !== '{{ $invoice['id'] }}'" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                <svg x-show="copiedCufId === '{{ $invoice['id'] }}'" style="display: none;" class="w-3.5 h-3.5 text-emerald-600 font-bold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic">Sin CUF</span>
                                    @endif
                                </td>

                                <!-- Fecha Emisión -->
                                <td class="px-3 py-3 text-xs text-slate-600 whitespace-nowrap font-mono">
                                    {{ !empty($invoice['fechaEmision']) ? \Carbon\Carbon::parse($invoice['fechaEmision'])->format('d/m/Y H:i') : '-' }}
                                </td>

                                <!-- Razón Social -->
                                <td class="px-3 py-3 text-xs text-slate-800 font-medium whitespace-nowrap" title="{{ $invoice['nombreRazonSocial'] }}">
                                    {{ \Illuminate\Support\Str::limit($invoice['nombreRazonSocial'], 22, '...') }}
                                </td>

                                <!-- NIT / N° Documento (<tipo doc>;<num doc>) -->
                                <td class="px-3 py-3 text-xs font-mono whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <span class="font-semibold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded text-[11px] border border-slate-200">{{ $docTypeLabel }}</span>
                                        <span class="text-slate-400 font-bold">;</span>
                                        <span class="text-slate-800 font-medium">{{ $invoice['numeroDocumento'] ?? '-' }}</span>
                                    </div>
                                </td>

                                <!-- Monto & Balance Pendiente (Pagado / Por cobrar) -->
                                <td class="px-3 py-3 text-xs whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-slate-900">Bs. {{ number_format($amountVal, 2) }}</span>
                                        <div class="mt-0.5">
                                            @if($balanceVal <= 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    Pagado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200" title="Saldo pendiente: Bs. {{ number_format($balanceVal, 2) }}">
                                                    Por cobrar (Bs. {{ number_format($balanceVal, 2) }})
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Compañía (Global view) -->
                                @if(empty($company_id))
                                <td class="px-3 py-3 text-xs text-slate-600" title="{{ trim($invoice['company_name'] ?? '', '"') }}">
                                    <a href="{{ route('admin.companies.profile', [$invoice['companyid'], 'tab' => 'invoices']) }}" class="text-sky-600 hover:text-sky-800 font-medium transition-colors"> 
                                        {{ \Illuminate\Support\Str::limit(trim($invoice['company_name'] ?? '', '"'), 14, '...') }} 
                                    </a>
                                </td>
                                @endif

                                <!-- Acciones Dropdown -->
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <div class="relative inline-block text-left" x-data="{ isOpen: false }">
                                        <button @click.stop="isOpen = !isOpen" 
                                                class="inline-flex items-center justify-center w-8 h-8 text-slate-500 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg border border-slate-200 transition-colors focus:outline-none shadow-sm" 
                                                type="button"
                                                title="Acciones de la factura"> 
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>

                                        <div x-show="isOpen" 
                                             @click.away="isOpen = false" 
                                             style="display: none;"
                                             class="absolute right-0 mt-2 bg-white border border-slate-200 rounded-xl shadow-2xl w-56 z-50 py-1.5 text-xs text-slate-700 divide-y divide-slate-100">
                                            
                                            <!-- Group 1: General & Detail -->
                                            <div class="py-1">
                                                <button type="button" wire:click="openDetail('{{ $invoice['id'] }}', 'general')" @click="isOpen = false" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-sky-50 hover:text-sky-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-sky-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    <span>Ver Detalle Completo</span>
                                                </button>

                                                <button type="button" wire:click="reemitInvoice('{{ $invoice['id'] }}')" @click="isOpen = false" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    <span>Reemitir Factura a SIAT</span>
                                                </button>
                                            </div>

                                            <!-- Group 2: PDF & Docs -->
                                            <div class="py-1">
                                                <button type="button" wire:click="viewPdf('{{ $invoice['id'] }}')" @click="isOpen = false" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                    <span>Ver PDF</span>
                                                </button>

                                                <button type="button" wire:click="regeneratePdf('{{ $invoice['id'] }}')" @click="isOpen = false" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    <span>Regenerar PDF</span>
                                                </button>
                                            </div>

                                            <!-- Group 3: Pagos & Cliente -->
                                            <div class="py-1">
                                                <button type="button" wire:click="openDetail('{{ $invoice['id'] }}', 'payments')" @click="isOpen = false" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-purple-50 hover:text-purple-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                                    <span>Ver Pagos</span>
                                                </button>

                                                <button type="button" wire:click="openDetail('{{ $invoice['id'] }}', 'client')" @click="isOpen = false" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-teal-50 hover:text-teal-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-teal-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                    <span>Ver Cliente</span>
                                                </button>
                                            </div>

                                            <!-- Group 4: Edit -->
                                            <div class="pt-1">
                                                <button type="button" wire:click="$emit('openEditInvoiceModal', '{{ $invoice['id'] }}')" @click="isOpen = false" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                    <span>Editar Factura</span>
                                                </button>
                                            </div>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="group center">
                                <td class="px-6 py-8 whitespace-nowrap text-sm text-slate-400 text-center" colspan="100%">
                                    No se encontraron facturas con los filtros seleccionados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Invoice Detail Modal Component -->
    @livewire('prepagobags::superadmin.invoice-detail-modal')

    <!-- Edit Invoice Modal Component -->
    @livewire('prepagobags::superadmin.invoice-edit-modal')
    
    <!-- PDF Viewer Modal (Self-contained Alpine State) -->
    <div x-data="{ 
             pdfModalOpen: false, 
             currentPdfUrl: '', 
             currentInvoiceId: '' 
         }"
         @open-pdf.window="currentPdfUrl = $event.detail.url; currentInvoiceId = $event.detail.id; pdfModalOpen = true"
         x-show="pdfModalOpen" 
         style="display: none;"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/75 backdrop-blur-sm"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="relative w-full max-w-5xl h-[90vh] p-4 flex flex-col bg-white rounded-2xl shadow-2xl" @click.away="pdfModalOpen = false">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">Visualizador de PDF</h3>
                <div class="flex items-center space-x-2">
                    <button @click="$wire.regeneratePdf(currentInvoiceId); pdfModalOpen = false" class="px-4 py-2 text-sm font-medium text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-lg transition-colors flex items-center shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Regenerar PDF
                    </button>
                    <template x-if="currentPdfUrl">
                        <a :href="currentPdfUrl" download class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Descargar PDF
                        </a>
                    </template>
                    <button @click="pdfModalOpen = false" class="text-slate-400 hover:bg-slate-100 hover:text-slate-900 rounded-lg p-2 transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
            </div>
            
            <!-- Iframe body -->
            <div class="flex-1 w-full h-full bg-slate-100 p-2 overflow-hidden rounded-b-2xl relative">
                <template x-if="currentPdfUrl">
                    <iframe :src="currentPdfUrl" class="w-full h-full rounded border-0" title="PDF Viewer"></iframe>
                </template>
                <template x-if="!currentPdfUrl">
                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50 rounded-b-2xl">
                        <svg class="w-16 h-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h4 class="text-lg font-medium text-slate-700 mb-2">El PDF aún no ha sido generado</h4>
                        <p class="text-sm text-slate-500 mb-6">Genera el documento para poder previsualizarlo y descargarlo.</p>
                        <button @click="$wire.regeneratePdf(currentInvoiceId); pdfModalOpen = false" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors flex items-center shadow-sm">
                            Generar PDF de la Factura
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <nav role="navigation" aria-label="Pagination Navigation" class="flex mt-5">
        <span>
            {{-- Previous Page Link --}}
            @if ($onFirstPage)
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-slate-50 border border-slate-200 cursor-not-allowed leading-5 rounded-md mr-4 text-slate-400">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button wire:click="changePage(false)" wire:loading.attr="disabled" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-slate-300 leading-5 rounded-md text-slate-700 hover:bg-slate-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-all mr-4">
                    {!! __('pagination.previous') !!}
                </button>
            @endif
        </span>

        <span>
            {{-- Next Page Link --}}
            @if ($hasMorePages)
                <button wire:click="changePage(true)" wire:loading.attr="disabled" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-slate-300 leading-5 rounded-md text-slate-700 hover:bg-slate-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-all mr-4">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-slate-50 border border-slate-200 cursor-not-allowed leading-5 rounded-md mr-4 text-slate-400">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </span>
    </nav>
</div>