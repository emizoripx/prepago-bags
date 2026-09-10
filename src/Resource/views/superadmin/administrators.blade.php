@extends('prepagobags::layouts.app')
@section('meta_title', "Gestión de Administradores")

@section('header')
@endsection

@section('body')
<div x-data="{ 
    showModal: false, 
    mode: 'promote', 
    email: '',
    firstName: '',
    lastName: '',
    phone: '',
    password: ''
}">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between pb-6 border-b border-gray-200">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                <svg class="w-7 h-7 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Gestión de Administradores
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Control de accesos y asignación de permisos de Super Administrador del sistema
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <button 
                @click="showModal = true" 
                type="button" 
                class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:from-sky-500 hover:to-indigo-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition-all duration-150 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Agregar Administrador
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    <div class="mt-4">
        @if(session('message'))
            <div class="mb-4 bg-emerald-50 border border-emerald-300 text-emerald-800 text-sm p-4 rounded-lg flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('message') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-300 text-red-800 text-sm p-4 rounded-lg flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-300 text-red-800 text-sm p-4 rounded-lg shadow-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Administradores</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ count($administrators) }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tu Sesión Actual</p>
                <p class="mt-1 text-sm font-semibold text-gray-800 truncate" title="{{ $currentAdmin->email ?? '' }}">
                    {{ $currentAdmin->first_name ?? '' }} {{ $currentAdmin->last_name ?? '' }}
                </p>
                <p class="text-xs text-gray-400 truncate">{{ $currentAdmin->email ?? '' }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100 p-5 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Seguridad de Acceso</p>
                <p class="mt-1 text-sm font-semibold text-emerald-700 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Guardia 'admin' Activa
                </p>
                <p class="text-xs text-gray-400">Protección estricta /superu</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Administrators Table -->
    <div class="mt-8 bg-white shadow-sm rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Lista de Super Administradores</h3>
            <span class="text-xs text-gray-500 font-medium">{{ count($administrators) }} registros</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Usuario</th>
                        <th class="px-6 py-3 font-semibold">Teléfono</th>
                        <th class="px-6 py-3 font-semibold">Último Acceso</th>
                        <th class="px-6 py-3 font-semibold">Estado</th>
                        <th class="px-6 py-3 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($administrators as $admin)
                    <tr class="hover:bg-gray-50/80 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-bold flex items-center justify-center text-xs shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($admin->first_name ?: 'A', 0, 1)) }}{{ strtoupper(substr($admin->last_name ?: 'D', 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <div class="font-semibold text-gray-900 flex items-center gap-1.5">
                                        {{ $admin->first_name }} {{ $admin->last_name }}
                                        @if($currentAdmin && $currentAdmin->id === $admin->id)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-sky-100 text-sky-800">Tú</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $admin->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                            {{ $admin->phone ?: '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-xs">
                            @if($admin->last_login)
                                {{ \Carbon\Carbon::parse($admin->last_login)->format('d/m/Y H:i') }}
                            @else
                                <span class="text-gray-400">Sin registro</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($admin->is_superadmin == 1)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-emerald-500"></span>
                                    Superadmin Activo
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            @if($currentAdmin && $currentAdmin->id === $admin->id)
                                <span class="text-xs text-gray-400 italic" title="No puedes revocar tus propios permisos">Sesión activa</span>
                            @else
                                <form action="{{ route('admin.administrators.toggle', $admin->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de modificar los permisos de este administrador?');">
                                    @csrf
                                    @if($admin->is_superadmin == 1)
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-red-200 text-xs font-medium rounded text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                            Revocar Acceso
                                        </button>
                                    @else
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 border border-emerald-200 text-xs font-medium rounded text-emerald-700 bg-emerald-50 hover:bg-emerald-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors">
                                            Activar Acceso
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            No se encontraron superadministradores registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal: Agregar Administrador -->
    <div 
        x-show="showModal" 
        x-cloak 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(4px);">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div 
                x-show="showModal"
                @click.away="showModal = false"
                class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-2xl rounded-2xl border border-gray-200">
                
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Agregar Nuevo Administrador
                    </h3>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Toggle Mode: Promover usuario existente o Crear nuevo -->
                <div class="mt-4 flex rounded-lg p-1 bg-gray-100 border border-gray-200 text-xs font-semibold">
                    <button 
                        type="button" 
                        @click="mode = 'promote'" 
                        :class="mode === 'promote' ? 'bg-white text-sky-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                        class="w-1/2 py-2 rounded-md transition-all text-center">
                        Promover Usuario Existente
                    </button>
                    <button 
                        type="button" 
                        @click="mode = 'create'" 
                        :class="mode === 'create' ? 'bg-white text-sky-700 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                        class="w-1/2 py-2 rounded-md transition-all text-center">
                        Crear Nuevo Administrador
                    </button>
                </div>

                <form action="{{ route('admin.administrators.store') }}" method="POST" class="mt-5 space-y-4">
                    @csrf
                    <div>
                        <label for="modal_email" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="modal_email" 
                            required 
                            placeholder="usuario@ejemplo.com" 
                            class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all">
                        <p class="text-[11px] text-gray-500 mt-1" x-show="mode === 'promote'">
                            Si el correo ya pertenece a una cuenta registrada en la plataforma, se le asignará el rol de Super Administrador sin alterar su contraseña.
                        </p>
                    </div>

                    <!-- Campos adicionales para creación -->
                    <div x-show="mode === 'create'" class="space-y-4 pt-2">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="first_name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="first_name" 
                                    id="first_name" 
                                    :required="mode === 'create'"
                                    placeholder="Juan" 
                                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all">
                            </div>
                            <div>
                                <label for="last_name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                    Apellido <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="last_name" 
                                    id="last_name" 
                                    :required="mode === 'create'"
                                    placeholder="Pérez" 
                                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Teléfono / Celular
                            </label>
                            <input 
                                type="text" 
                                name="phone" 
                                id="phone" 
                                placeholder="+591 70000000" 
                                class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                :required="mode === 'create'"
                                placeholder="Mínimo 8 caracteres" 
                                class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-2">
                        <button 
                            type="button" 
                            @click="showModal = false" 
                            class="px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            Cancelar
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-500 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500 transition-colors">
                            <span x-text="mode === 'promote' ? 'Promover a Administrador' : 'Crear Administrador'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
