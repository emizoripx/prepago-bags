<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
        <h3 class="text-base font-bold text-slate-900">Plantillas (Templates)</h3>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button wire:click="syncTemplates" wire:loading.attr="disabled" 
                class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl transition-colors bg-sky-50 text-sky-600 hover:bg-sky-100 border border-sky-200/50 shadow-sm">
                <svg wire:loading.remove wire:target="syncTemplates" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <svg wire:loading wire:target="syncTemplates" class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove wire:target="syncTemplates">Sincronizar Plantillas</span>
                <span wire:loading wire:target="syncTemplates">Sincronizando...</span>
            </button>
            <div class="w-full sm:w-64 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input wire:model.debounce.500ms="search" type="search" class="block w-full py-2 pl-9 pr-8 text-xs text-slate-900 border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors" placeholder="Buscar plantilla...">
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
    @if($errors->has('download'))
    <div class="px-5 py-2.5 bg-red-50 text-red-600 text-xs border-b border-red-100 flex items-center">
        <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        {{ $errors->first('download') }}
    </div>
    @endif
    
    <div class="relative overflow-x-auto min-h-[300px]">
        @include('prepagobags::components.shared.table-spinner')
        <table class="min-w-full divide-y divide-slate-200 transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none">
            <thead class="bg-sky-600">
                <tr>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nombre</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Sector Doc.</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Sucursal</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Header / Footer</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Archivo Blade</th>
                    <th scope="col" class="px-3 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Acción</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($templates as $template)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 whitespace-nowrap text-xs font-semibold text-slate-900">
                        {{ $template->display_name }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600 font-mono">
                        {{ $template->document_sector_code }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600">
                        Suc. {{ $template->branch_code }} (PV: {{ $template->pos_code }})
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs">
                        <div class="flex space-x-1.5">
                            @if($template->header_custom)
                                <span class="px-1.5 py-0.2 inline-flex text-[10px] font-semibold rounded bg-emerald-50 text-emerald-700">Header: Sí</span>
                            @else
                                <span class="px-1.5 py-0.2 inline-flex text-[10px] font-semibold rounded bg-slate-100 text-slate-500">Header: No</span>
                            @endif
                            
                            @if($template->footer_custom)
                                <span class="px-1.5 py-0.2 inline-flex text-[10px] font-semibold rounded bg-sky-50 text-sky-700">Footer: Sí</span>
                            @else
                                <span class="px-1.5 py-0.2 inline-flex text-[10px] font-semibold rounded bg-slate-100 text-slate-500">Footer: No</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-3 py-3 text-xs text-slate-500 font-mono max-w-xs truncate" title="{{ $template->blade_resource }}">
                        {{ $template->blade_resource }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-right text-xs">
                        <button wire:click="downloadTemplate({{ $template->id }})" class="inline-flex items-center px-2.5 py-1 text-xs font-medium text-sky-700 bg-sky-50 hover:bg-sky-100 rounded-lg transition-colors shadow-sm" title="Descargar archivo Blade">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Descargar
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400 bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p>No se encontraron plantillas.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($templates->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 flex justify-end">
        <nav role="navigation" aria-label="Pagination Navigation" class="flex space-x-2">
            @if ($templates->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg text-slate-400">
                    Anterior
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    Anterior
                </button>
            @endif

            @if ($templates->hasMorePages())
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
</div>
