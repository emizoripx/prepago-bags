@extends('prepagobags::layouts.app')
@php
    $settings = json_decode($company->settings);
    $companyName = $settings->name ?? 'Compañía';
    $nitNumber = $settings->id_number ?? '-';
    $logoUrl = $settings->company_logo ?? null;
    if ($logoUrl && !str_starts_with($logoUrl, 'http')) {
        $logoUrl = url($logoUrl);
    }
    $customReports = $settings->custom_reports ?? [];
@endphp

@section('meta_title', "Perfil: " . $companyName)

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
<div style="width: 100%; padding-bottom: 3rem;">
    <!-- Company Header Bar -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); width: 100%; box-sizing: border-box;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; width: 100%;">
            
            <!-- Left Branding & Metadata -->
            <div style="display: flex; align-items: center; gap: 1.25rem; flex: 1; min-width: 280px;">
                <!-- Company Logo / Avatar with Quick Trigger -->
                <div style="position: relative; flex-shrink: 0;">
                    @if(!empty($logoUrl))
                        <div style="width: 64px; height: 64px; border-radius: 1rem; background: #ffffff; border: 1px solid #cbd5e1; padding: 4px; display: flex; align-items: center; justify-content: center; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                            <img src="{{ $logoUrl }}" alt="Logo {{ $companyName }}" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>
                    @else
                        <div style="width: 64px; height: 64px; border-radius: 1rem; background: linear-gradient(135deg, #0284c7, #2563eb); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.375rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; box-shadow: 0 4px 6px -1px rgba(2,132,199,0.25);">
                            {{ strtoupper(substr($companyName, 0, 2)) }}
                        </div>
                    @endif
                    <button type="button" 
                            onclick="Livewire.emit('openUploadCompanyLogoModal', {{ $company->id }})" 
                            style="position: absolute; bottom: -4px; right: -4px; padding: 4px; background: #ffffff; color: #475569; border-radius: 50%; border: 1px solid #cbd5e1; box-shadow: 0 2px 4px rgba(0,0,0,0.1); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 22px; height: 22px;" 
                            title="Subir o Cambiar Logo (Dropzone)">
                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                </div>

                <!-- Info Column -->
                <div style="flex: 1; min-width: 0;">
                    <!-- Breadcrumbs -->
                    <div style="display: flex; align-items: center; font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">
                        <a href="{{ route('admin.companies') }}" style="color: #0284c7; text-decoration: none; font-weight: 500;">Compañías</a>
                        <span style="margin: 0 0.375rem; color: #cbd5e1;">/</span>
                        <span style="font-weight: 600; color: #1e293b; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 260px;">{{ $companyName }}</span>
                    </div>

                    <!-- Company Title + Status Badges -->
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <h2 style="font-size: 1.375rem; font-weight: 700; line-height: 1.2; color: #0f172a; margin: 0;">
                            {{ $companyName }}
                        </h2>
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; {{ $company->production ? 'background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;' : 'background: #fffbeb; color: #b45309; border: 1px solid #fde68a;' }}">
                            {{ $company->production ? 'Producción' : 'Pruebas' }}
                        </span>
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.6875rem; font-weight: 700; background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                            {{ $company->is_postpago ? 'Postpago' : 'Prepago' }}
                        </span>
                    </div>

                    <!-- Metadata Bar -->
                    <div style="display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap; font-size: 0.75rem; color: #64748b; margin-top: 0.375rem;">
                        <span>NIT: <strong style="color: #1e293b; font-family: monospace;">{{ $nitNumber }}</strong></span>
                        <span style="color: #cbd5e1;">•</span>
                        <span>ID: <strong style="color: #1e293b; font-family: monospace;">#{{ $company->id }}</strong></span>
                        @if($company->created_at)
                            <span style="color: #cbd5e1;">•</span>
                            <span>Creado: <strong style="color: #1e293b;">{{ \Carbon\Carbon::parse($company->created_at)->diffForHumans() }}</strong></span>
                        @endif
                        @if(count($customReports) > 0)
                            <span style="color: #cbd5e1;">•</span>
                            <span style="display: inline-flex; align-items: center; gap: 0.25rem; color: #6b21a8; font-weight: 600; background: #f3e8ff; border: 1px solid #e9d5ff; padding: 0.125rem 0.5rem; border-radius: 0.375rem; font-size: 0.6875rem;">
                                <svg style="width: 12px; height: 12px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                {{ count($customReports) }} Reporte(s) Custom
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Action Buttons -->
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; flex-shrink: 0; justify-content: flex-end;">
                <!-- Subir / Cambiar Logo (Dropzone) Button -->
                <button type="button" 
                        onclick="Livewire.emit('openUploadCompanyLogoModal', {{ $company->id }})" 
                        style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; border-radius: 0.625rem; font-size: 0.75rem; font-weight: 600; background: #ffffff; border: 1px solid #cbd5e1; color: #334155; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.15s ease;">
                    <svg style="width: 14px; height: 14px; color: #64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>{{ !empty($logoUrl) ? 'Cambiar Logo' : 'Subir Logo' }}</span>
                </button>

                <!-- Ingresar como Cliente -->
                <a href="{{ route('admin.companies.impersonate', $company->company_id) }}" 
                   style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; border-radius: 0.625rem; font-size: 0.75rem; font-weight: 600; background: #059669; border: none; color: #ffffff; cursor: pointer; text-decoration: none; box-shadow: 0 1px 2px rgba(5,150,105,0.25); transition: background 0.15s ease;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Ingresar como Cliente</span>
                </a>

                <!-- Suspender -->
                <button type="button" 
                        onclick="Livewire.emit('openModal', 'suspendModal', [{{$company->id}}])" 
                        style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; border-radius: 0.625rem; font-size: 0.75rem; font-weight: 600; background: #e11d48; border: none; color: #ffffff; cursor: pointer; box-shadow: 0 1px 2px rgba(225,29,72,0.25); transition: background 0.15s ease;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                    <span>Suspender</span>
                </button>

                <!-- Vincular -->
                <button type="button" 
                        onclick="Livewire.emit('openModal', 'linkedModal', [{{$company->id}}])" 
                        style="display: inline-flex; align-items: center; gap: 0.375rem; padding: 0.5rem 0.875rem; border-radius: 0.625rem; font-size: 0.75rem; font-weight: 600; background: #0284c7; border: none; color: #ffffff; cursor: pointer; box-shadow: 0 1px 2px rgba(2,132,199,0.25); transition: background 0.15s ease;">
                    <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                    </svg>
                    <span>Vincular</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Active Tab Content Area -->
    <div x-show="currentTab === 'information'">
        <livewire:company-information-tab :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'invoices'" style="display: none;">
        <livewire:invoices-admin-table :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'users'" style="display: none;">
        <livewire:company-users-tab :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'settings'" style="display: none;">
        <livewire:company-settings-tab :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'products'" style="display: none;">
        <livewire:company-products-tab :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'clients'" style="display: none;">
        <livewire:company-clients-tab :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'payments'" style="display: none;">
        <livewire:company-payments-tab :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'parametrics'" style="display: none;">
        <livewire:company-parametrics-tab :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'branches'" style="display: none;">
        <livewire:company-branches-tab :company_id="$company->id" />
    </div>
    <div x-show="currentTab === 'templates'" style="display: none;">
        <livewire:company-templates-tab :company_id="$company->id" />
    </div>

    <!-- Standalone Livewire Logo Upload Modal -->
    <livewire:upload-company-logo-modal />
</div>
@endsection
