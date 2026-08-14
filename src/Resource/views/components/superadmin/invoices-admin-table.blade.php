<div x-data="{ pdfModalOpen: false, currentPdfUrl: '', currentInvoiceId: '' }" @open-pdf.window="currentPdfUrl = $event.detail.url; currentInvoiceId = $event.detail.id; pdfModalOpen = true" class="container px-4 mx-auto @if(empty($company_id)) py-8 @endif">
        
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
            <p class="text-sm text-slate-500 mt-1">Listado de documentos y estado de emisión.</p>
        </div>
        @endif

        <!-- Section 2: Filters & Search -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-6">
            @component('prepagobags::components.shared.table-filters', [
                'searchPlaceholder' => 'Buscar por CUF, Documento o Razón Social'
            ])
                @slot('filters')
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="relative w-full md:w-48">
                            <select wire:model="estado"
                                class="w-full rounded-xl border border-slate-200 appearance-none bg-slate-50 text-slate-600 py-2.5 px-4 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all">
                                <option value="" selected>Todos los estados</option>
                                <option value="690">Válido</option>
                                <option value="902">Rechazado</option>
                                <option value="905">Anulado</option>
                                <option value="904">Pendiente</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                        <div class="relative w-full md:w-48">
                            <select wire:model="prefactura"
                                class="w-full rounded-xl border border-slate-200 appearance-none bg-slate-50 text-slate-600 py-2.5 px-4 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all">
                                <option value="inactive" selected >Facturas</option>
                                <option value="active" >Prefacturas</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                @endslot
            @endcomponent
        </div>
        
        <!-- Section 3: Data / Table -->

        <div>
            @if (session()->has('message'))
                <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 dark:bg-gray-800 dark:text-emerald-400" role="alert">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    {{ session('error') }}
                </div>
            @endif
        </div>


    <div class="flex flex-col">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="relative overflow-hidden border border-slate-200 shadow-sm md:rounded-lg min-h-[300px]">
                    @include('prepagobags::components.shared.table-spinner')
                    <table class="min-w-full divide-y divide-slate-200 transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none">
                        <thead class="bg-sky-600">
                            <tr>
                            @if($prefactura == "inactive")
                                <th scope="col" class="px-4 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Estado
                                </th>
                            @endif
                                <th scope="col" class="px-4 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    #
                                </th>

                                <th scope="col" class="px-4 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Fecha emisión
                                </th>

                                <th scope="col" class="px-4 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Razón Social
                                </th>
                                <th scope="col" class="px-4 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Número Documento
                                </th>

                                <th scope="col" class="px-4 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Monto total
                                </th>
                                @if(empty($company_id))
                                <th scope="col" class="px-4 py-3 text-xs font-semibold tracking-wider text-left uppercase text-white">
                                    Compañia
                                </th>
                                @endif

                                <th scope="col" class="relative py-3 px-4">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($invoices as $invoice)
                            <tr class="hover:bg-slate-50 transition-colors">
                            @if($prefactura == "inactive")
                                <td class="px-4 text-sm whitespace-nowrap">
                                    @php
                                        $statusClass = 'bg-slate-50 text-slate-600 ring-1 ring-slate-500/20';
                                        $statusText = 'Desconocido';
                                        if($invoice['codigoEstado'] == 690) {
                                            $statusClass = 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20';
                                            $statusText = 'Válido';
                                        } elseif($invoice['codigoEstado'] == 905) {
                                            $statusClass = 'bg-slate-50 text-slate-600 ring-1 ring-slate-500/20';
                                            $statusText = 'Anulado';
                                        } elseif($invoice['codigoEstado'] == 902) {
                                            $statusClass = 'bg-red-50 text-red-600 ring-1 ring-red-500/20';
                                            $statusText = 'Rechazado';
                                        } elseif($invoice['codigoEstado'] == 904 || $invoice['codigoEstado'] == 908) {
                                            $statusClass = 'bg-amber-50 text-amber-600 ring-1 ring-amber-500/20';
                                            $statusText = 'Pendiente';
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                @endif
                                <td class="px-4 py-3 text-sm text-slate-700 whitespace-nowrap">{{ (int)$invoice['numeroFactura'] != 0 ? (int)$invoice['numeroFactura'] : "Prefactura-".(int)$invoice['prefactura_number'] }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $invoice['fechaEmision'] }}</td>
                                <td class="px-4 py-3 text-sm text-slate-800 font-medium whitespace-nowrap">{{ $invoice['nombreRazonSocial'] }}</td>
                                <td class="px-4 py-3 text-sm text-slate-600 whitespace-nowrap">{{ $invoice['numeroDocumento'] }}</td>
                                <td class="px-4 py-3 text-sm text-slate-800 font-medium whitespace-nowrap">{{ number_format($invoice['amount'],2) }}</td>
                                @if(empty($company_id))
                                <td class="px-4 py-3 text-sm text-slate-600" title="{{ trim($invoice['company_name'],'"')}}">
                                    <a href="{{route('admin.companies.profile', [$invoice['companyid'], 'tab' => 'invoices'])}}" class="text-sky-600 hover:text-sky-800 font-medium transition-colors"> 
                                    {{ \Illuminate\Support\Str::limit(trim($invoice['company_name'],'"'), $limit = 15, $end = '...')  }} 
                                    </a>
                                </td>
                                @endif
                                <td class="px-4 py-3 inline-table" >
                                    <div class="absolute z-10" x-data="{ isOpen: false }">
                                          <button @click="isOpen = !isOpen" class="p-1.5 text-sm font-medium text-center text-slate-500 bg-white rounded-md hover:bg-slate-100 hover:text-slate-800 focus:outline-none transition-colors" type="button"> 
     
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
                                                <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                                            </svg>
                                        </button>

                                        <div x-show="isOpen" @click.away="isOpen = false" class="absolute right-10 bg-white border border-slate-200 rounded-lg shadow-lg w-44 inline-table z-70">
      
                                        <ul class="py-1 text-sm text-slate-600 text-left">
            @if($invoice['codigoEstado'] == 690 || $invoice['codigoEstado'] == 902)
                <li>
                    <button wire:click="viewPdf('{{ $invoice['id'] }}')" @click="isOpen = false" class="w-full text-left block px-4 py-2 hover:bg-slate-50 hover:text-blue-600 font-medium transition-colors">
                        Ver PDF
                    </button>
                </li>
                <li>
                    <button wire:click="$emit('openEditInvoiceModal', '{{ $invoice['id'] }}')" class="w-full text-left block px-4 py-2 hover:bg-slate-50 hover:text-emerald-600 font-medium transition-colors">
                        Editar Factura
                    </button>
                </li>
            @endif
        </ul>
                                          
                                        </div>
                                    </div>
                                    
                                </td>
                            </tr>

                            @empty
                                <tr class="group center">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 text-center" colspan="100%">
                                        {{ ctrans('texts.no_results') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <!-- Edit Invoice Modal Component -->
    @livewire('prepagobags::superadmin.invoice-edit-modal')
    
    <!-- PDF Viewer Modal -->
    <div x-show="pdfModalOpen" 
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
    
    </div>
 
  

        <nav role="navigation" aria-label="Pagination Navigation" class="flex">
            <span>
                {{-- Previous Page Link --}}
                @if ($onFirstPage)
                     <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-slate-50 border border-slate-200 cursor-not-allowed leading-5 rounded-md mr-4 mt-5 text-slate-400">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button wire:click="changePage(false)" wire:loading.attr="disabled" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-slate-300 leading-5 rounded-md text-slate-700 hover:bg-slate-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-all mr-4 mt-5">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif
            </span>
 
            <span>
                {{-- Next Page Link --}}
                @if ($hasMorePages)
                    <button wire:click="changePage(true)" wire:loading.attr="disabled" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-slate-300 leading-5 rounded-md text-slate-700 hover:bg-slate-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600/20 transition-all mr-4 mt-5">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-slate-50 border border-slate-200 cursor-not-allowed leading-5 rounded-md mr-4 mt-5 text-slate-400">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </span>
        </nav>


</div>