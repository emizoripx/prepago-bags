<div style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">
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
        <h3 style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.5rem; margin-bottom: 0.75rem;">Documentos Sectoriales / Facturación</h3>

        @if(!$company->is_postpago)
        <div style="overflow-x: auto; border: 1px solid #e2e8f0; border-radius: 0.75rem;">
            <table style="width: 100%; font-size: 0.75rem; text-align: left; color: #475569; border-collapse: collapse;">
                <thead style="background: #0284c7; color: #ffffff; text-transform: uppercase; font-size: 0.6875rem;">
                    <tr>
                        <th style="padding: 0.625rem 0.75rem; font-weight: 600;">#</th>
                        <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Nombre Documento</th>
                        <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Disponibles</th>
                        <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Última Bolsa</th>
                        <th style="padding: 0.625rem 0.75rem; font-weight: 600;">Emitidas</th>
                    </tr>
                </thead>
                <tbody style="background: #ffffff;">
                    @foreach($document_sectors as $di)
                    <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s;">
                        <td style="padding: 0.625rem 0.75rem; font-family: monospace; font-weight: 700; color: #0f172a;">{{ $di->fel_doc_sector_id }}</td>
                        <td style="padding: 0.625rem 0.75rem; font-weight: 500; color: #1e293b;">{{ $arrayNames[$di->fel_doc_sector_id] ?? '-' }}</td>
                        <td style="padding: 0.625rem 0.75rem; font-weight: 700; color: #0284c7;">{{ $di->invoice_number_available }}</td>
                        <td style="padding: 0.625rem 0.75rem; font-family: monospace; color: #64748b;">{{ $prepago_bags_payments[$di->fel_doc_sector_id] ?? "--" }}</td>
                        <td style="padding: 0.625rem 0.75rem; font-weight: 600; color: #0f172a;">{{ $di->counter }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
            <div>
                <h4 style="color: #0f172a; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">Estado de Pago</h4>
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
        </div>
        @endif
    </div>
</div>
