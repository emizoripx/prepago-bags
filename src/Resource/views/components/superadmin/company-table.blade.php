<div class="container px-4 mx-auto py-8">
    
    <!-- Section 1: Header & Breadcrumbs -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="flex text-xs text-slate-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center hover:text-blue-600 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                            Inicio
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                            <span class="ml-1 text-slate-800 font-medium">Compañías</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h2 class="text-2xl font-bold leading-tight text-slate-900">Compañías</h2>
            <p class="text-xs text-slate-500 mt-1">Gestión de cuentas registradas, fases SIAT y suscripciones activas.</p>
        </div>
        
        <div>
            <button type="button" onclick="Livewire.emit('openCreateCompanyModal')" class="inline-flex items-center px-3.5 py-2 border border-transparent shadow-sm text-xs font-semibold rounded-xl text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Crear Compañía
            </button>
        </div>
    </div>
    
    <!-- Section 2: Filters & Search -->
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-4 mb-6">
        @component('prepagobags::components.shared.table-filters', [
            'searchPlaceholder' => 'Buscar por Nombre, NIT o Correo'
        ])
            @slot('filters')
                <div class="relative w-full md:w-52">
                    <select wire:model="phase" class="w-full rounded-xl border border-slate-200 appearance-none bg-slate-50 text-slate-600 text-xs py-2.5 px-3 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all font-medium">
                        <option value="todos">Todos los estados</option>
                        <option value="Production" selected>Producción</option>
                        <option value="Piloto testing">Pruebas piloto</option>
                        <option value="Testing">Pruebas</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <div class="relative w-full md:w-52 ml-0 md:ml-3 mt-3 md:mt-0">
                    <select wire:model="connectionType" class="w-full rounded-xl border border-slate-200 appearance-none bg-slate-50 text-slate-600 text-xs py-2.5 px-3 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 focus:bg-white transition-all font-medium">
                        <option value="todos">Todas las conexiones</option>
                        <option value="partner">Conexión Partner</option>
                        <option value="individual">Conexión Individual</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
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
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    ID
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    Compañía
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    NIT
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    Fase SIAT
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    Conexión
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    Administrador
                                </th>
                                <th scope="col" class="px-3 py-3 text-xs font-semibold tracking-wider text-left text-white uppercase">
                                    Creación
                                </th>
                                <th scope="col" class="relative px-3 py-3 text-center text-xs font-semibold uppercase text-white">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($companies as $company)
                            <tr class="hover:bg-slate-50 transition-colors {{ $company->email == 'root@emizor.com' ? 'bg-blue-50/40' : '' }}">
                                <td class="px-3 py-3 text-xs font-bold text-slate-900 whitespace-nowrap font-mono">
                                    #{{ $company->id }}
                                </td>
                                <td class="px-3 py-3 text-xs font-medium whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $rawLogo = trim($company->company_logo ?? '', '"');
                                            if($rawLogo && !str_starts_with($rawLogo, 'http')) {
                                                $rawLogo = url($rawLogo);
                                            }
                                        @endphp
                                        @if(!empty($rawLogo))
                                            <div class="w-6 h-6 rounded-md bg-white border border-slate-200 p-0.5 shadow-sm flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                <img src="{{ $rawLogo }}" alt="Logo" class="max-w-full max-h-full object-contain">
                                            </div>
                                        @else
                                            <div class="w-6 h-6 rounded-md bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-[10px] flex-shrink-0">
                                                {{ strtoupper(substr(trim($company->company_name ?? 'C','"'), 0, 1)) }}
                                            </div>
                                        @endif
                                        <a href="{{ route('admin.companies.profile', $company->id) }}" class="text-sky-600 hover:text-sky-800 font-bold hover:underline inline-flex items-center gap-1.5" title="Ver perfil de la compañía">
                                            {{ $company->company_name == "" ? "SIN NOMBRE" : trim($company->company_name,'"') }}
                                        </a>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-xs text-slate-700 whitespace-nowrap font-mono">
                                    {{ $company->company_id_number == "" ? "-" : trim($company->company_id_number,'"') }}
                                </td>
                                <td class="px-3 py-3 text-xs whitespace-nowrap">
                                    @php
                                        $statusClass = 'bg-slate-100 text-slate-600';
                                        if($company->phase == "Production") $statusClass = 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20';
                                        elseif($company->phase == "Testing") $statusClass = 'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20';
                                        elseif($company->phase == "Piloto testing") $statusClass = 'bg-sky-50 text-sky-700 ring-1 ring-sky-600/20';
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                        {{ $company->phase == "Production" ? "Producción" : ($company->phase=="Testing" ? "Pruebas" : "Piloto pruebas") }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-xs whitespace-nowrap">
                                    @if($company->is_b2b2b_partner == "true" || $company->is_b2b2b_partner == 1)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 ring-1 ring-purple-600/20">
                                            Partner
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 ring-1 ring-slate-500/20">
                                            Individual
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-xs whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($company->first_name, 0, 1)) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-medium text-slate-900 leading-tight">{{ $company->first_name . " ". $company->last_name }}</span>
                                            <span class="text-[11px] text-slate-400 leading-tight">{{ $company->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-xs whitespace-nowrap">
                                    @if($company->created_at)
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold text-slate-700">
                                                {{ \Carbon\Carbon::parse($company->created_at)->diffForHumans() }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                {{ \Carbon\Carbon::parse($company->created_at)->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                                
                                <td class="px-3 py-3 text-center whitespace-nowrap">
                                    <div class="relative inline-block text-left" x-data="{ isOpen: false }">
                                        <button @click="isOpen = !isOpen" 
                                                class="inline-flex items-center justify-center w-8 h-8 text-slate-500 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg border border-slate-200 transition-colors focus:outline-none shadow-sm" 
                                                type="button"
                                                title="Acciones de la compañía"> 
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>

                                        <div x-show="isOpen" 
                                             @click.away="isOpen = false" 
                                             style="display: none;" 
                                             class="absolute right-0 mt-2 bg-white border border-slate-200 rounded-xl shadow-2xl w-52 z-50 py-1.5 text-xs text-slate-700 divide-y divide-slate-100">
                                            <div class="py-1">
                                                <a href="{{ route('admin.companies.profile', $company->id) }}" 
                                                   class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-sky-50 hover:text-sky-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-sky-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                    <span>Revisar Cuenta</span>
                                                </a>
                                                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'invoices']) }}" 
                                                   class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors gap-2.5">
                                                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                    <span>Facturas</span>
                                                </a>
                                            </div>
                                            <div class="pt-1">
                                                <button type="button" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-purple-700 hover:bg-purple-50 transition-colors gap-2.5" 
                                                        onclick="Livewire.emit('openMigrateToPartnerModal', {{ $company->id }})">
                                                    <svg class="w-4 h-4 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                    <span>{{ ($company->is_b2b2b_partner == 'true' || $company->is_b2b2b_partner == 1) ? 'Actualizar Partner' : 'Migrar a Partner' }}</span>
                                                </button>
                                                <button type="button" 
                                                        class="flex items-center w-full px-4 py-2.5 text-xs font-medium text-slate-700 hover:bg-amber-50 hover:text-amber-700 transition-colors gap-2.5" 
                                                        onclick="Livewire.emit('openModal', 'piloto-up', [{{$company->id}}])">
                                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                                    <span>Pasar a pruebas piloto</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="px-6 py-8 text-center whitespace-nowrap text-xs text-slate-400" colspan="100%">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
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

    @if($companies->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex mt-5">
            <span class="flex space-x-2">
                {{-- Previous Page Link --}}
                @if ($companies->onFirstPage())
                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg text-slate-400">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button wire:click="previousPage" wire:loading.attr="disabled" rel="prev" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif
 
                {{-- Next Page Link --}}
                @if ($companies->hasMorePages())
                    <button wire:click="nextPage" wire:loading.attr="disabled" rel="next" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg text-slate-400">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </span>
        </nav>
    @endif
</div>