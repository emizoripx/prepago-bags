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
  
<div class="container px-4 mx-auto" x-data="{ tab: '{{ request('tab', 'information') }}' }">
    <div class="py-8 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold leading-tight text-slate-900">Perfil: {!! json_decode($company->settings)->name !!}</h2>
            <p class="text-sm text-slate-500 mt-1">NIT: {!! json_decode($company->settings)->id_number !!} | FASE: {{$company->production ? 'Producción' : 'Pruebas'}}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.companies.impersonate', $company->company_id) }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 text-sm font-medium">Ingresar como Cliente</a>
            <button onclick="Livewire.emit('openModal', 'suspendModal', [{{$company->id}}])" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">Suspender</button>
            <button onclick="Livewire.emit('openModal', 'linkedModal', [{{$company->id}}])" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">Vincular</button>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="border-b border-slate-200 mb-6">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-slate-500">
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'information'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'information', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'information' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Información
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'users'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'users', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'users' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Usuarios
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'settings'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'settings', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'settings' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Configuración
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'invoices'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'invoices', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'invoices' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Facturas
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'products'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'products', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'products' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Productos
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'clients'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'clients', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'clients' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Clientes
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'payments'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'payments', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'payments' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Pagos
                </a>
            </li>
        <li class="mr-2">
                <a href="#" @click.prevent="tab = 'parametrics'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'parametrics', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'parametrics' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Paramétricas
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'branches'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'branches', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'branches' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Sucursales
                </a>
            </li>
            <li class="mr-2">
                <a href="#" @click.prevent="tab = 'templates'" :class="{ 'text-emerald-500 border-emerald-500': tab === 'templates', 'border-transparent hover:text-slate-900 hover:border-slate-300': tab !== 'templates' }" class="inline-flex p-4 border-b-2 rounded-t-lg group">
                    Plantillas
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
    <div x-show="tab === 'invoices'" style="display: none;">
        <livewire:invoices-admin-table :company_id="$company->id" />
    </div>
    <div x-show="tab === 'products'" style="display: none;">
        <livewire:company-products-tab :company_id="$company->id" />
    </div>
    <div x-show="tab === 'clients'" style="display: none;">
        <livewire:company-clients-tab :company_id="$company->id" />
    </div>
    <div x-show="tab === 'payments'" style="display: none;">
        <livewire:company-payments-tab :company_id="$company->id" />
    </div>
    <div x-show="tab === 'parametrics'" style="display: none;">
        <livewire:company-parametrics-tab :company_id="$company->id" />
    </div>
    <div x-show="tab === 'branches'" style="display: none;">
        <livewire:company-branches-tab :company_id="$company->id" />
    </div>
    <div x-show="tab === 'templates'" style="display: none;">
        <livewire:company-templates-tab :company_id="$company->id" />
    </div>

</div>
@endsection
