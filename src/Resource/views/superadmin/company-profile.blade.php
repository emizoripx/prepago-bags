@extends('prepagobags::layouts.app')
@section('meta_title',"Perfil de Compañía")

@section('header')
    @if($errors->any())
        <div class="alert alert-failure mb-4">
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
@endsection

@section('body')
  
<div class="container px-4 mx-auto" x-data="{ tab: 'information' }">
    <div class="py-8 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold leading-tight text-white">Perfil: {!! json_decode($company->settings)->name !!}</h2>
            <p class="text-sm text-gray-400 mt-1">NIT: {!! json_decode($company->settings)->id_number !!} | FASE: {{$company->production ? 'Producción' : 'Pruebas'}}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.companies.impersonate', $company->company_id) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Ingresar como Cliente</a>
            <button onclick="Livewire.emit('openModal', 'suspendModal', [{{$company->id}}])" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Suspender</button>
            <button onclick="Livewire.emit('openModal', 'linkedModal', [{{$company->id}}])" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Vincular</button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-gray-700 mb-6">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-400">
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'information'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'information', 'border-transparent hover:text-gray-300 hover:border-gray-300': tab !== 'information' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Información
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'users'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'users', 'border-transparent hover:text-gray-300 hover:border-gray-300': tab !== 'users' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Usuarios
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'settings'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'settings', 'border-transparent hover:text-gray-300 hover:border-gray-300': tab !== 'settings' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Configuración
                </a>
            </li>
        </ul>
    </div>

    <!-- Tab Contents -->
    <div x-show="tab === 'information'">
        <livewire:company-information-tab :company_id="$company->id" />
    </div>
    <div x-show="tab === 'users'" style="display: none;">
        <livewire:company-users-tab :company_id="$company->id" />
    </div>
    <div x-show="tab === 'settings'" style="display: none;">
        <livewire:company-settings-tab :company_id="$company->id" />
    </div>

</div>
@endsection
