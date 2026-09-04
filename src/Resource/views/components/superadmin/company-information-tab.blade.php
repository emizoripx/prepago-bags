<div style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">
    @if(isset($settings->is_b2b2b_partner) && ($settings->is_b2b2b_partner == true || $settings->is_b2b2b_partner == 1))
        <livewire:prepagobags::superadmin.company-onboarding-status 
            :company_id="$company->id" 
            :tenant_id="$settings->tenant_id ?? $settings->id_number" 
            :phase="$company->phase" 
        />
    @endif

    <!-- Row 1: Basic Info & Usage Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; width: 100%;">
        <!-- Basic Information -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; margin-bottom: 0.75rem; display: flex; justify-content: space-between; align-items: center;">
                <span>Información Básica</span>
                <span style="color: #0284c7; font-weight: 700;">{{ ($company->is_postpago == 1) ? "POSTPAGO" : "PREPAGO" }}</span>
            </h3>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.625rem; font-size: 0.75rem; color: #475569;">
                <li style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">Razón Social:</span> 
                    <strong style="color: #0f172a; font-weight: 600; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $settings->name ?? '-' }}</strong>
                </li>
                <li style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">NIT:</span> 
                    <strong style="color: #1e293b; font-family: monospace;">{{ $settings->id_number ?? '-' }}</strong>
                </li>
                <li style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">Tipo de Cuenta:</span> 
                    <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; {{ $company->is_postpago ? 'background: #e0f2fe; color: #0369a1;' : 'background: #ecfdf5; color: #047857;' }}">
                        {{ ($company->is_postpago == 1) ? "POSTPAGO" : "PREPAGO" }}
                    </span>
                </li>
                <li style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b; font-weight: 500;">Conexión FEL:</span>
                    @if(isset($settings->is_b2b2b_partner) && ($settings->is_b2b2b_partner == true || $settings->is_b2b2b_partner == 1))
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe;">
                            Partner B2B2B
                        </span>
                    @else
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                Directa (OAuth)
                            </span>
                            <button type="button" onclick="Livewire.emit('openEditFelCredentialsModal', {{ $company->id }})" style="color: #0284c7; background: none; border: none; cursor: pointer; padding: 0; display: inline-flex; align-items: center;" title="Ver / Editar Credenciales FEL">
                                <svg style="width: 13px; height: 13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </div>
                    @endif
                </li>
                @if(!isset($settings->is_b2b2b_partner) || (!$settings->is_b2b2b_partner))
                    @if($fel_token)
                        <li style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b; font-weight: 500;">Client ID:</span>
                            <strong style="color: #1e293b; font-family: monospace;">{{ $fel_token->client_id ?? '-' }}</strong>
                        </li>
                        <li style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b; font-weight: 500;">Token Status:</span>
                            @if(!empty($fel_token->access_token))
                                <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.6875rem; font-weight: 700; color: #047857;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #10b981; display: inline-block;"></span> Activo
                                </span>
                            @else
                                <span style="color: #dc2626; font-weight: 600;">Sin Token</span>
                            @endif
                        </li>
                    @endif
                @endif
                @if($company->is_postpago == 1 && !empty($post_pago_plan_companies))
                    <li style="display: flex; justify-content: space-between; align-items: center;"><span style="color: #64748b; font-weight: 500;">Plan Postpago:</span> <strong style="color: #0f172a;">{!! $post_pago_plan_companies->name !!}</strong></li>
                    <li style="display: flex; justify-content: space-between; align-items: center;"><span style="color: #64748b; font-weight: 500;">Frecuencia:</span> <strong style="color: #0f172a;">{!! $post_pago_plan_companies->frequency !!} mes(es)</strong></li>
                    <li style="display: flex; justify-content: space-between; align-items: center;"><span style="color: #64748b; font-weight: 500;">Creado en:</span> <strong style="color: #1e293b; font-family: monospace;">{!! \Carbon\Carbon::parse($post_pago_plan_companies->created_at)->format("d/m/Y") !!}</strong></li>
                    <li style="display: flex; justify-content: space-between; align-items: center;"><span style="color: #64748b; font-weight: 500;">Fecha Inicio:</span> <strong style="color: #1e293b; font-family: monospace;">{!! $post_pago_plan_companies->start_date !!}</strong></li>
                    <li style="display: flex; justify-content: space-between; align-items: center;"><span style="color: #64748b; font-weight: 500;">Fecha Fin:</span> <strong style="color: #1e293b; font-family: monospace;">{!! \Carbon\Carbon::parse($post_pago_plan_companies->start_date)->addMonths($post_pago_plan_companies->frequency)->format("d/m/Y") !!}</strong></li>
                @endif
            </ul>
        </div>

        <!-- Usage Statistics -->
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); grid-column: span 2;">
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; margin-bottom: 0.75rem;">
                <h3 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin: 0;">Estadísticas de Uso</h3>
                @if($stats && $stats->updated_at)
                    <span style="font-size: 0.6875rem; color: #94a3b8; font-family: monospace;">Actualizado: {{ $stats->updated_at->diffForHumans() }}</span>
                @endif
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(110px, 1fr)); gap: 0.75rem; text-align: center;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.75rem;">
                    <p style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin: 0;">Clientes</p>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #0284c7; margin: 0.25rem 0 0 0;">{{ $stats ? $stats->total_clients : 0 }}</p>
                    @if($company->is_postpago && !empty($post_pago_plan_companies)) <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.125rem 0 0 0;">de {{ $post_pago_plan_companies->num_clients }}</p> @endif
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.75rem;">
                    <p style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin: 0;">Productos</p>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #0284c7; margin: 0.25rem 0 0 0;">{{ $stats ? $stats->total_products : 0 }}</p>
                    @if($company->is_postpago && !empty($post_pago_plan_companies)) <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.125rem 0 0 0;">de {{ $post_pago_plan_companies->num_products }}</p> @endif
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.75rem;">
                    <p style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin: 0;">Sucursales</p>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #0284c7; margin: 0.25rem 0 0 0;">{{ $stats ? $stats->total_branches : 0 }}</p>
                    @if($company->is_postpago && !empty($post_pago_plan_companies)) <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.125rem 0 0 0;">de {{ $post_pago_plan_companies->num_branches }}</p> @endif
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; border-radius: 0.75rem;">
                    <p style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin: 0;">Usuarios</p>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #0284c7; margin: 0.25rem 0 0 0;">{{ $stats ? $stats->total_users : 0 }}</p>
                    @if($company->is_postpago && !empty($post_pago_plan_companies)) <p style="font-size: 0.625rem; color: #94a3b8; margin: 0.125rem 0 0 0;">de {{ $post_pago_plan_companies->num_users }}</p> @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Custom Reports (Reportes Personalizados) -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="padding: 0.375rem; border-radius: 0.5rem; background: #f3e8ff; color: #9333ea; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h3 style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; color: #1e293b; margin: 0;">Reportes Personalizados (Custom Reports)</h3>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.125rem 0 0 0;">Módulos y plantillas de reportes fiscales a medida asignados a esta compañía.</p>
                </div>
            </div>
            <div>
                @if(count($custom_reports) > 0)
                    <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe;">
                        {{ count($custom_reports) }} reporte(s) habilitado(s)
                    </span>
                @else
                    <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 600; background: #f1f5f9; color: #64748b;">
                        Sin reportes personalizados
                    </span>
                @endif
            </div>
        </div>

        @if(count($custom_reports) > 0)
            <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 0.75rem;">
                <table style="width: 100%; font-size: 0.75rem; text-align: left; color: #475569; border-collapse: collapse;">
                    <thead style="background: #0284c7; color: #ffffff; text-transform: uppercase; font-size: 0.6875rem;">
                        <tr>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">ID</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Reporte / Entidad</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Formato</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Filtros Habilitados</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Plantilla S3 / Blade</th>
                        </tr>
                    </thead>
                    <tbody style="background: #ffffff;">
                        @foreach($custom_reports as $report)
                        <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;">
                            <td style="padding: 0.625rem 0.75rem; font-family: monospace; font-weight: 700; color: #0f172a;">#{{ $report->id ?? '-' }}</td>
                            <td style="padding: 0.625rem 0.75rem; font-weight: 600; color: #0f172a;">
                                {{ str_replace('_', ' ', $report->entity ?? 'Reporte') }}
                                <div style="font-family: monospace; font-size: 0.6875rem; color: #94a3b8; font-weight: normal;">{{ $report->entity ?? '' }}</div>
                            </td>
                            <td style="padding: 0.625rem 0.75rem;">
                                <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 0.25rem; font-size: 0.6875rem; font-weight: 700; background: #f3e8ff; color: #7e22ce; font-family: monospace; text-transform: uppercase;">
                                    {{ $report->type_format_report ?? 'Excel / XLSX' }}
                                </span>
                            </td>
                            <td style="padding: 0.625rem 0.75rem;">
                                @if(!empty($report->filters) && is_array($report->filters))
                                    <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                                        @foreach($report->filters as $filter)
                                            <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.625rem; background: #f1f5f9; color: #334155; font-family: monospace;">
                                                {{ $filter }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">Estándar</span>
                                @endif
                            </td>
                            <td style="padding: 0.625rem 0.75rem;">
                                @if(!empty($report->template))
                                    <a href="{{ $report->template }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.375rem; color: #0284c7; font-weight: 600; text-decoration: underline;" title="{{ $report->template }}">
                                        <svg style="width: 14px; height: 14px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span>Descargar / Ver Plantilla</span>
                                    </a>
                                @else
                                    <span style="color: #94a3b8; font-style: italic;">Plantilla estándar</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: 1.5rem; text-align: center; font-size: 0.75rem; color: #94a3b8; background: #f8fafc; border-radius: 0.75rem; border: 1px dashed #cbd5e1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <svg style="width: 28px; height: 28px; color: #cbd5e1; margin-bottom: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p style="margin: 0; font-weight: 500;">Esta compañía no tiene reportes personalizados asignados.</p>
                <span style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.25rem;">Utiliza los reportes generales del portal.</span>
            </div>
        @endif
    </div>

    <!-- Row 3: Documents / Billing Status -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.625rem;">
                <div style="padding: 0.375rem; border-radius: 0.5rem; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h3 style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; color: #1e293b; margin: 0;">Documentos Sectoriales / Disponibilidad</h3>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.125rem 0 0 0;">Tipos de factura autorizados por Impuestos (SIN) y bolsas/límites disponibles.</p>
                </div>
            </div>
            <div>
                <button wire:click="syncDocumentSectors" wire:loading.attr="disabled" 
                    class="flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-xl transition-colors bg-sky-50 text-sky-600 hover:bg-sky-100 border border-sky-200/50 shadow-sm cursor-pointer">
                    <svg wire:loading.remove wire:target="syncDocumentSectors" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <svg wire:loading wire:target="syncDocumentSectors" class="w-3.5 h-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span wire:loading.remove wire:target="syncDocumentSectors">Sincronizar Documentos Sector</span>
                    <span wire:loading wire:target="syncDocumentSectors">Sincronizando...</span>
                </button>
            </div>
        </div>

        @if(session()->has('sync_message'))
            <div style="margin-bottom: 1rem; padding: 0.625rem 1rem; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.75rem; color: #047857; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('sync_message') }}
            </div>
        @endif
        @if(session()->has('sync_error'))
            <div style="margin-bottom: 1rem; padding: 0.625rem 1rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem; color: #b91c1c; font-size: 0.75rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('sync_error') }}
            </div>
        @endif

        @if(count($document_sectors) > 0)
        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 0.75rem;">
            <table style="width: 100%; font-size: 0.75rem; text-align: left; color: #475569; border-collapse: collapse;">
                <thead style="background: #0284c7; color: #ffffff; text-transform: uppercase; font-size: 0.6875rem;">
                    <tr>
                        <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Código</th>
                        <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Nombre del Documento Sector</th>
                        @if(!$company->is_postpago)
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Disponibles</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Vencimiento Bolsa</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Última Recarga</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Emitidas</th>
                        @else
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Límite Postpago</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Facturas Emitidas</th>
                            <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Excedentes</th>
                        @endif
                    </tr>
                </thead>
                <tbody style="background: #ffffff;">
                    @foreach($document_sectors as $di)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;">
                        <td style="padding: 0.625rem 0.75rem; font-family: monospace; font-weight: 700; color: #0f172a;">{{ $di->fel_doc_sector_id }}</td>
                        <td style="padding: 0.625rem 0.75rem; font-weight: 600; color: #1e293b;">
                            {{ $arrayNames[$di->fel_doc_sector_id] ?? ('Documento Sector ' . $di->fel_doc_sector_id) }}
                        </td>
                        @if(!$company->is_postpago)
                            <td style="padding: 0.625rem 0.75rem; font-weight: 700; color: {{ ($di->invoice_number_available > 0) ? '#0284c7' : '#dc2626' }};">
                                {{ $di->invoice_number_available }} facturas
                            </td>
                            <td style="padding: 0.625rem 0.75rem; font-family: monospace; color: #64748b;">
                                {{ !empty($di->duedate) ? \Carbon\Carbon::parse($di->duedate)->format('d/m/Y') : '-' }}
                            </td>
                            <td style="padding: 0.625rem 0.75rem; font-family: monospace; color: #64748b;">
                                {{ $prepago_bags_payments[$di->fel_doc_sector_id] ?? "--" }}
                            </td>
                            <td style="padding: 0.625rem 0.75rem; font-weight: 600; color: #0f172a;">
                                {{ $di->counter }}
                            </td>
                        @else
                            <td style="padding: 0.625rem 0.75rem; font-weight: 700; color: #0284c7;">
                                {{ $di->postpago_limit ?? 'Ilimitado' }}
                            </td>
                            <td style="padding: 0.625rem 0.75rem; font-weight: 600; color: #0f172a;">
                                {{ $di->postpago_counter ?? 0 }}
                            </td>
                            <td style="padding: 0.625rem 0.75rem; font-weight: 600; color: {{ ($di->postpago_exceded_limit > 0) ? '#dc2626' : '#64748b' }};">
                                {{ $di->postpago_exceded_limit ?? 0 }}
                            </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="padding: 1.5rem; text-align: center; font-size: 0.75rem; color: #94a3b8; background: #f8fafc; border-radius: 0.75rem; border: 1px dashed #cbd5e1; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <svg style="width: 28px; height: 28px; color: #cbd5e1; margin-bottom: 0.5rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <p style="margin: 0; font-weight: 500;">No se encontraron documentos sectoriales registrados para esta empresa.</p>
            <span style="font-size: 0.6875rem; color: #94a3b8; margin-top: 0.25rem;">Haz clic en "Sincronizar Documentos Sector" para obtener los tipos de factura autorizados por Impuestos.</span>
        </div>
        @endif

        @if($company->is_postpago)
        <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
            <h4 style="color: #0f172a; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Estado de Pago del Plan Postpago</h4>
            @if($stats && $stats->total_due_invoices_count > 0)
                <div style="padding: 0.75rem; margin-bottom: 0.75rem; font-size: 0.75rem; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.75rem;">
                    <span style="font-weight: 700;">¡Atención!</span> La cuenta tiene {{ $stats->total_due_invoices_count }} facturas pendientes por un total de <strong>Bs. {{ number_format($stats->total_due_invoices_amount, 2) }}</strong>
                </div>
            @else
                <div style="padding: 0.75rem; margin-bottom: 0.75rem; font-size: 0.75rem; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 0.75rem;">
                    Al día. No hay facturas pendientes.
                </div>
            @endif

            @if(!empty($post_pago_plan_companies))
            <ul style="list-style: none; padding: 0; margin: 0.75rem 0 0 0; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.75rem; color: #475569;">
                <li style="display: flex; justify-content: space-between;"><span style="font-weight: 600; color: #334155;">Suspensión por Factura Pendiente:</span> <strong style="color: #0f172a;">{!! ($post_pago_plan_companies->due_invoice_suspend == 1)? "Activado" : "Inactivo" !!}</strong></li>
                @if ($post_pago_plan_companies->due_invoice_suspend == 1)
                <li style="display: flex; justify-content: space-between;"><span style="font-weight: 600; color: #334155;">Límite Facturas Pendientes:</span> <strong style="color: #0f172a;">{!! $post_pago_plan_companies->due_invoice_limit !!}</strong></li>
                @endif
            </ul>
            @endif
        </div>
        @endif
    </div>
</div>
