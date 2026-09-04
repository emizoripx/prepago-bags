<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
        <h3 class="text-base font-bold text-slate-900">Documentos Sectoriales</h3>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button wire:click="syncDocumentSectors" wire:loading.attr="disabled" 
                class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl transition-colors bg-sky-50 text-sky-600 hover:bg-sky-100 border border-sky-200/50 shadow-sm cursor-pointer whitespace-nowrap">
                <svg wire:loading.remove wire:target="syncDocumentSectors" class="w-3.5 h-3.5" style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <svg wire:loading wire:target="syncDocumentSectors" class="w-3.5 h-3.5 animate-spin" style="width: 14px; height: 14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span wire:loading.remove wire:target="syncDocumentSectors">Sincronizar Documentos Sector</span>
                <span wire:loading wire:target="syncDocumentSectors">Sincronizando...</span>
            </button>
            <div class="w-full sm:w-64 relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" style="width: 14px; height: 14px;" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input wire:model.debounce.500ms="search" type="search" class="block w-full py-2 pl-9 pr-8 text-xs text-slate-900 border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors" placeholder="Buscar documento...">
                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-sky-600 animate-spin" style="width: 14px; height: 14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Código</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nombre Documento Sector</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Plantilla PDF</th>
                    @if(!$company->is_postpago)
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Disponibles</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Vencimiento Bolsa</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Última Recarga</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Emitidas</th>
                    @else
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Límite Postpago</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Emitidas</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-white uppercase tracking-wider">Excedentes</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($document_sectors as $di)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3 whitespace-nowrap text-xs font-bold text-slate-900 font-mono">
                        #{{ $di->fel_doc_sector_id }}
                    </td>
                    <td class="px-4 py-3 text-xs text-slate-800 font-medium">
                        {{ $arrayNames[$di->fel_doc_sector_id] ?? ('Documento Sector ' . $di->fel_doc_sector_id) }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs">
                        @if(isset($assignedTemplates[$di->fel_doc_sector_id]))
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700">
                                {{ $assignedTemplates[$di->fel_doc_sector_id] }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-500">
                                Plantilla Predeterminada
                            </span>
                        @endif
                    </td>
                    @if(!$company->is_postpago)
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-bold {{ ($di->invoice_number_available > 0) ? 'text-sky-600' : 'text-rose-600' }}">
                            {{ $di->invoice_number_available }} facturas
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-500 font-mono">
                            {{ !empty($di->duedate) ? \Carbon\Carbon::parse($di->duedate)->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-slate-500 font-mono">
                            {{ $prepago_bags_payments[$di->fel_doc_sector_id] ?? '--' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-slate-800 text-right">
                            {{ $di->counter }}
                        </td>
                    @else
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-bold text-sky-600">
                            {{ $di->postpago_limit ?? 'Ilimitado' }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-semibold text-slate-800">
                            {{ $di->postpago_counter ?? 0 }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-bold text-right {{ ($di->postpago_exceded_limit > 0) ? 'text-rose-600' : 'text-slate-400' }}">
                            {{ $di->postpago_exceded_limit ?? 0 }}
                        </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-sm font-medium text-slate-600">No se encontraron documentos sectoriales</p>
                            <p class="text-xs text-slate-400 mt-1">Haz clic en "Sincronizar Documentos Sector" para actualizar desde el SIAT.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
