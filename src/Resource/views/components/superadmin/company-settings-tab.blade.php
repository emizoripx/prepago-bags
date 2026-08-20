<div class="bg-white border border-slate-200 rounded-xl shadow-sm p-5 sm:p-6">
    <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Configuración de Generadores de Facturas</h3>

    <div>
        @if (session()->has('message'))
            <div class="p-3 mb-4 text-xs text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center" role="alert">
                <svg class="w-4 h-4 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                {{ session('message') }}
            </div>
        @endif
    </div>

    <form wire:submit.prevent="saveSettings" class="space-y-6">
        <div class="max-w-md">
            <label for="level_invoice_number_generation" class="block mb-1.5 text-xs font-semibold text-slate-700">Nivel de Generación de Número de Factura</label>
            <select wire:model="level_invoice_number_generation" id="level_invoice_number_generation" class="bg-slate-50 border border-slate-200 text-slate-800 text-xs rounded-xl focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 block w-full py-2.5 px-3 transition-all font-medium">
                <option value="0">Nivel Compañia</option>
                <option value="1">Nivel Sucursal</option>
                <option value="2">Nivel Punto de Venta</option>
                <option value="3">Nivel Sector Documento</option>
            </select>
        </div>

        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 border-b border-slate-100 pb-1">Contadores Actuales</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($invoice_generators as $code => $counter)
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl" wire:key="generator-{{ $code }}">
                    <label class="block mb-1 text-xs font-semibold text-slate-700">
                        Código {{ $code }} 
                        @php
                            $parts = explode('-', $code);
                            if(count($parts) == 3 && isset($sector_documents[$parts[2]])) {
                                echo "<span class='text-slate-500 font-normal'>(" . $sector_documents[$parts[2]] . ")</span>";
                            }
                        @endphp
                    </label>
                    <input type="number" wire:model="invoice_generators.{{ $code }}" class="bg-white border border-slate-200 text-slate-900 font-mono text-xs rounded-lg focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 block w-full py-1.5 px-3 transition-all font-semibold">
                    @error('invoice_generators.'.$code) <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span> @enderror
                </div>
                @endforeach
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-xl shadow-sm transition-colors">
                Guardar Configuración
            </button>
        </div>
    </form>
</div>
