<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
        <h3 class="text-base font-bold text-slate-900">Sucursales</h3>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button wire:click="syncBranches" wire:loading.attr="disabled" 
                class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl transition-colors bg-sky-50 text-sky-600 hover:bg-sky-100 border border-sky-200/50 shadow-sm">
                <svg wire:loading.remove wire:target="syncBranches" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <svg wire:loading wire:target="syncBranches" class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove wire:target="syncBranches">Sincronizar Sucursales</span>
                <span wire:loading wire:target="syncBranches">Sincronizando...</span>
            </button>
            <div class="w-full sm:w-64 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input wire:model.debounce.500ms="search" type="search" class="block w-full py-2 pl-9 pr-8 text-xs text-slate-900 border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors" placeholder="Buscar sucursal...">
                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-sky-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    @if(session()->has('sync_message'))
        <div class="px-5 py-2.5 bg-emerald-50 text-emerald-600 text-xs font-semibold border-b border-emerald-100 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            {{ session('sync_message') }}
        </div>
    @endif
    @if(session()->has('sync_error'))
        <div class="px-5 py-2.5 bg-rose-50 text-rose-600 text-xs font-semibold border-b border-rose-100 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('sync_error') }}
        </div>
    @endif
    <div class="relative overflow-x-auto min-h-[300px]">
        @include('prepagobags::components.shared.table-spinner')
        <table class="min-w-full divide-y divide-slate-200 transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none">
            <thead class="bg-sky-600">
                <tr>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Código</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Descripción</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Zona</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Teléfono</th>
                    <th scope="col" class="px-3 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider">Puntos de Venta (POS)</th>
                    <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Acción</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($branches as $branch)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 whitespace-nowrap text-xs font-bold text-slate-900 font-mono">
                        {{ $branch->codigo == 0 ? 'Casa Matriz (0)' : 'Sucursal ' . $branch->codigo }}
                    </td>
                    <td class="px-3 py-3 text-xs text-slate-700 font-medium">
                        {{ $branch->descripcion }}
                    </td>
                    <td class="px-3 py-3 text-xs text-slate-600">
                        {{ $branch->zona ?? '-' }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600 font-mono">
                        {{ $branch->telefono ?? '-' }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-center text-xs">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ ($branch->pos_count ?? 0) > 0 ? 'bg-sky-100 text-sky-800' : 'bg-slate-100 text-slate-500' }}">
                            {{ $branch->pos_count ?? 0 }} {{ ($branch->pos_count ?? 0) === 1 ? 'POS' : 'POS' }}
                        </span>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-right text-xs">
                        <button wire:click="openPosModal({{ $branch->id }})" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors shadow-sm" title="Ver Puntos de Venta">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Ver POS
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400 bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p>No se encontraron sucursales.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($branches->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 flex justify-end">
        <nav role="navigation" aria-label="Pagination Navigation" class="flex space-x-2">
            @if ($branches->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg text-slate-400">
                    Anterior
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    Anterior
                </button>
            @endif

            @if ($branches->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    Siguiente
                </button>
            @else
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg text-slate-400">
                    Siguiente
                </span>
            @endif
        </nav>
    </div>
    @endif

    {{-- Modal Puntos de Venta (POS) --}}
    @if($showPosModal && $selectedBranch)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-y-auto overflow-x-hidden bg-slate-900/60 backdrop-blur-sm"
         style="margin: 0; top: 0; left: 0; right: 0; bottom: 0;">
        
        <!-- Modal Card -->
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden z-10 my-8">
            <div class="bg-gradient-to-r from-sky-600 to-sky-700 px-6 py-4 flex justify-between items-center text-white">
                <div>
                    <h3 class="text-base font-bold flex items-center gap-2" id="modal-title">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Puntos de Venta (POS)
                    </h3>
                    <p class="text-xs text-sky-100 mt-0.5">
                        {{ $selectedBranch->codigo == 0 ? 'Casa Matriz (Código 0)' : 'Sucursal ' . $selectedBranch->codigo }} - {{ $selectedBranch->descripcion }}
                    </p>
                </div>
                <button wire:click="closePosModal" type="button" class="text-white/80 hover:text-white p-1.5 rounded-lg hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <div class="overflow-x-auto border border-slate-200 rounded-xl">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold text-slate-600 uppercase">Código POS</th>
                                <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold text-slate-600 uppercase">Descripción</th>
                                <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold text-slate-600 uppercase">Tipo</th>
                                <th scope="col" class="px-4 py-2.5 text-left text-xs font-semibold text-slate-600 uppercase">Datos Adicionales</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-100">
                            @forelse($branchPosList as $pos)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap text-xs font-bold text-slate-900 font-mono">
                                    {{ $pos->codigo }}
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-800 font-medium">
                                    {{ $pos->descripcion ?: 'Punto de Venta ' . $pos->codigo }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-600">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                        {{ $pos->tipoPos ? 'Tipo: ' . $pos->tipoPos : 'Estándar' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500 font-mono">
                                    @if($pos->numeroContrato)
                                        <span class="block">Contrato: {{ $pos->numeroContrato }}</span>
                                    @endif
                                    @if($pos->nitComisionista)
                                        <span class="block">Comisionista: {{ $pos->nitComisionista }}</span>
                                    @endif
                                    @if(!$pos->numeroContrato && !$pos->nitComisionista)
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-xs text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-7 h-7 text-slate-300 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <p>No se encontraron puntos de venta registrados para esta sucursal.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-slate-50 px-6 py-3 border-t border-slate-200 flex justify-end">
                <button wire:click="closePosModal" type="button" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition shadow-sm">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
