 <?php
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
            ];

        ?>
<div>
      <nav class="flex flex-col bg-[#404040] w-64 h-screen shadow-md transition-colors duration-300">
        <div class="flex items-center justify-center h-16 bg-white w-full border-b border-gray-200 shadow-sm">
             <span class="font-bold text-gray-800 text-xl tracking-wider">emizor</span>
        </div>
        <div class="flex flex-col mt-4 mb-4 pb-4 border-b border-[#555555] mx-4">
            <span class="text-[#cccccc] text-xs font-semibold uppercase tracking-wider mb-1">Usuario</span>
            <span class="font-medium text-white text-sm truncate" title="Super Administrador">Super Administrador</span>
            <span class="text-[10px] text-emerald-400 font-semibold mt-1">Activo</span>
        </div>
        <div class="mt-2 mb-4 flex-1">
          <ul class="space-y-1">
            @foreach($sidebar as $row)
            <a href="{{ route($row['url']) }}">
            <li class="px-6 py-3 flex flex-row items-center transition-all duration-200 {{ isActive($row['url'], false) ? 'bg-[#333333] text-white border-l-4 border-blue-500 font-medium' : 'text-[#cccccc] hover:bg-[#4d4d4d] hover:text-white font-normal' }} ">
              <img src="{{ asset('images/svg/' . $row['icon'] . '.svg') }}" class="w-5 h-5 {{ isActive($row['url'], false) ? 'opacity-100' : 'opacity-70' }}" alt="" />
                <span class="ml-4">{{ $row['title'] }}</span>
              </li>
            </a>
            @endforeach
          </ul>
        </div>

        <div class="mt-auto mb-6 px-4 space-y-3">
            <a href="{{ route('admin.logout') }}" class="flex items-center justify-center w-full px-4 py-2 bg-[#333333] border border-[#555555] text-[#cccccc] font-medium rounded-md hover:bg-[#2a2a2a] hover:text-red-400 hover:border-[#666666] transition-all duration-300 group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Cerrar Sesión
            </a>
        </div>
      </nav>
    </div>