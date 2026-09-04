@extends('prepagobags::layouts.app')
@section('meta_title', "Configuración Partner")

@section('header')

@endsection

@section('body')
    <div>
        <div class="mt-8">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Datos del Partner (EMIZOR5)
            </h3>
            
            <div class="mt-4">
                @if(session('message'))
                    <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-sm p-4 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('message') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-300 text-red-800 text-sm p-4 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-300 text-red-800 text-sm p-4 rounded-lg">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="mt-4 bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Connection Settings Form -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Estado y Configuración de Conexión</h4>
                            <form action="{{ route('admin.partner.settings.update') }}" method="POST" class="mt-4 space-y-4">
                                @csrf
                                <!-- Entorno Testing -->
                                <div class="bg-gray-50 p-4 rounded border border-gray-200">
                                    <h5 class="font-medium text-gray-700 mb-2">Entorno Testing / Piloto</h5>
                                    <div>
                                        <label for="api_url_testing" class="block text-xs font-medium text-gray-600">Host API</label>
                                        <div class="mt-1">
                                            <input type="url" name="api_url_testing" id="api_url_testing" class="shadow-sm focus:ring-sky-500 focus:border-sky-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border bg-white" value="{{ $api_url_testing }}" placeholder="https://api.test.emizor.com">
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label for="partner_token_testing" class="block text-xs font-medium text-gray-600">Token (Partner)</label>
                                        <div class="mt-1 flex space-x-2">
                                            <input type="text" name="partner_token_testing" id="partner_token_testing" class="shadow-sm focus:ring-sky-500 focus:border-sky-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border bg-white" value="{{ $partner_token_testing }}">
                                            <button type="button" onclick="testConnection('testing')" class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                                Probar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Entorno Producción -->
                                <div class="bg-emerald-50 p-4 rounded border border-emerald-200">
                                    <h5 class="font-medium text-emerald-800 mb-2">Entorno Producción</h5>
                                    <div>
                                        <label for="api_url_production" class="block text-xs font-medium text-emerald-700">Host API</label>
                                        <div class="mt-1">
                                            <input type="url" name="api_url_production" id="api_url_production" class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-emerald-300 rounded-md p-2 border bg-white" value="{{ $api_url_production }}" placeholder="https://api.emizor.com">
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <label for="partner_token_production" class="block text-xs font-medium text-emerald-700">Token (Partner)</label>
                                        <div class="mt-1 flex space-x-2">
                                            <input type="text" name="partner_token_production" id="partner_token_production" class="shadow-sm focus:ring-emerald-500 focus:border-emerald-500 block w-full sm:text-sm border-emerald-300 rounded-md p-2 border bg-white" value="{{ $partner_token_production }}">
                                            <button type="button" onclick="testConnection('production')" class="inline-flex items-center px-3 py-1 border border-emerald-300 shadow-sm text-xs font-medium rounded-md text-emerald-800 bg-white hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                                Probar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3 pt-2">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                        Guardar Configuración
                                    </button>
                                </div>
                            </form>
                            
                            <script>
                                function testConnection(env) {
                                    const apiUrl = document.getElementById('api_url_' + env).value;
                                    const token = document.getElementById('partner_token_' + env).value;
                                    
                                    if (!apiUrl || !token) {
                                        alert('Por favor, ingresa el Host y el Token del entorno ' + env + ' antes de probar la conexión.');
                                        return;
                                    }
                                    
                                    const btn = event.currentTarget;
                                    const originalText = btn.innerHTML;
                                    btn.innerHTML = 'Probando...';
                                    btn.disabled = true;

                                    fetch('{{ route("admin.partner.settings.test") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        },
                                        body: JSON.stringify({
                                            api_url: apiUrl,
                                            partner_token: token
                                        })
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        btn.innerHTML = originalText;
                                        btn.disabled = false;
                                        if(data.success) {
                                            alert('✅ ' + data.message);
                                        } else {
                                            alert('❌ ' + data.message);
                                        }
                                    })
                                    .catch(err => {
                                        btn.innerHTML = originalText;
                                        btn.disabled = false;
                                        alert('❌ Ocurrió un error al intentar probar la conexión.');
                                        console.error(err);
                                    });
                                }
                            </script>
                        </div>

                        <!-- Metrics -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Métricas de Tráfico (Últimos 14 días)</h4>
                            <div class="mt-4 flex space-x-6">
                                <div>
                                    <span class="text-2xl font-bold text-sky-600">{{ $partner_api_count }}</span>
                                    <span class="block text-sm text-gray-500">Peticiones (API)</span>
                                </div>
                                <div>
                                    <span class="text-2xl font-bold text-indigo-600">{{ $partner_webhook_count }}</span>
                                    <span class="block text-sm text-gray-500">Webhooks Recibidos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Webhooks Section -->
            <div class="mt-8 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Webhooks para configurar en EMIZOR-FEL
                    </h3>
                </div>
                <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-4">
                        Copia las siguientes URLs y regístralas en el sistema Partner (EMIZOR-FEL) para poder recibir las notificaciones correspondientes a cada evento.
                    </p>
                    <ul class="space-y-3">
                        @foreach($partner_webhooks as $webhook)
                            <li class="flex items-center justify-between bg-gray-50 p-3 rounded border border-gray-200">
                                <span class="text-sm font-mono text-gray-800 break-all">{{ $webhook }}</span>
                                <button onclick="navigator.clipboard.writeText('{{ $webhook }}'); alert('Copiado al portapapeles!')" class="ml-4 text-sky-600 hover:text-sky-800 text-sm font-medium focus:outline-none">
                                    Copiar
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            
            <div class="mt-8 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Historial Reciente de Conexiones
                    </h3>
                    <span class="text-xs text-gray-500">Últimos 10 registros</span>
                </div>
                
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método / URL</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($partner_logs as $log)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($log->type == 'api')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-800">
                                                        API Request
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        Webhook
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <span class="font-bold text-gray-600">{{ $log->method }}</span> 
                                                <span class="text-gray-500 break-all">{{ Str::limit($log->url, 60) }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($log->status_code >= 200 && $log->status_code < 300)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $log->status_code }} OK
                                                    </span>
                                                @elseif($log->status_code)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        {{ $log->status_code }} ERROR
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        N/A
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 bg-white">
                                                No hay registros de conexión recientes.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
