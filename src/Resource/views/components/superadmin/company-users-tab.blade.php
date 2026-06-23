<div class="bg-gray-800 rounded-lg shadow overflow-hidden">
    <div class="px-4 py-5 border-b border-gray-700 sm:px-6 flex justify-between items-center">
        <h3 class="text-lg leading-6 font-medium text-white">Usuarios de la Compañía</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-700">
            <thead class="bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Usuario / Correo</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Último Acceso</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Creado En</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Verificado</th>
                </tr>
            </thead>
            <tbody class="bg-gray-800 divide-y divide-gray-700">
                @forelse($users as $user)
                <tr class="hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=random" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-white">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{ $user->email }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->diffForHumans() : 'Nunca' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ \Carbon\Carbon::parse($user->created_at)->format("Y-m-d H:i") }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        @if (!is_null($user->email_verified_at))
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                {{ $user->email_verified_at }}
                            </span>
                        @else
                            <button wire:click="markVerified({{ $user->id }})" class="text-emerald-400 hover:text-emerald-300 bg-emerald-400/10 hover:bg-emerald-400/20 px-3 py-1 rounded">
                                Verificar ahora
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-400">No hay usuarios vinculados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
