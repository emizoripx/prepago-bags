<div x-data="{ open: @entangle('open') }"
     x-show="open"
     x-cloak
     style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; overflow-y: auto;"
     @if($step === 2 && $status === 'SYNCHRONIZING') wire:poll.3s="checkStatus" @endif>

    <!-- Backdrop -->
    <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); transition: opacity 0.2s ease;" 
         wire:click="closeModal"></div>

    <!-- Modal Container -->
    <div style="display: flex; min-height: 100vh; align-items: center; justify-content: center; padding: 1.5rem; position: relative; z-index: 10000;">
        
        <div style="position: relative; width: 100%; max-width: 42rem; background: #ffffff; border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #e2e8f0; overflow: hidden; text-align: left;"
             @click.stop>
            
            @if($step === 1)
            <!-- PASO 1: FORMULARIO DE CREDENCIALES -->
            <form wire:submit.prevent="migrate">
                <!-- Header -->
                <div style="padding: 1.25rem 1.5rem; background: linear-gradient(135deg, #7c3aed, #9333ea); color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.5rem; background: rgba(255, 255, 255, 0.18); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 20px; height: 20px; color: #ffffff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 700; color: #ffffff; margin: 0; line-height: 1.2;">
                                {{ $is_already_partner ? 'Credenciales Partner B2B2B' : 'Migrar a Partner B2B2B' }}
                            </h3>
                            <p style="font-size: 0.75rem; color: #f3e8ff; margin: 0.25rem 0 0 0;">
                                {{ $company_name }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeModal" type="button" style="background: transparent; border: none; color: rgba(255, 255, 255, 0.8); cursor: pointer; padding: 0.375rem; border-radius: 0.5rem;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div style="padding: 1.5rem;">
                    <!-- Context banner -->
                    <div style="background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 0.75rem; padding: 0.875rem 1rem; margin-bottom: 1.25rem; font-size: 0.8125rem; color: #6b21a8; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-weight: 600;">Entorno Partner:</span>
                            <select wire:model="target_environment" style="background: #ffffff; border: 1px solid #c084fc; color: #6b21a8; font-weight: 700; font-size: 0.75rem; border-radius: 0.375rem; padding: 0.25rem 0.5rem; outline: none; cursor: pointer;">
                                <option value="testing">🧪 Pruebas (Testing / Piloto)</option>
                                <option value="production">🚀 Producción</option>
                            </select>
                        </div>
                        @if(!empty($tenant_key) && $tenant_verified)
                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 9999px; font-size: 0.6875rem; font-family: monospace; font-weight: 700; color: #047857;">
                                ✓ Tenant Key: {{ $tenant_key }}
                            </span>
                        @else
                            <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.625rem; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; color: #475569;">
                                Conexión Individual
                            </span>
                        @endif
                    </div>

                    <!-- Input para Tenant Key Externo / Existente -->
                    <div style="margin-bottom: 1.25rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem;">
                        <label for="tenant_key_input" style="display: block; font-size: 0.75rem; font-weight: 700; color: #334155; margin-bottom: 0.375rem;">
                            ¿Ya tienes un Tenant Key de esta compañía en el Core? <span style="font-weight: normal; color: #64748b;">{{ $is_already_partner ? '' : '(Opcional)' }}</span>
                        </label>
                        <div style="display: flex; gap: 0.5rem;">
                            <input type="text" wire:model.defer="tenant_key_input" id="tenant_key_input" placeholder="Pega el Tenant Key existente (ej: 6a51539e12667)" {{ $is_already_partner ? 'disabled' : '' }} style="flex: 1; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0.5rem 0.75rem; font-size: 0.8125rem; font-family: monospace; outline: none; background: #ffffff;">
                            @if(!$is_already_partner)
                            <button type="button" wire:click="verifyTenantKey" wire:loading.attr="disabled" style="padding: 0.5rem 1rem; background: #7c3aed; color: #ffffff; border: none; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; white-space: nowrap;">
                                <span wire:loading.remove wire:target="verifyTenantKey">Verificar</span>
                                <span wire:loading wire:target="verifyTenantKey">Verificando...</span>
                            </button>
                            @if($tenant_verified || !empty($tenant_key_input))
                                <button type="button" wire:click="resetTenantVerification" style="padding: 0.5rem 0.75rem; background: #e2e8f0; color: #475569; border: none; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 600; cursor: pointer;">
                                    Limpiar
                                </button>
                            @endif
                            @endif
                        </div>
                        @if($tenant_error)
                            <span style="display: block; color: #ef4444; font-size: 0.75rem; margin-top: 0.375rem;">{{ $tenant_error }}</span>
                        @endif

                        @if($tenant_verified)
                            <div style="margin-top: 0.75rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.5rem; padding: 0.75rem; font-size: 0.75rem; color: #065f46;">
                                <div style="display: flex; align-items: center; gap: 0.375rem; font-weight: 700; margin-bottom: 0.25rem;">
                                    <svg style="width: 16px; height: 16px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>Compañía encontrada en el Core con este Tenant Key</span>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem; margin-top: 0.5rem; background: #ffffff; padding: 0.5rem 0.75rem; border-radius: 0.375rem; border: 1px solid #d1fae5;">
                                    <div>
                                        <span style="color: #64748b; font-size: 0.6875rem;">Firma / Certificado:</span><br>
                                        {!! $has_existing_certificate ? '<strong style="color:#059669;">✓ Detectado</strong>' : '<strong style="color:#d97706;">! Falta subir</strong>' !!}
                                    </div>
                                    <div>
                                        <span style="color: #64748b; font-size: 0.6875rem;">CUIS Operaciones:</span><br>
                                        {!! $has_existing_cuis ? '<strong style="color:#059669;">✓ Activo</strong>' : '<span style="color:#64748b;">Pendiente</span>' !!}
                                    </div>
                                    <div>
                                        <span style="color: #64748b; font-size: 0.6875rem;">Paramétricas:</span><br>
                                        {!! $has_existing_parametrics ? '<strong style="color:#059669;">✓ Sincronizadas</strong>' : '<span style="color:#64748b;">Pendiente</span>' !!}
                                        @if(!$has_existing_parametrics)
                                            <div style="margin-top: 0.25rem;">
                                                <button type="button" onclick="if(confirm('¿Estás seguro de forzar la sincronización? Esto descargará todos los catálogos y puede tardar varios segundos.')) { @this.call('forceSyncParametrics') }" wire:loading.attr="disabled" style="background: transparent; border: none; padding: 0; color: #6d28d9; font-size: 0.6875rem; font-weight: 600; cursor: pointer; text-decoration: underline;">
                                                    <span wire:loading.remove wire:target="forceSyncParametrics">Forzar Sincronización Manual</span>
                                                    <span wire:loading wire:target="forceSyncParametrics">Sincronizando...</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($has_existing_credentials)
                                    <div style="margin-top: 0.5rem;">
                                        <button type="button" wire:click="toggleCredentialsForm" style="background: transparent; border: none; padding: 0; color: #6d28d9; font-size: 0.75rem; font-weight: 600; cursor: pointer; text-decoration: underline;">
                                            {{ $show_credentials_form ? '▲ Ocultar actualización de credenciales' : ($is_already_partner ? '▼ Reemplazar Certificado o Token SIN' : '▼ ¿Deseas reemplazar el Certificado o Token SIN? (Opcional)') }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p style="font-size: 0.6875rem; color: #64748b; margin: 0.375rem 0 0 0;">
                                Si no tienes un Tenant Key previo, déjalo vacío y completa los datos abajo para registrar la empresa desde cero.
                            </p>
                        @endif
                    </div>
                    
                    @if($errors->has('general') || $error_message)
                        <div style="margin-bottom: 1.25rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 0.75rem 1rem; border-radius: 0.75rem; font-size: 0.75rem;">
                            <p style="font-weight: 700; margin: 0 0 0.25rem 0;">Error en la migración:</p>
                            <p style="margin: 0;">{{ $errors->first('general') ?? $error_message }}</p>
                        </div>
                    @endif
                    
                    @if(!$has_existing_credentials || $show_credentials_form)
                        <div style="display: flex; flex-direction: column; gap: 1rem; padding: {{ $has_existing_credentials ? '1rem' : '0' }}; background: {{ $has_existing_credentials ? '#f8fafc' : 'transparent' }}; border: {{ $has_existing_credentials ? '1px solid #e2e8f0' : 'none' }}; border-radius: 0.75rem; margin-bottom: 1rem;">
                            @if($is_already_partner)
                                <div style="background: #e0e7ff; color: #3730a3; padding: 0.5rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem;">
                                    <span style="font-weight: 600;">Nota:</span> Completa únicamente los campos que deseas actualizar. Puedes dejar en blanco los demás.
                                </div>
                            @endif
                            <!-- Token SIN -->
                            <div>
                                <label for="sin_token" style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.375rem;">Token SIN (Delegación) @if($has_existing_credentials) <span style="font-weight: normal; color: #64748b;">{{ $is_already_partner ? '' : '(Opcional)' }}</span> @endif</label>
                                <textarea wire:model.defer="sin_token" id="sin_token" rows="3" style="width: 100%; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0.625rem 0.75rem; font-size: 0.8125rem; font-family: monospace; outline: none; background: #ffffff;" placeholder="Pega el Token de Delegación del SIAT"></textarea>
                                @error('sin_token') <span style="display: block; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                            </div>
                            
                            <!-- Código Sistema -->
                            <div>
                                <label for="system_code" style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.375rem;">Código de Sistema (Emizor) <span style="font-weight: normal; color: #64748b;">{{ $is_already_partner ? '' : '(Opcional)' }}</span></label>
                                <input type="text" wire:model.defer="system_code" id="system_code" style="width: 100%; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0.625rem 0.75rem; font-size: 0.8125rem; outline: none; background: #ffffff;" placeholder="Ej: 9C87B57635...">
                                @error('system_code') <span style="display: block; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <!-- Certificado P12 -->
                                <div>
                                    <label for="certificate_file" style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.375rem;">Certificado Digital (.p12 / .pfx) @if($has_existing_credentials) <span style="font-weight: normal; color: #64748b;">{{ $is_already_partner ? '' : '(Opcional)' }}</span> @endif</label>
                                    <input type="file" wire:model="certificate_file" id="certificate_file" style="width: 100%; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0.375rem 0.5rem; font-size: 0.75rem; outline: none; background: #ffffff;">
                                    @error('certificate_file') <span style="display: block; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                                    <div wire:loading wire:target="certificate_file" style="font-size: 0.75rem; color: #9333ea; margin-top: 0.25rem;">Cargando archivo...</div>
                                </div>

                                <!-- Contraseña Certificado -->
                                <div>
                                    <label for="certificate_password" style="display: block; font-size: 0.75rem; font-weight: 600; color: #334155; margin-bottom: 0.375rem;">Contraseña del Certificado</label>
                                    <input type="password" wire:model.defer="certificate_password" id="certificate_password" style="width: 100%; box-sizing: border-box; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 0.625rem 0.75rem; font-size: 0.8125rem; outline: none; background: #ffffff;" placeholder="••••••••">
                                    @error('certificate_password') <span style="display: block; color: #ef4444; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer Buttons -->
                <div style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" wire:click="closeModal" style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                        {{ $is_already_partner ? 'Cerrar' : 'Cancelar' }}
                    </button>
                    @if(!$is_already_partner || $show_credentials_form)
                    <button type="submit" wire:loading.attr="disabled" style="padding: 0.5rem 1.25rem; border-radius: 0.5rem; border: none; background: #9333ea; color: #ffffff; font-size: 0.8125rem; font-weight: 600; cursor: pointer; box-shadow: 0 1px 2px rgba(147, 51, 234, 0.3);">
                        <span wire:loading.remove wire:target="migrate">
                            {{ $is_already_partner ? 'Actualizar Credenciales' : ($has_existing_credentials && !$show_credentials_form ? 'Confirmar Migración a Partner' : 'Iniciar Migración') }}
                        </span>
                        <span wire:loading wire:target="migrate">Procesando...</span>
                    </button>
                    @endif
                </div>
            </form>
            @else
            <!-- PASO 2: CHECKLIST DE PROGRESO ASÍNCRONO -->
            <div>
                <!-- Header -->
                <div style="padding: 1.25rem 1.5rem; background: {{ $status === 'COMPLETED' ? 'linear-gradient(135deg, #059669, #10b981)' : ($status === 'FAILED' ? 'linear-gradient(135deg, #dc2626, #ef4444)' : 'linear-gradient(135deg, #7c3aed, #9333ea)') }}; color: #ffffff; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="padding: 0.5rem; background: rgba(255, 255, 255, 0.18); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                            @if($status === 'COMPLETED')
                                <svg style="width: 20px; height: 20px; color: #ffffff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif($status === 'FAILED')
                                <svg style="width: 20px; height: 20px; color: #ffffff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            @else
                                <svg style="width: 20px; height: 20px; color: #ffffff;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 700; color: #ffffff; margin: 0; line-height: 1.2;">
                                @if($status === 'COMPLETED')
                                    {{ $is_already_partner ? 'Credenciales Actualizadas' : 'Migración Completada' }}
                                @elseif($status === 'FAILED')
                                    Error en la Migración
                                @else
                                    Sincronizando Empresa con Partner...
                                @endif
                            </h3>
                            <p style="font-size: 0.75rem; color: #f3e8ff; margin: 0.25rem 0 0 0;">
                                {{ $company_name }}
                            </p>
                        </div>
                    </div>
                    <button wire:click="closeModal" type="button" style="background: transparent; border: none; color: rgba(255, 255, 255, 0.8); cursor: pointer; padding: 0.375rem; border-radius: 0.5rem;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div style="padding: 1.5rem;">
                    @if($error_message)
                        <div style="margin-bottom: 1.25rem; background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; padding: 0.75rem 1rem; border-radius: 0.75rem; font-size: 0.75rem;">
                            <p style="font-weight: 700; margin: 0 0 0.25rem 0;">Error detectado:</p>
                            <p style="margin: 0;">{{ $error_message }}</p>
                        </div>
                    @endif

                    <!-- Checklist items -->
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <!-- Item 1 -->
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 0.5rem; background: {{ $is_registered ? '#ecfdf5' : '#f8fafc' }}; border: 1px solid {{ $is_registered ? '#a7f3d0' : '#e2e8f0' }};">
                            <div style="width: 24px; height: 24px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; background: {{ $is_registered ? '#10b981' : '#cbd5e1' }}; color: #ffffff;">
                                {{ $is_registered ? '✓' : '1' }}
                            </div>
                            <div style="font-size: 0.75rem;">
                                <p style="font-weight: 600; color: {{ $is_registered ? '#065f46' : '#334155' }}; margin: 0;">Registro en Core Partner</p>
                                <p style="color: #64748b; margin: 0.125rem 0 0 0; font-size: 0.6875rem;">{{ $is_registered ? 'Empresa dada de alta en la plataforma central.' : 'Pendiente...' }}</p>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 0.5rem; background: {{ $credentials_uploaded ? '#ecfdf5' : '#f8fafc' }}; border: 1px solid {{ $credentials_uploaded ? '#a7f3d0' : '#e2e8f0' }};">
                            <div style="width: 24px; height: 24px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; background: {{ $credentials_uploaded ? '#10b981' : '#cbd5e1' }}; color: #ffffff;">
                                {{ $credentials_uploaded ? '✓' : '2' }}
                            </div>
                            <div style="font-size: 0.75rem;">
                                <p style="font-weight: 600; color: {{ $credentials_uploaded ? '#065f46' : '#334155' }}; margin: 0;">Custodia de Credenciales SIN</p>
                                <p style="color: #64748b; margin: 0.125rem 0 0 0; font-size: 0.6875rem;">{{ $credentials_uploaded ? 'Certificado digital y Token SIN transferidos de forma segura.' : 'Pendiente...' }}</p>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 0.5rem; background: {{ $operations_started ? '#ecfdf5' : '#f8fafc' }}; border: 1px solid {{ $operations_started ? '#a7f3d0' : '#e2e8f0' }};">
                            <div style="width: 24px; height: 24px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; background: {{ $operations_started ? '#10b981' : '#cbd5e1' }}; color: #ffffff;">
                                {{ $operations_started ? '✓' : '3' }}
                            </div>
                            <div style="font-size: 0.75rem;">
                                <p style="font-weight: 600; color: {{ $operations_started ? '#065f46' : '#334155' }}; margin: 0;">Inicio de Operaciones & CUIS</p>
                                <p style="color: #64748b; margin: 0.125rem 0 0 0; font-size: 0.6875rem;">{{ $operations_started ? 'Solicitud de CUIS e inicio de operaciones despachado.' : 'Pendiente...' }}</p>
                            </div>
                        </div>

                        <!-- Item 4 -->
                        <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 0.5rem; background: {{ $parametrics_synced ? '#ecfdf5' : ($status === 'SYNCHRONIZING' ? '#faf5ff' : '#f8fafc') }}; border: 1px solid {{ $parametrics_synced ? '#a7f3d0' : ($status === 'SYNCHRONIZING' ? '#d8b4fe' : '#e2e8f0') }};">
                            <div style="width: 24px; height: 24px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; background: {{ $parametrics_synced ? '#10b981' : ($status === 'SYNCHRONIZING' ? '#9333ea' : '#cbd5e1') }}; color: #ffffff;">
                                {{ $parametrics_synced ? '✓' : '4' }}
                            </div>
                            <div style="font-size: 0.75rem;">
                                <p style="font-weight: 600; color: {{ $parametrics_synced ? '#065f46' : ($status === 'SYNCHRONIZING' ? '#6b21a8' : '#334155') }}; margin: 0;">Sincronización de Catálogos & Webhook</p>
                                <p style="color: #64748b; margin: 0.125rem 0 0 0; font-size: 0.6875rem;">
                                    @if($parametrics_synced)
                                        Paramétricas y catálogos SIN sincronizados exitosamente.
                                    @elseif($status === 'SYNCHRONIZING')
                                        Descargando catálogos del SIN en segundo plano. Esperando confirmación de Webhook...
                                        <div style="margin-top: 0.5rem;">
                                            <button type="button" onclick="if(confirm('¿Estás seguro de forzar la sincronización? Esto descargará todos los catálogos y puede tardar varios segundos.')) { @this.call('forceSyncParametrics') }" wire:loading.attr="disabled" style="background: transparent; border: none; padding: 0; color: #6d28d9; font-size: 0.6875rem; font-weight: 600; cursor: pointer; text-decoration: underline;">
                                                <span wire:loading.remove wire:target="forceSyncParametrics">¿Atascado? Forzar sincronización manualmente</span>
                                                <span wire:loading wire:target="forceSyncParametrics">Forzando...</span>
                                            </button>
                                        </div>
                                    @else
                                        Pendiente de sincronización...
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    @if($status === 'COMPLETED')
                        <button type="button" wire:click="finishMigration" style="padding: 0.5rem 1.25rem; border-radius: 0.5rem; border: none; background: #059669; color: #ffffff; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                            Finalizar y Ver Perfil
                        </button>
                    @elseif($status === 'FAILED')
                        <button type="button" wire:click="closeModal" style="padding: 0.5rem 1rem; border-radius: 0.5rem; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                            Cerrar
                        </button>
                        <button type="button" wire:click="$set('step', 1)" style="padding: 0.5rem 1.25rem; border-radius: 0.5rem; border: none; background: #9333ea; color: #ffffff; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                            Volver a Intentar
                        </button>
                    @else
                        <button type="button" wire:click="closeModal" style="padding: 0.5rem 1.25rem; border-radius: 0.5rem; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-size: 0.8125rem; font-weight: 600; cursor: pointer;">
                            Cerrar (El proceso continuará en segundo plano)
                        </button>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
