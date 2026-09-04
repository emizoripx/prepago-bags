<div x-data="{ open: @entangle('open') }"
     x-show="open"
     x-cloak
     style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; overflow-y: auto;">

    <!-- Backdrop -->
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); transition: opacity 0.2s ease;" 
         wire:click="closeModal"></div>

    <!-- Modal Container -->
    <div style="display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 1.5rem; position: relative; z-index: 10000;">
        
        <div style="position: relative; width: 100%; max-width: 36rem; background: #ffffff; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; text-align: left;"
             @click.stop>
            
            <!-- Header -->
            <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #0284c7, #0369a1); color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="padding: 0.5rem; background: rgba(255, 255, 255, 0.18); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 22px; height: 22px; color: #ffffff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 700; color: #ffffff; margin: 0; line-height: 1.2;">
                            Credenciales de Conexión FEL
                        </h3>
                        <p style="font-size: 0.75rem; color: #e0f2fe; margin: 0.25rem 0 0 0;">
                            {{ $company_name }} <span style="opacity: 0.8;">(ID #{{ $company_id }})</span>
                        </p>
                    </div>
                </div>
                <button wire:click="closeModal" type="button" style="background: transparent; border: none; color: rgba(255, 255, 255, 0.8); cursor: pointer; padding: 0.375rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;" title="Cerrar modal">
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                
                <!-- Feedback Alerts -->
                @if($status_message)
                    <div style="padding: 0.875rem 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.75rem; color: #047857; font-size: 0.8125rem; font-weight: 600; display: flex; align-items: center; gap: 0.625rem;">
                        <svg style="width: 18px; height: 18px; color: #059669; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <span>{{ $status_message }}</span>
                    </div>
                @endif
                @if($error_message)
                    <div style="padding: 0.875rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; color: #b91c1c; font-size: 0.8125rem; font-weight: 600; display: flex; align-items: center; gap: 0.625rem;">
                        <svg style="width: 18px; height: 18px; color: #dc2626; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $error_message }}</span>
                    </div>
                @endif

                <!-- Current Token Status Box -->
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; display: flex; flex-direction: column; gap: 0.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b;">Estado del Token OAuth</span>
                        @if($has_token && !$is_expired)
                            <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.1875rem 0.625rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                                Token Válido / Activo
                            </span>
                        @elseif($has_token && $is_expired)
                            <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.1875rem 0.625rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: #fffbeb; color: #b45309; border: 1px solid #fde68a;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                                Token Expirado
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.1875rem 0.625rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                                Sin Token
                            </span>
                        @endif
                    </div>
                    @if($has_token)
                        <div style="font-size: 0.6875rem; color: #64748b; font-family: monospace; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; border-top: 1px dashed #e2e8f0; padding-top: 0.375rem;">
                            <span>Token: <strong style="color: #334155;">{{ $token_preview }}</strong></span>
                            @if($token_expires_at)
                                <span>Vence: <strong style="color: #334155;">{{ $token_expires_at }}</strong></span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Form Fields -->
                <div style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.75rem;">
                    <!-- Host URL with Presets -->
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem;">
                            <label style="font-weight: 700; color: #334155;">Endpoint / URL de FEL (Host)</label>
                            <div style="display: flex; gap: 0.375rem;">
                                <button type="button" wire:click="setPresetHost('demo')" style="padding: 0.125rem 0.5rem; font-size: 0.625rem; font-weight: 700; background: #fffbeb; color: #b45309; border: 1px solid #fde68a; border-radius: 0.25rem; cursor: pointer;">
                                    Staging
                                </button>
                                <button type="button" wire:click="setPresetHost('production')" style="padding: 0.125rem 0.5rem; font-size: 0.625rem; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; border-radius: 0.25rem; cursor: pointer;">
                                    Producción
                                </button>
                            </div>
                        </div>
                        <input type="text" wire:model.defer="host" 
                               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-family: monospace; font-size: 0.75rem; color: #1e293b; background: #ffffff; outline: none;"
                               placeholder="https://fel.emizor.com">
                        @error('host') <span style="color: #e11d48; font-size: 0.6875rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Client ID -->
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 0.375rem;">Client ID</label>
                        <input type="text" wire:model.defer="client_id" 
                               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-family: monospace; font-size: 0.75rem; color: #1e293b; background: #ffffff; outline: none;"
                               placeholder="Ej: 1 o 6">
                        @error('client_id') <span style="color: #e11d48; font-size: 0.6875rem;">{{ $message }}</span> @enderror
                    </div>

                    <!-- Client Secret -->
                    <div>
                        <label style="display: block; font-weight: 700; color: #334155; margin-bottom: 0.375rem;">Client Secret</label>
                        <input type="password" wire:model.defer="client_secret" 
                               style="width: 100%; box-sizing: border-box; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; font-family: monospace; font-size: 0.75rem; color: #1e293b; background: #ffffff; outline: none;"
                               placeholder="••••••••••••••••••••••••••••••••">
                        @error('client_secret') <span style="color: #e11d48; font-size: 0.6875rem;">{{ $message }}</span> @enderror
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <button wire:click="closeModal" type="button" 
                        style="padding: 0.5rem 1rem; font-size: 0.75rem; font-weight: 600; color: #475569; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 0.5rem; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    {{ $status_message ? 'Cerrar y Concluir' : 'Cancelar' }}
                </button>
                <div style="display: flex; gap: 0.5rem;">
                    @if($status_message)
                        <button wire:click="closeModal" type="button" 
                                style="padding: 0.5rem 1.25rem; font-size: 0.75rem; font-weight: 700; color: #ffffff; background: #059669; border: none; border-radius: 0.5rem; cursor: pointer; box-shadow: 0 1px 3px rgba(5,150,105,0.25);">
                            Listo / Finalizar
                        </button>
                    @else
                        <button wire:click="saveAndAuthenticate" wire:loading.attr="disabled" type="button" 
                                style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 1.25rem; font-size: 0.75rem; font-weight: 700; color: #ffffff; background: #0284c7; border: none; border-radius: 0.5rem; cursor: pointer; box-shadow: 0 1px 3px rgba(2,132,199,0.25);">
                            <span wire:loading.remove wire:target="saveAndAuthenticate">Guardar y Autenticar</span>
                            <span wire:loading wire:target="saveAndAuthenticate">Autenticando...</span>
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
