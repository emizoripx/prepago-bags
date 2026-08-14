<div x-data="{ isOpen: @entangle('isOpen') }" 
     x-show="isOpen" 
     style="display: none;"
     class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto overflow-x-hidden bg-slate-900/50 backdrop-blur-sm"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="relative w-full max-w-3xl p-4 md:h-auto" 
         @click.away="$wire.closeModal()"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
        
        <!-- Modal content -->
        <div class="relative bg-white rounded-2xl shadow-xl border border-slate-200">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-5 border-b border-slate-200 rounded-t-2xl">
                <div>
                    <h3 class="text-xl font-semibold text-slate-900">
                        Editar Factura {{ $invoice ? '#' . ($invoice->fel_invoice->numeroFactura ?: $invoice->number) : '' }}
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">
                        Modifica notas, términos y datos adicionales (evento) de la factura.
                    </p>
                </div>
                <button type="button" wire:click="closeModal" class="text-slate-400 bg-transparent hover:bg-slate-100 hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
            
            <!-- Modal body -->
            <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
                
                @if($invoice)
                <!-- Información Referencial (Solo Lectura) -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="block text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Razón Social</span>
                            <span class="block text-sm font-medium text-slate-800">{{ $invoice->fel_invoice->nombreRazonSocial ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Número Documento</span>
                            <span class="block text-sm font-medium text-slate-800">{{ $invoice->fel_invoice->numeroDocumento ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-blue-600 uppercase tracking-wider mb-1">Fecha de Emisión</span>
                            <span class="block text-sm font-medium text-slate-800">{{ $invoice->fel_invoice ? \Carbon\Carbon::parse($invoice->fel_invoice->fechaEmision)->format('d/m/Y H:i') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Información General -->
                <div class="space-y-4">
                    <h4 class="text-md font-medium text-slate-900 border-b pb-2">Información General (Editable)</h4>
                    
                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">Términos de Facturación</label>
                        <textarea wire:model.defer="terms" rows="3" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 transition-colors"></textarea>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">Notas Adicionales (Públicas)</label>
                        <textarea wire:model.defer="public_notes" rows="3" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 transition-colors"></textarea>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-slate-700">Notas Privadas</label>
                        <textarea wire:model.defer="private_notes" rows="3" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2.5 transition-colors"></textarea>
                    </div>
                </div>

                <!-- Datos Adicionales (Extras) -->
                <div class="space-y-4 mt-6">
                    <div class="flex justify-between items-center border-b pb-2">
                        <h4 class="text-md font-medium text-slate-900">Datos Adicionales (Extras / Autorización / Eventos)</h4>
                    </div>
                    
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-4 space-y-3">
                        @foreach($extras as $index => $extra)
                            <div class="grid grid-cols-12 gap-3 items-center">
                                <div class="col-span-5 md:col-span-4">
                                    <input type="text" wire:model.defer="extras.{{ $index }}.key" placeholder="Clave (Ej. evento)" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2">
                                </div>
                                <div class="col-span-5 md:col-span-7">
                                    <input type="text" wire:model.defer="extras.{{ $index }}.value" placeholder="Valor" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2">
                                </div>
                                <div class="col-span-2 md:col-span-1 text-right">
                                    <button type="button" wire:click="removeExtra({{ $index }})" class="p-2 text-red-500 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors inline-block">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <div class="grid grid-cols-12 gap-3 items-center pt-2 mt-2 border-t border-slate-200 border-dashed">
                            <div class="col-span-5 md:col-span-4">
                                <input type="text" wire:model.defer="new_extra_key" placeholder="Nueva clave (Ej. evento)" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2">
                            </div>
                            <div class="col-span-4 md:col-span-6">
                                <input type="text" wire:model.defer="new_extra_value" placeholder="Nuevo valor" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2">
                            </div>
                            <div class="col-span-3 md:col-span-2">
                                <button type="button" wire:click="addExtra" class="w-full px-2 md:px-4 py-2 text-sm font-medium text-emerald-700 bg-emerald-100 hover:bg-emerald-200 rounded-lg transition-colors whitespace-nowrap text-center">
                                    Agregar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                    <div class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500"></div>
                    </div>
                @endif

            </div>
            
            <!-- Modal footer -->
            <div class="flex items-center justify-end p-5 border-t border-slate-200 rounded-b-2xl bg-slate-50 space-x-3">
                <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 hover:text-slate-900 focus:ring-4 focus:outline-none focus:ring-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="button" wire:click="save" class="px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:ring-4 focus:outline-none focus:ring-emerald-300 transition-colors shadow-sm">
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
