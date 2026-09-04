<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
        <h3 class="text-base font-bold text-slate-900">Productos de la Compañía</h3>
        <div class="w-full sm:w-64">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-3.5 h-3.5 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input wire:model.debounce.500ms="search" type="search" class="block w-full py-2 pl-9 pr-8 text-xs text-slate-900 border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-colors" placeholder="Buscar por código o nombre...">
            </div>
        </div>
    </div>
    <div class="relative overflow-x-auto min-h-[300px]">
        @include('prepagobags::components.shared.table-spinner')
        <table class="min-w-full divide-y divide-slate-200 transition-opacity duration-200" wire:loading.class="opacity-50 pointer-events-none">
            <thead class="bg-sky-600">
                <tr>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Código</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Nombre del Producto</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Costo</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Precio</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Estado SIAT</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Creado En</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($products as $product)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 whitespace-nowrap text-xs font-mono font-bold text-slate-900">
                        {{ $product->product_key }}
                    </td>
                    <td class="px-3 py-3 text-xs text-slate-700 font-medium">
                        {{ $product->notes }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600 font-mono">
                        {{ number_format($product->cost, 2) }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs font-bold text-slate-900 font-mono">
                        {{ number_format($product->price, 2) }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs">
                        @if(!empty($product->codigo_producto_sin))
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20">
                                Homologado
                            </span>
                            <div class="mt-1 text-[11px] text-slate-400 font-normal space-y-0.5">
                                <div><span class="font-medium text-slate-500">Cód. SIN:</span> <span class="font-mono text-slate-700">{{ $product->codigo_producto_sin }}</span></div>
                                @if(!empty($product->codigo_actividad_economica))
                                    <div><span class="font-medium text-slate-500">Actividad:</span> <span class="font-mono text-slate-700">{{ $product->codigo_actividad_economica }}</span></div>
                                @endif
                                <div><span class="font-medium text-slate-500">Unidad:</span> {{ $product->nombre_unidad ?: ($product->codigo_unidad ?: 'UNIDAD') }}</div>
                            </div>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                Sin homologar
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-400 font-mono">
                        {{ $product->created_at ? \Carbon\Carbon::parse($product->created_at)->format("d/m/Y H:i") : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400 bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            <p>No se encontraron productos.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="px-4 py-3 border-t border-slate-200 flex justify-end">
        <nav role="navigation" aria-label="Pagination Navigation" class="flex space-x-2">
            @if ($products->onFirstPage())
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-slate-50 border border-slate-200 cursor-not-allowed rounded-lg text-slate-400">
                    Anterior
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-white border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-sky-600 transition shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-600/20">
                    Anterior
                </button>
            @endif

            @if ($products->hasMorePages())
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
