<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50">
        <div>
            <h3 class="text-sm font-bold text-slate-800">Estado de Onboarding (Partner)</h3>
            <p class="text-xs text-slate-500 mt-0.5">Verifica el progreso de sincronización con el SIN (Emizor-FEL)</p>
        </div>
        <button wire:click="checkStatus" wire:loading.attr="disabled" class="inline-flex items-center px-3 py-1.5 border border-slate-300 shadow-sm text-xs font-semibold rounded-lg text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 transition-colors">
            <svg wire:loading.remove wire:target="checkStatus" class="w-4 h-4 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            <svg wire:loading wire:target="checkStatus" class="animate-spin -ml-1 mr-2 h-4 w-4 text-sky-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span wire:loading.remove wire:target="checkStatus">Consultar Estado</span>
            <span wire:loading wire:target="checkStatus">Consultando...</span>
        </button>
    </div>

    <div class="p-5">
        @if($error)
            <div class="rounded-lg bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error al consultar el estado</h3>
                        <div class="mt-2 text-xs text-red-700">
                            <p>{{ $error }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($status)
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Progreso General</span>
                @if(isset($status['is_ready']) && $status['is_ready'])
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                        <svg class="mr-1 h-3 w-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        Compañía Lista
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                        <svg class="mr-1 h-3 w-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        En Proceso
                    </span>
                @endif
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Global Steps -->
                <div>
                    <h4 class="text-xs font-semibold text-slate-900 mb-3 border-b border-slate-100 pb-2">Configuración Global</h4>
                    <ul class="space-y-3">
                        @if(isset($status['company']['steps']))
                            @foreach($status['company']['steps'] as $stepKey => $stepData)
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 mt-0.5">
                                        @if($stepData['status'] == 'completed')
                                            <svg class="h-4 w-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        @elseif($stepData['status'] == 'syncing')
                                            <svg class="h-4 w-4 text-sky-500 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        @else
                                            <svg class="h-4 w-4 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 9a1 1 0 00-1 1v2a1 1 0 002 0v-2a1 1 0 00-1-1zm2-3a1 1 0 00-1 1v.01a1 1 0 102 0V7a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                        @endif
                                    </div>
                                    <div class="ml-2.5 flex-1">
                                        <p class="text-xs font-medium text-slate-700">
                                            @if($stepKey == 'logo_uploaded') Logo Configurado
                                            @elseif($stepKey == 'credentials_uploaded') Credenciales SIAT
                                            @elseif($stepKey == 'parametrics_synced') Sincronización Paramétricas
                                            @else {{ $stepKey }} @endif
                                        </p>
                                        @if(isset($stepData['details']))
                                            <p class="text-[10px] text-slate-500 mt-0.5">{{ json_encode($stepData['details']) }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>

                <!-- Branches & POS Steps -->
                <div>
                    <h4 class="text-xs font-semibold text-slate-900 mb-3 border-b border-slate-100 pb-2">Sucursales y Puntos de Venta</h4>
                    @if(isset($status['branches']))
                        <div class="space-y-4">
                            @foreach($status['branches'] as $branch)
                                <div class="bg-slate-50 rounded-lg p-3 border border-slate-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold text-slate-800">{{ $branch['name'] ?? 'Sucursal ' . $branch['branch_code'] }}</span>
                                        @if(isset($branch['steps']['template_selected']['status']) && $branch['steps']['template_selected']['status'] == 'completed')
                                            <span class="text-[10px] text-emerald-600 font-medium">Plantilla ✅</span>
                                        @else
                                            <span class="text-[10px] text-slate-400 font-medium">Plantilla ❌</span>
                                        @endif
                                    </div>
                                    
                                    @if(isset($branch['pos']))
                                        <div class="space-y-2 mt-2">
                                            @foreach($branch['pos'] as $pos)
                                                <div class="bg-white rounded border border-slate-200 p-2 text-xs">
                                                    <p class="font-medium text-slate-700 mb-1">{{ $pos['name'] ?? 'POS ' . $pos['pos_code'] }}</p>
                                                    <ul class="space-y-1">
                                                        @foreach($pos['steps'] as $posStepKey => $posStepData)
                                                            <li class="flex items-center text-[11px]">
                                                                @if($posStepData['status'] == 'completed')
                                                                    <span class="text-emerald-500 mr-1.5">✓</span>
                                                                @else
                                                                    <span class="text-slate-300 mr-1.5">•</span>
                                                                @endif
                                                                <span class="text-slate-600">
                                                                    @if($posStepKey == 'cuis_generated') Código CUIS
                                                                    @elseif($posStepKey == 'cufd_generated') Código CUFD
                                                                    @else {{ $posStepKey }} @endif
                                                                </span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="text-center py-6 text-slate-500">
                <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <p class="text-sm">Presiona "Consultar Estado" para ver el progreso de esta compañía en Emizor-FEL.</p>
            </div>
        @endif
    </div>
</div>
