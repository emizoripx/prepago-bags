<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-4">
    <!-- Basic Information -->
    <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm sm:p-6 xl:p-8">
        <h3 class="mb-4 text-xl font-semibold text-slate-900">Información Básica</h3>
        <ul class="space-y-4 text-sm text-slate-600">
            <li><span class="font-medium text-slate-900">Tipo de Cuenta:</span> {!! ($company->is_postpago == 1) ? "POSTPAGO" : "PREPAGO" !!}</li>
            @if($company->is_postpago == 1 && !empty($post_pago_plan_companies))
                <li><span class="font-medium text-slate-900">Plan Postpago:</span> {!! $post_pago_plan_companies->name !!}</li>
                <li><span class="font-medium text-slate-900">Frecuencia:</span> {!! $post_pago_plan_companies->frequency !!} mes(es)</li>
                <li><span class="font-medium text-slate-900">Creado en:</span> {!! \Carbon\Carbon::parse($post_pago_plan_companies->created_at)->format("Y-m-d") !!}</li>
                <li><span class="font-medium text-slate-900">Fecha Inicio:</span> {!! $post_pago_plan_companies->start_date !!}</li>
                <li><span class="font-medium text-slate-900">Fecha Fin:</span> {!! \Carbon\Carbon::parse($post_pago_plan_companies->start_date)->addMonths($post_pago_plan_companies->frequency)->format("Y-m-d") !!}</li>
            @endif
        </ul>
    </div>

    <!-- Usage Statistics -->
    <div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm sm:p-6 xl:p-8 col-span-2">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-slate-900">Estadísticas de Uso</h3>
            @if($stats && $stats->updated_at)
                <span class="text-xs text-slate-500">Última actualización: {{ $stats->updated_at->diffForHumans() }}</span>
            @endif
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                <p class="text-sm font-medium text-slate-600">Clientes</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats ? $stats->total_clients : 0 }}</p>
                @if($company->is_postpago && !empty($post_pago_plan_companies)) <p class="text-xs text-slate-500">de {{ $post_pago_plan_companies->num_clients }}</p> @endif
            </div>
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                <p class="text-sm font-medium text-slate-600">Productos</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats ? $stats->total_products : 0 }}</p>
                @if($company->is_postpago && !empty($post_pago_plan_companies)) <p class="text-xs text-slate-500">de {{ $post_pago_plan_companies->num_products }}</p> @endif
            </div>
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                <p class="text-sm font-medium text-slate-600">Sucursales</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats ? $stats->total_branches : 0 }}</p>
                @if($company->is_postpago && !empty($post_pago_plan_companies)) <p class="text-xs text-slate-500">de {{ $post_pago_plan_companies->num_branches }}</p> @endif
            </div>
            <div class="bg-slate-50 border border-slate-200 p-4 rounded-xl">
                <p class="text-sm font-medium text-slate-600">Usuarios</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats ? $stats->total_users : 0 }}</p>
                @if($company->is_postpago && !empty($post_pago_plan_companies)) <p class="text-xs text-slate-500">de {{ $post_pago_plan_companies->num_users }}</p> @endif
            </div>
        </div>
    </div>
</div>

<div class="p-4 bg-white border border-slate-200 rounded-xl shadow-sm sm:p-6 xl:p-8">
    <h3 class="mb-4 text-xl font-semibold text-slate-900">Documentos / Facturación</h3>

    @if(!$company->is_postpago)
    <div class="overflow-hidden border border-slate-200 rounded-lg">
        <table class="w-full text-sm text-left text-slate-600 divide-y divide-slate-200">
            <thead class="bg-sky-600 text-white uppercase text-xs">
                <tr>
                    <th scope="col" class="px-4 py-3 font-semibold tracking-wider">#</th>
                    <th scope="col" class="px-4 py-3 font-semibold tracking-wider">Nombre Documento</th>
                    <th scope="col" class="px-4 py-3 font-semibold tracking-wider">Disponibles</th>
                    <th scope="col" class="px-4 py-3 font-semibold tracking-wider">Última Bolsa</th>
                    <th scope="col" class="px-4 py-3 font-semibold tracking-wider">Emitidas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @foreach($document_sectors as $di)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-4 py-3">{{ $di->fel_doc_sector_id }}</td>
                    <td class="px-4 py-3">{{ $arrayNames[$di->fel_doc_sector_id] ?? '-' }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-900">{{ $di->invoice_number_available }}</td>
                    <td class="px-4 py-3">{{ $prepago_bags_payments[$di->fel_doc_sector_id] ?? "--" }}</td>
                    <td class="px-4 py-3">{{ $di->counter }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <h4 class="text-slate-900 text-sm font-semibold mb-2">Estado de Pago</h4>
            @if($stats && $stats->total_due_invoices_count > 0)
                <div class="p-4 mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg" role="alert">
                    <span class="font-medium">¡Atención!</span> La cuenta tiene {{ $stats->total_due_invoices_count }} facturas pendientes por un total de <strong>Bs. {{ number_format($stats->total_due_invoices_amount, 2) }}</strong>
                </div>
            @else
                <div class="p-4 mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg" role="alert">
                    Al día. No hay facturas pendientes.
                </div>
            @endif

            @if(!empty($post_pago_plan_companies))
            <ul class="space-y-2 text-sm text-slate-600 mt-4">
                <li><span class="font-semibold text-slate-900">Suspensión por Factura Pendiente:</span> {!! ($post_pago_plan_companies->due_invoice_suspend == 1)? "Activado" : "Inactivo" !!}</li>
                @if ($post_pago_plan_companies->due_invoice_suspend == 1)
                <li><span class="font-semibold text-slate-900">Límite Facturas Pendientes para suspender:</span> {!! $post_pago_plan_companies->due_invoice_limit !!}</li>
                @endif
            </ul>
            @endif
        </div>
    </div>
    @endif
</div>
