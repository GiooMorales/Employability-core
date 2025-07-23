<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Employability Core')</title>
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
    @if(Auth::check())
        <meta name="user-id" content="{{ Auth::user()->id_usuarios }}">
    @endif
</head>
<body class="@yield('body-class')">
    <div class="container">
        @include('partials.header')
        <div class="content">
            @include('partials.sidebar')
            
            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/search.js') }}"></script>
    @stack('scripts')
    @yield('scripts')
</body>
</html> 