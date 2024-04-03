<div class="container px-4 mx-auto">

        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight text-white">Facturas</h2>
            </div>
            <div class="my-2 flex sm:flex-row flex-col">
                <div class="flex flex-row mb-1 sm:mb-0">
                  
                     <div class="relative w-40">
                        <select wire:model="prefactura"
                            class="h-full rounded-l border block w-full bg-[#2a343f] border-gray-400 text-white py-2 px-4 pr-8 leading-tight focus:outline-none ">
                            <option value="inactive" selected >Facturas</option>
                            <option value="active" >Prefacturas</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        </div>
                    </div>
                </div>
                <div class="block relative w-full">
                    <span class="h-full absolute inset-y-0 left-0 flex items-center pl-2">
                        <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                            <path
                                d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                            </path>
                        </svg>
                    </span>
                    <input wire:model.debounce.500ms="search" placeholder="Buscar por CUF / Número documento / Razón social" class="appearance-none rounded-r rounded-l sm:rounded-l-none border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-[#2a343f] text-sm placeholder-gray-400 text-white focus:placeholder-gray-600 focus:text-white focus:outline-none" />
                </div>
            </div>
        </div>


    <div class="flex flex-col">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden border border-gray-200 dark:border-gray-700 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                            @if($prefactura == "inactive")
                                <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Estado
                                </th>
                            @endif
                                <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    #
                                </th>

                                <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Fecha emisión
                                </th>

                                <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Razón Social
                                </th>
                                <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Número Documento
                                </th>

                                <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Monto total
                                </th>
                                <th scope="col" class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                    Compañia
                                </th>

                                <th scope="col" class="relative py-3 px-4">
                                    <span class="sr-only">Acciones</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-500 dark:bg-[#1f2937b8]">
                            @forelse($invoices as $invoice)
                            <tr>
                            @if($prefactura == "inactive")
                                <td class="px-4 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap">
                                    
                                     {!! \EmizorIpx\ClientFel\Models\FelInvoiceRequest::getStatusText($invoice->codigoEstado) !!}
                                    
                                </td>
                                @endif
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ (int)$invoice['numeroFactura'] != 0 ? (int)$invoice['numeroFactura'] : "Prefactura-".(int)$invoice['prefactura_number'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ $invoice['fechaEmision'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ $invoice['nombreRazonSocial'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ $invoice['numeroDocumento'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300 whitespace-nowrap">{{ number_format($invoice['amount'],2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-300" title="{{ trim($invoice['company_name'],'"')}}">
                                    <a href="{{route('admin.companies.invoices', $invoice['companyid'])}}" class="text-blue-400"> 
                                    {{ \Illuminate\Support\Str::limit(trim($invoice['company_name'],'"'), $limit = 15, $end = '...')  }} 
                                    </a>
                                </td>
                                <td class="px-4 py-3 inline-table" >
                                    <div class="absolute z-10" x-data="{ isOpen: false }">
                                          <button @click="isOpen = !isOpen" class=" text-sm font-medium text-center text-gray-900 bg-white rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none dark:text-white focus:ring-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-600" type="button"> 
     
                                            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 4 15">
                                                <path d="M3.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 6.041a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm0 5.959a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                                            </svg>
                                        </button>

                                        <div x-show="isOpen" @click.away="isOpen = false" class="absolute right-10 bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600 inline-table z-70">
      
                                            <ul class="py-2 text-sm text-gray-700 dark:text-gray-200 " >
                                                <li>
                                                    
                                                </li>
                                            </ul>
                                          
                                        </div>
                                    </div>
                                    
                                </td>
                            </tr>

                            @empty
                                <tr class="group center text-white">
                                    <td class="px-6 py-1 whitespace-nowrap text-sm leading-5 " colspan="100%">
                                        {{ ctrans('texts.no_results') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
 
  

        <nav role="navigation" aria-label="Pagination Navigation" class="flex">
            <span>
                {{-- Previous Page Link --}}
                @if ($onFirstPage)
                     <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-[#1f2937] border border-gray-500 cursor-default leading-5 rounded-md mr-4 mt-5 text-gray-500">
                        {!! __('pagination.previous') !!}
                    </span>
                @else
                    <button wire:click="changePage(false)" wire:loading.attr="disabled" rel="prev" class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-[#1f2937] border border-[#6b7280] leading-5 rounded-md text-gray-100 hover:text-white mr-4 mt-5">
                        {!! __('pagination.previous') !!}
                    </button>
                @endif
            </span>
 
            <span>
                {{-- Next Page Link --}}
                @if ($hasMorePages)
                    <button wire:click="changePage(true)" wire:loading.attr="disabled" rel="next" class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-[#1f2937] border border-[#6b7280] leading-5 rounded-md text-gray-100 hover:text-white mr-4 mt-5">
                        {!! __('pagination.next') !!}
                    </button>
                @else
                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium bg-[#1f2937] border border-gray-500 cursor-default leading-5 rounded-md mr-4 mt-5 text-gray-500">
                        {!! __('pagination.next') !!}
                    </span>
                @endif
            </span>
        </nav>


</div>