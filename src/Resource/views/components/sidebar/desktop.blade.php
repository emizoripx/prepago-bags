<?php
    if (!isset($company) || empty($company)) {
        $companyId = request()->route('company_id') ?? request()->route('id');
        if ($companyId) {
            $company = \EmizorIpx\PrepagoBags\Models\AccountPrepagoBags::join('companies', 'fel_company.company_id', '=', 'companies.id')
                ->select('companies.id', 'fel_company.company_id', 'fel_company.client_id', 'fel_company.production', 'fel_company.is_postpago', 'companies.settings')
                ->where('fel_company.deleted_at', '=', null)
                ->where('companies.id', $companyId)
                ->first();
        }
    }

    $sidebar = [
        [
            "url" => "admin.dashboard",
            "icon" => "dashboard",
            "title" => "Panel de control",
        ], 
        [
            "url" => "admin.companies",
            "icon" => "suitcase",
            "title" => "Compañias",
        ],
        [
            "url" => "admin.invoices",
            "icon" => "invoices",
            "title" => "Facturas",
        ],
        [
            "url" => "admin.partner.settings",
            "icon" => "settings",
            "title" => "Configuración Partner",
        ],
    ];

    $companyName = isset($company) && isset($company->settings) ? (json_decode($company->settings)->name ?? 'Compañía') : 'Compañía';
?>

<style>
    .pb-sidebar-nav {
        background-color: #383838;
        color: #f1f5f9;
        min-height: 100vh;
    }
    .pb-sidebar-link {
        color: #cbd5e1;
        border-left: 4px solid transparent;
        transition: all 0.15s ease-in-out;
        text-decoration: none;
    }
    .pb-sidebar-link:hover {
        background-color: #484848 !important;
        color: #ffffff !important;
    }
    .pb-sidebar-link.active {
        background-color: #222222 !important;
        border-left: 4px solid #38bdf8 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    .pb-btn-back {
        background-color: #258cb5;
        color: #ffffff;
        transition: background-color 0.2s ease;
    }
    .pb-btn-back:hover {
        background-color: #1e7396;
        color: #ffffff;
    }
    .pb-btn-logout {
        background-color: #2b2b2b;
        border: 1px solid #4a4a4a;
        color: #d1d5db;
        transition: all 0.2s ease;
    }
    .pb-btn-logout:hover {
        background-color: #202020;
        color: #f87171;
        border-color: #666666;
    }
</style>

<div>
    <nav class="pb-sidebar-nav flex flex-col w-64 h-screen shadow-2xl transition-all duration-300 select-none">
        <!-- Logo Header -->
        <div class="flex items-center justify-center h-16 w-full shadow-sm" style="background-color: #2b2b2b; border-bottom: 1px solid #484848;">
            <span class="font-bold text-white text-xl tracking-wider flex items-center gap-2">
                <svg class="w-6 h-6" style="color: #38bdf8;" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <span style="color: #ffffff; font-weight: 700;">emizor</span>
            </span>
        </div>

        <!-- User / Admin Status Badge -->
        <div class="flex flex-col mt-3 mb-2 pb-3 mx-4" style="border-bottom: 1px solid #4a4a4a;">
            <span class="text-[10px] font-bold uppercase tracking-wider mb-1" style="color: #a3a3a3;">ADMINISTRADOR</span>
            <div class="flex items-center justify-between">
                <span class="font-semibold text-sm truncate text-white" title="Super Administrador">Super Administrador</span>
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold" style="background-color: rgba(16, 185, 129, 0.2); color: #34d399;">Activo</span>
            </div>
        </div>

        @if(isset($company))
            <!-- COMPANY CONTEXT SIDEBAR -->
            <div class="px-4 mt-2 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider" style="color: #9ca3af;">MENÚ DE COMPAÑÍA</span>
            </div>

            <!-- Back to Companies Button -->
            <div class="px-3 mb-2">
                <a href="{{ route('admin.companies') }}" class="pb-btn-back flex items-center justify-center w-full px-3 py-2 font-semibold text-xs rounded shadow group">
                    <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a Compañías
                </a>
            </div>

            <!-- Active Company Name Tag -->
            <div class="mx-3 px-3 py-2 rounded mb-2 shadow-inner" style="background-color: #262626; border-left: 4px solid #38bdf8;">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2 flex-shrink-0" style="color: #38bdf8;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span class="text-xs font-bold uppercase truncate text-white" title="{{ $companyName }}">
                        {{ $companyName }}
                    </span>
                </div>
            </div>

            <!-- Company Navigation Menu -->
            <div class="mt-1 flex-1 overflow-y-auto pr-1 space-y-0.5" style="scrollbar-width: thin;">
                <!-- 1. Vista General -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'information']) }}"
                   @click.prevent="currentTab = 'information'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=information')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'information' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'information' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Vista General</span>
                </a>

                <!-- 2. Facturas -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'invoices']) }}"
                   @click.prevent="currentTab = 'invoices'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=invoices')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'invoices' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'invoices' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Facturas</span>
                </a>

                <!-- 3. Usuarios -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'users']) }}"
                   @click.prevent="currentTab = 'users'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=users')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'users' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'users' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <span>Usuarios</span>
                </a>

                <!-- 4. Configuración -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'settings']) }}"
                   @click.prevent="currentTab = 'settings'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=settings')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'settings' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'settings' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Configuración</span>
                </a>

                <!-- 5. Productos -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'products']) }}"
                   @click.prevent="currentTab = 'products'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=products')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'products' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'products' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span>Productos</span>
                </a>

                <!-- 6. Clientes -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'clients']) }}"
                   @click.prevent="currentTab = 'clients'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=clients')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'clients' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'clients' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Clientes</span>
                </a>

                <!-- 7. Pagos -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'payments']) }}"
                   @click.prevent="currentTab = 'payments'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=payments')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'payments' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'payments' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    <span>Pagos</span>
                </a>

                <!-- 8. Paramétricas -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'parametrics']) }}"
                   @click.prevent="currentTab = 'parametrics'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=parametrics')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'parametrics' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'parametrics' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                    </svg>
                    <span>Paramétricas</span>
                </a>

                <!-- 9. Sucursales -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'branches']) }}"
                   @click.prevent="currentTab = 'branches'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=branches')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'branches' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'branches' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Sucursales</span>
                </a>

                <!-- 10. Plantillas -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'templates']) }}"
                   @click.prevent="currentTab = 'templates'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=templates')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'templates' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'templates' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                    <span>Plantillas</span>
                </a>

                <!-- 11. Documentos Sectoriales -->
                <a href="{{ route('admin.companies.profile', [$company->id, 'tab' => 'documents']) }}"
                   @click.prevent="currentTab = 'documents'; window.history.replaceState(null, '', '{{ route('admin.companies.profile', $company->id) }}?tab=documents')"
                   class="pb-sidebar-link px-4 py-2 flex flex-row items-center rounded mx-2 text-xs cursor-pointer"
                   :class="{ 'active': currentTab === 'documents' }">
                    <svg class="w-4 h-4 mr-3 flex-shrink-0" :style="currentTab === 'documents' ? 'color: #38bdf8;' : 'color: #9ca3af;'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Documentos</span>
                </a>
            </div>
        @else
            <!-- GENERAL ADMIN SIDEBAR -->
            <div class="mt-2 mb-4 flex-1">
                <ul class="space-y-1">
                    @foreach($sidebar as $row)
                    <a href="{{ route($row['url']) }}">
                        <li class="pb-sidebar-link px-6 py-3 flex flex-row items-center text-sm {{ isActive($row['url'], false) ? 'active' : '' }}">
                            <img src="{{ asset('images/svg/' . $row['icon'] . '.svg') }}" class="w-5 h-5 {{ isActive($row['url'], false) ? 'opacity-100' : 'opacity-70' }}" alt="" />
                            <span class="ml-4">{{ $row['title'] }}</span>
                        </li>
                    </a>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Logout Action -->
        <div class="mt-auto mb-4 px-4 space-y-2 pt-3" style="border-top: 1px solid #4a4a4a;">
            <a href="{{ route('admin.logout') }}" class="pb-btn-logout flex items-center justify-center w-full px-4 py-2 font-medium text-xs rounded group">
                <svg class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Cerrar Sesión
            </a>
        </div>
    </nav>
</div>