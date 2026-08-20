<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
        <h3 class="text-base font-bold text-slate-900">Clientes de la Compañía</h3>
        <div class="w-full sm:w-64">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input wire:model.debounce.500ms="search" type="search" class="block w-full py-2 pl-9 pr-8 text-xs text-slate-900 border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors" placeholder="Buscar por nombre o NIT...">
                <div wire:loading class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-sky-600 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <div class="relative overflow-x-auto min-h-[300px]">
        @include('prepagobags::components.shared.table-spinner')
        <table class="min-w-full divide-y divide-slate-200 transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none">
            <thead class="bg-sky-600">
                <tr>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nombre del Cliente</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Tipo Doc.</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">NIT / CI</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Balance</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Creado En</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($clients as $client)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 whitespace-nowrap text-xs font-semibold text-slate-900">
                        {{ $client->name }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-500">
                        {{ $client->fel_client ? $client->fel_client->getFieldTypeDocument() : '-' }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs font-mono text-slate-700">
                        {{ $client->id_number }} {{ $client->codigo_sucursal ? '- ' . $client->codigo_sucursal : '' }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs font-bold text-slate-900">
                        Bs. {{ number_format($client->balance, 2) }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-500 font-mono">
                        {{ $client->created_at ? \Carbon\Carbon::parse($client->created_at)->format("d/m/Y H:i") : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400 bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            <p>No se encontraron clientes.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clients->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 flex justify-end">
        <nav role="navigation" aria-label="Pagination Navigation" class="flex space-x-2">
            @if ($clients->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg text-slate-400">
                    Anterior
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    Anterior
                </button>
            @endif

            @if ($clients->hasMorePages())
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
