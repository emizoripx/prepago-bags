<div x-data="{ isOpen: @entangle('open') }" 
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
        
        <div class="relative bg-white rounded-2xl shadow-xl border border-slate-200">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-5 border-b border-slate-200 rounded-t-2xl">
                <h3 class="text-xl font-semibold text-slate-900" id="modal-title">
                    Crear y Provisionar Nueva Compañía (Partner API)
                </h3>
                <button type="button" wire:click="closeModal" class="text-slate-400 bg-transparent hover:bg-slate-100 hover:text-slate-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>

            @if ($errors->has('general'))
                <div class="p-3 m-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    {{ $errors->first('general') }}
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Datos de la Compañía -->
                        <div class="col-span-1 md:col-span-2">
                            <h4 class="font-medium text-slate-700 mb-2 border-b pb-1">Datos de la Empresa</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Razón Social</label>
                            <input type="text" wire:model.defer="company_name" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                            @error('company_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">NIT</label>
                            <input type="text" wire:model.defer="id_number" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                            @error('id_number') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Datos del Usuario Administrador -->
                        <div class="col-span-1 md:col-span-2 mt-2">
                            <h4 class="font-medium text-slate-700 mb-2 border-b pb-1">Usuario Administrador</h4>
                            <p class="text-xs text-slate-500 mb-2">Si el email ya existe en la plataforma, la empresa se asociará a esa cuenta.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Email</label>
                            <input type="email" wire:model.defer="email" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                            @error('email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Contraseña (Opcional si existe)</label>
                            <input type="password" wire:model.defer="password" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                            @error('password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Nombres</label>
                            <input type="text" wire:model.defer="first_name" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                            @error('first_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Apellidos</label>
                            <input type="text" wire:model.defer="last_name" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                            @error('last_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        <!-- Configuración Partner API -->
                        <div class="col-span-1 md:col-span-2 mt-2">
                            <h4 class="font-medium text-slate-700 mb-2 border-b pb-1">Configuración Partner API</h4>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700">Fase de Creación</label>
                            <select wire:model="phase" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                                <option value="testing">Testing (Credenciales de Prueba)</option>
                                <option value="piloto">Pruebas Piloto</option>
                                <option value="production">Producción</option>
                            </select>
                            @error('phase') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                        </div>

                        @if(in_array($phase, ['piloto', 'production']))
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Modalidad de Facturación</label>
                                <select wire:model="billing_type" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                                    <option value="computarizada">Computarizada en Línea</option>
                                    <option value="electronica">Electrónica en Línea</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Tipo de Documento</label>
                                <select wire:model.defer="document_type" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                                    <option value="5">NIT (5)</option>
                                    <option value="1">CI (1)</option>
                                    <option value="2">CEX (2)</option>
                                    <option value="3">PAS (3)</option>
                                    <option value="4">OD (4)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700">Código de Sistema (Partner)</label>
                                <input type="text" wire:model.defer="system_code" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5">
                                @error('system_code') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="flex items-center mt-6">
                                <input id="is_unipersonal" type="checkbox" wire:model.defer="is_unipersonal" class="w-4 h-4 text-sky-600 bg-gray-100 border-gray-300 rounded focus:ring-sky-500">
                                <label for="is_unipersonal" class="ml-2 text-sm font-medium text-gray-900">
                                    Es Empresa Unipersonal
                                </label>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700">Token de Impuestos Delegado</label>
                                <textarea wire:model.defer="sin_token" rows="2" class="bg-slate-50 border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2.5"></textarea>
                                @error('sin_token') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            @if($billing_type == 'electronica')
                                <div class="col-span-1 md:col-span-2 bg-slate-50 p-4 rounded-xl border border-slate-200 mt-2">
                                    <h5 class="text-sm font-semibold text-slate-800 mb-3">Firma Digital (Obligatoria para Electrónica)</h5>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-slate-700 mb-1">Certificado .p12</label>
                                            <input type="file" wire:model="certificate_file" accept=".p12" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                                            @error('certificate_file') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-slate-700 mb-1">Contraseña del Certificado</label>
                                            <input type="password" wire:model.defer="certificate_password" class="bg-white border border-slate-300 text-slate-900 text-sm rounded-lg focus:ring-sky-500 focus:border-sky-500 block w-full p-2">
                                            @error('certificate_password') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end p-5 border-t border-slate-200 rounded-b-2xl bg-slate-50 space-x-3">
                    <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:ring-4 focus:outline-none focus:ring-slate-200 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 text-sm font-medium text-white bg-sky-600 rounded-lg hover:bg-sky-700 focus:ring-4 focus:outline-none focus:ring-sky-300 transition-colors shadow-sm disabled:opacity-50">
                        <span wire:loading.remove wire:target="save">Crear Compañía</span>
                        <span wire:loading wire:target="save">Creando...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
