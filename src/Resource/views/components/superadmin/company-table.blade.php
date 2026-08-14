    <div class="container px-4 mx-auto py-8">
        
        <!-- Section 1: Header & Breadcrumbs -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
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
                                <span class="ml-1 md:ml-2 text-slate-800 font-medium">Compañías</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold leading-tight text-slate-900">Compañías</h2>
                <p class="text-sm text-slate-500 mt-1">Gestión de cuentas y estado de suscripciones.</p>
            </div>
        </div>
        
        <!-- Section 2: Filters & Search -->
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-6">
            @component('prepagobags::components.shared.table-filters', [
                'searchPlaceholder' => 'Buscar por Nombre, NIT o Correo'
            ])
                @slot('filters')
                    <div class="relative w-full md:w-56">
                        <select wire:model="phase" class="w-full rounded-xl border border-slate-200 appearance-none bg-slate-50 text-slate-600 py-2.5 px-4 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all">
                                <option value="todos" >Todos los estados</option>
                                <option value="Production" selected>Producción</option>
                                <option value="Piloto testing" >Pruebas piloto</option>
                                <option value="Testing">Pruebas</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                @endslot
            @endcomponent
        </div>

        <!-- Section 3: Data / Table -->

    <div class="flex flex-col">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="relative overflow-hidden border border-slate-200 shadow-sm md:rounded-xl bg-white min-h-[300px]">
                    @include('prepagobags::components.shared.table-spinner')
                    <table class="min-w-full divide-y divide-slate-200 transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none">
                        <thead class="bg-sky-600">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    ID
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    Compañía
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    NIT
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    Estado
                                </th>
                                <th scope="col" class="px-6 py-4 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    Administrador
                                </th>
                                <th scope="col" class="relative px-6 py-4">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($companies as $company)
                            <tr class="hover:bg-slate-50/50 transition-colors {{ $company->email == 'root@emizor.com' ? 'bg-blue-50/50' : '' }}">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 whitespace-nowrap">
                                    #{{ $company->id }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 whitespace-nowrap">
                                    {{ $company->company_name == "" ? "SIN NOMBRE" : trim($company->company_name,'"') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                                    {{ $company->company_id_number == "" ? "-" : trim($company->company_id_number,'"') }}
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    @php
                                        $statusClass = 'bg-slate-100 text-slate-600';
                                        if($company->phase == "Production") $statusClass = 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-500/20';
                                        elseif($company->phase == "Testing") $statusClass = 'bg-amber-50 text-amber-600 ring-1 ring-amber-500/20';
                                        elseif($company->phase == "Piloto testing") $statusClass = 'bg-sky-50 text-sky-600 ring-1 ring-sky-500/20';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $company->phase == "Production" ? "Producción" : ($company->phase=="Testing" ? "Pruebas" : "Piloto pruebas") }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm whitespace-nowrap">
                                    <div class="flex items-center gap-x-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                            {{ strtoupper(substr($company->first_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h2 class="text-sm font-medium text-slate-900">{{ $company->first_name . " ". $company->last_name }}</h2>
                                            <p class="text-xs text-slate-500">{{ $company->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4 text-right whitespace-nowrap relative" x-data="{ isOpen: false }">
                                    <button @click="isOpen = !isOpen" class="text-slate-400 hover:text-blue-600 focus:outline-none p-2 rounded-lg hover:bg-blue-50 transition-colors" type="button"> 
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>

                                    <div x-show="isOpen" @click.away="isOpen = false" x-transition.opacity class="absolute right-8 top-10 bg-white border border-slate-100 rounded-xl shadow-lg w-48 z-20 overflow-hidden">
                                        <ul class="py-1 text-sm text-slate-600 text-left">
                                            <li>
                                                <a href="{{route('admin.companies.profile', $company->id)}}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600 font-medium transition-colors">Revisar Cuenta</a>
                                            </li>
                                            <li>
                                                <a href="{{route('admin.companies.profile', [$company->id, 'tab' => 'invoices'])}}" class="block px-4 py-2 hover:bg-slate-50 hover:text-blue-600 transition-colors">Facturas</a>
                                            </li>
                                            <li class="border-t border-slate-100 my-1"></li>
                                            <li>
                                                <button class="w-full text-left block px-4 py-2 hover:bg-slate-50 hover:text-blue-600 transition-colors" onclick="Livewire.emit('openModal', 'piloto-up', [{{$company->id}}])">Pasar a pruebas piloto</button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-8 text-center whitespace-nowrap text-sm text-slate-500" colspan="100%">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <p>{{ ctrans('texts.no_results') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if( $companies->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center mt-8 mb-4">
            <span class="flex space-x-2">
                {{-- Previous Page Link --}}
                @if ($companies->onFirstPage())
                    <span class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-slate-200 cursor-not-allowed rounded-lg text-slate-400 shadow-sm">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif
 
                {{-- Next Page Link --}}
                @if ($companies->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="inline-flex items-center px-4 py-2 text-sm font-medium bg-white border border-slate-200 cursor-not-allowed rounded-lg text-slate-400 shadow-sm">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </span>
        </nav>
    @endif
</div>