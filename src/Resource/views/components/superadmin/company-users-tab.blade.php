<div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
        <h3 class="text-base font-bold text-slate-900">Usuarios de la Compañía</h3>
        <span class="text-xs text-slate-500 font-medium">{{ count($users) }} usuarios</span>
    </div>
    <div class="relative overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-sky-600">
                <tr>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Usuario / Correo</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Último Acceso</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Creado En</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-white uppercase tracking-wider">Verificación</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 whitespace-nowrap">
                        <div class="flex items-center gap-2.5">
                            <div class="flex-shrink-0 h-7 w-7 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-[10px]">
                                {{ strtoupper(substr($user->first_name, 0, 1)) }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-semibold text-slate-900 leading-tight">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </span>
                                <span class="text-[11px] text-slate-400 leading-tight">
                                    {{ $user->email }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600 font-mono">
                        {{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->diffForHumans() : 'Nunca' }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs text-slate-600 font-mono">
                        {{ \Carbon\Carbon::parse($user->created_at)->format("d/m/Y H:i") }}
                    </td>
                    <td class="px-3 py-3 whitespace-nowrap text-xs">
                        @if (!is_null($user->email_verified_at))
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20">
                                Verificado
                            </span>
                        @else
                            <button wire:click="markVerified({{ $user->id }})" class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                                Verificar ahora
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-xs text-slate-400 bg-white">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-8 h-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <p>No hay usuarios vinculados.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
