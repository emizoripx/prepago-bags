<div class="bg-white border border-slate-200 rounded-xl shadow-sm sm:p-6 xl:p-8">
    <h3 class="mb-4 text-xl font-semibold text-slate-900">Configuración de Generadores de Facturas</h3>

    <div>
        @if (session()->has('message'))
            <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 dark:bg-gray-800 dark:text-emerald-400" role="alert">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <form wire:submit.prevent="saveSettings">
        <div class="mb-6">
            <label for="level_invoice_number_generation" class="block mb-2 text-sm font-medium text-slate-600">Nivel de Generación de Número de Factura</label>
            <select wire:model="level_invoice_number_generation" id="level_invoice_number_generation" class="bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 block w-full p-2.5 transition-all">
                <option value="0">Nivel Compañia</option>
                <option value="1">Nivel Sucursal</option>
                <option value="2">Nivel Punto de Venta</option>
                <option value="3">Nivel Sector Documento</option>
            </select>
        </div>

        <div class="mt-6">
            <h4 class="text-lg font-medium text-slate-900 mb-4">Contadores Actuales</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($invoice_generators as $code => $counter)
                <div class="mb-4" wire:key="generator-{{ $code }}">
                    <label class="block mb-2 text-sm font-medium text-slate-600">
                        Código {{ $code }} 
                        @php
                            $parts = explode('-', $code);
                            if(count($parts) == 3 && isset($sector_documents[$parts[2]])) {
                                echo "- " . $sector_documents[$parts[2]];
                            }
                        @endphp
                    </label>
                    <input type="number" wire:model="invoice_generators.{{ $code }}" class="bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-lg focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 block w-full p-2.5 transition-all">
                    @error('invoice_generators.'.$code) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center transition-colors">Guardar Configuración</button>
        </div>
    </form>
</div>
