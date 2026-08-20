<div x-data="{ open: @entangle('isOpen'), isDropping: false }" 
     x-show="open" 
     x-cloak
     style="display: none;"
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">

    <!-- Backdrop with blur -->
    <div class="fixed inset-0 bg-slate-900 bg-opacity-60 backdrop-blur-sm transition-opacity" 
         @click="open = false; $wire.closeModal()"></div>

    <!-- Modal Container -->
    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200"
             @click.stop>
            
            <!-- Header -->
            <div class="px-6 py-4 bg-gradient-to-r from-sky-600 to-blue-700 text-white flex items-center justify-between"
                 style="background: linear-gradient(135deg, #0284c7, #1d4ed8);">
                <div class="flex items-center gap-3" style="display: flex; align-items: center; gap: 0.75rem;">
                    <div class="p-2 bg-white/10 rounded-xl" style="padding: 0.5rem; background: rgba(255,255,255,0.15); border-radius: 0.75rem;">
                        <svg class="w-5 h-5 text-white" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold leading-tight text-white m-0" style="font-size: 1rem; font-weight: 700; color: #ffffff; margin: 0;">Logo de la Empresa</h3>
                        <p class="text-xs text-sky-100 mt-0.5 m-0" style="font-size: 0.75rem; color: #e0f2fe; margin: 0;">{{ $company->settings->name ?? 'Compañía' }}</p>
                    </div>
                </div>
                <button wire:click="closeModal" type="button" class="text-white/70 hover:text-white transition-colors p-1.5 rounded-lg hover:bg-white/10" style="background: transparent; border: none; cursor: pointer;">
                    <svg class="w-5 h-5 text-white" style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body Form -->
            <form wire:submit.prevent="save" class="p-6 space-y-5" style="padding: 1.5rem;">
                
                <!-- Current Logo Status -->
                <div class="flex items-center gap-4 p-4 rounded-xl border border-slate-200 bg-slate-50"
                     style="display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; margin-bottom: 1.25rem;">
                    <div class="flex-shrink-0" style="flex-shrink: 0;">
                        @if($logo)
                            <div style="width: 72px; height: 72px; background: #ffffff; border: 2px solid #0284c7; border-radius: 0.75rem; padding: 0.25rem; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <img src="{{ $logo->temporaryUrl() }}" alt="Previsualización" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                        @elseif(!empty($current_logo_url))
                            <div style="width: 72px; height: 72px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 0.75rem; padding: 0.25rem; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                <img src="{{ $current_logo_url }}" alt="Logo actual" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                        @else
                            <div style="width: 72px; height: 72px; background: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 0.75rem; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8;">
                                <svg style="width: 28px; height: 28px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span style="font-size: 9px; font-weight: 600; margin-top: 2px;">SIN LOGO</span>
                            </div>
                        @endif
                    </div>

                    <div style="flex: 1; min-width: 0;">
                        <h4 style="font-size: 0.8125rem; font-weight: 700; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.025em;">
                            {{ $logo ? 'Nueva imagen seleccionada' : (!empty($current_logo_url) ? 'Logo Actual Configurado' : 'Sin logo registrado') }}
                        </h4>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0 0; line-height: 1.4;">
                            Aparecerá en el encabezado de las facturas (PDF), portal de clientes y correos.
                        </p>
                        @if(!empty($current_logo_url) && !$logo)
                            <button type="button" wire:click="deleteLogo" 
                                    onclick="confirm('¿Estás seguro de eliminar el logo actual de esta compañía?') || event.stopImmediatePropagation()"
                                    style="background: none; border: none; color: #e11d48; font-size: 0.75rem; font-weight: 600; cursor: pointer; padding: 0.25rem 0 0 0; display: inline-flex; align-items: center; gap: 0.25rem;">
                                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Eliminar logo actual</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Interactive Dropzone -->
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #334155; margin-bottom: 0.5rem;">
                        Subir Imagen (Dropzone)
                    </label>

                    <div @dragover.prevent="isDropping = true"
                         @dragleave.prevent="isDropping = false"
                         @drop.prevent="isDropping = false"
                         :class="{ 'border-sky-500 bg-sky-50': isDropping, 'border-slate-300 bg-slate-50': !isDropping }"
                         style="position: relative; border: 2px dashed #cbd5e1; border-radius: 1rem; padding: 2rem 1.5rem; text-align: center; cursor: pointer; transition: all 0.2s ease;">
                        
                        <input type="file" 
                               wire:model="logo" 
                               id="company_logo_dropzone" 
                               accept="image/png, image/jpeg, image/jpg, image/gif, image/webp, image/svg+xml" 
                               style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10;">
                        
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; pointer-events: none;">
                            <div style="width: 48px; height: 48px; border-radius: 50%; background: #e0f2fe; display: flex; align-items: center; justify-content: center; color: #0284c7; margin-bottom: 0.75rem;">
                                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #0f172a; margin: 0;">
                                Arrastra y suelta el logo aquí
                            </p>
                            <p style="font-size: 0.75rem; color: #0284c7; font-weight: 500; margin: 0.25rem 0 0 0;">
                                o haz clic para explorar archivos
                            </p>
                            <span style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.5rem;">
                                Formatos admitidos: PNG, JPG, SVG, WEBP (Máx. 5MB)
                            </span>
                        </div>
                    </div>

                    <!-- Uploading Progress / Spinner -->
                    <div wire:loading wire:target="logo" style="margin-top: 0.75rem; text-align: center; color: #0284c7; font-size: 0.8125rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <svg class="animate-spin" style="width: 16px; height: 16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Cargando previsualización...</span>
                    </div>

                    @error('logo')
                        <p style="color: #e11d48; font-size: 0.75rem; font-weight: 600; margin-top: 0.5rem; text-align: left;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Footer Actions -->
                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 1rem; margin-top: 1.5rem;">
                    <button type="button" wire:click="closeModal" 
                            style="padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 600; color: #475569; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 0.75rem; cursor: pointer; transition: background 0.15s;">
                        Cancelar
                    </button>
                    <button type="submit" wire:loading.attr="disabled" 
                            style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1.25rem; font-size: 0.75rem; font-weight: 600; color: #ffffff; background: #0284c7; border: none; border-radius: 0.75rem; cursor: pointer; box-shadow: 0 2px 4px rgba(2,132,199,0.25); transition: background 0.15s;">
                        <svg wire:loading wire:target="save" class="animate-spin" style="width: 14px; height: 14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Guardar Logo</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
