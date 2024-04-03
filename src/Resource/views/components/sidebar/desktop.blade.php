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
      <nav class="flex flex-col bg-[#1f2937] w-64 h-screen tex-gray-900 border border-gray-900">
        <div class="flex flex-wrap mt-8">
          <div class="w-1/2">
            <img
              src={{ asset('images/svg/user_other.svg') }}
              class="mx-auto w-20 h-20 rounded-full"
            />
          </div>
          <div class="w-1/2">
            <span class="font-semibold text-white">Super administrador</span>
            
          </div>
        </div>
        <div class="mt-10 mb-4">
          <ul class="ml-4">
            @foreach($sidebar as $row)
            <a href="{{ route($row['url']) }}">
            <li class="mb-2 px-4 py-4  flex flex-row  border-gray-300   hover:bg-[#343e4b] {{ isActive($row['url'], false) ? 'bg-[#343e4b] text-white' : 'text-gray-100' }} ">
              <img src="{{ asset('images/svg/' . $row['icon'] . '.svg') }}" class="w-5 h-5 fill-current text-white mr-3" alt="" />
                <span class="ml-2">{{ $row['title'] }}</span>
              </li>
            </a>
            @endforeach
          </ul>
        </div>
      </nav>
    </div>