<div class="bg-gray-800 rounded-lg shadow sm:p-6 xl:p-8">
    <h3 class="mb-4 text-xl font-bold text-white">Configuración de Generadores de Facturas</h3>

    <div>
        @if (session()->has('message'))
            <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 dark:bg-gray-800 dark:text-emerald-400" role="alert">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <form wire:submit.prevent="saveSettings">
        <div class="mb-6">
            <label for="level_invoice_number_generation" class="block mb-2 text-sm font-medium text-gray-300">Nivel de Generación de Número de Factura</label>
            <select wire:model="level_invoice_number_generation" id="level_invoice_number_generation" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                <option value="0">Nivel Compañia</option>
                <option value="1">Nivel Sucursal</option>
                <option value="2">Nivel Punto de Venta</option>
                <option value="3">Nivel Sector Documento</option>
            </select>
        </div>

        <div class="mt-6">
            <h4 class="text-lg font-medium text-white mb-4">Contadores Actuales</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($invoice_generators as $code => $counter)
                <div class="mb-4" wire:key="generator-{{ $code }}">
                    <label class="block mb-2 text-sm font-medium text-gray-300">
                        Código {{ $code }} 
                        @php
                            $parts = explode('-', $code);
                            if(count($parts) == 3 && isset($sector_documents[$parts[2]])) {
                                echo "- " . $sector_documents[$parts[2]];
                            }
                        @endphp
                    </label>
                    <input type="number" wire:model="invoice_generators.{{ $code }}" class="bg-gray-700 border border-gray-600 text-white text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5">
                    @error('invoice_generators.'.$code) <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="text-white bg-emerald-600 hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Guardar Configuración</button>
        </div>
    </form>
</div>
