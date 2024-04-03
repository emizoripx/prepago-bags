<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <!-- Error: {{ session('error') }} -->
        <title>@yield('meta_title', '')</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="@yield('meta_description')"/>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script src="{{ asset('vendor/alpinejs@2.8.2/alpine.js') }}" defer></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;1,100;1,300;1,400;1,700&display=swap" rel="stylesheet">

        <!-- Fonts -->
        <style>
           
        </style>
        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('favicon.png') }}" rel="shortcut icon" type="image/png">
        <link rel="canonical" href="{{ config('ninja.site_url') }}/{{ request()->path() }}"/>

        @livewireStyles

        {{-- Feel free to push anything to header using @push('header') --}}
        @stack('head')
    </head>

    <body class="antialiased">
        @if(session()->has('message'))
            <div class="py-1 text-sm text-center text-white bg-primary disposable-alert">
                {{ session('message') }}
            </div>
        @endif

        @component('prepagobags::components.sidebar.main')
            @yield('body')
        @endcomponent
        @livewire('livewire-ui-modal')
        @livewireScripts

    </body>

    <footer>
        @yield('footer')
        @stack('footer')
    </footer>
</html>
