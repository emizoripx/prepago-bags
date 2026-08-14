<div
    class="main_layout h-screen flex overflow-hidden bg-slate-50 transition-colors duration-300"
    x-data="{ sidebarOpen: false }"
    @keydown.window.escape="sidebarOpen = false"
    id="main-sidebar">

    @include('prepagobags::components.sidebar.desktop')
    <div class="flex flex-col w-0 flex-1 overflow-hidden">
        
         
        
        
        <main
            class="flex-1 relative z-0 overflow-y-auto pt-6 focus:outline-none bg-slate-50 transition-colors duration-300"
            tabindex="0" x-data
            x-init="$el.focus()" >

            <div class="mx-auto px-4 sm:px-6 md:px-8">
                @yield('header')
            </div>

            <div class="mx-auto px-4 sm:px-6 md:px-8 ">
                <div class="pt-4 py-6">
                    @includeWhen(session()->has('success'), 'prepagobags::components.messages.success')
                    
                    {{ $slot }}
                </div>
            </div>
        </main>
        
    </div>
</div>

<script>

</script>
