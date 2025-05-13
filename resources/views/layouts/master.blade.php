<!DOCTYPE html>
<html lang="{{ \App::getLocale() }}">
<head>
    @include('LaravelSaas::commons.head', [
        'title' => 'LaravelSaas',
    ])

    {{-- Laravel Mix - CSS File --}}
    {{-- <link rel="stylesheet" href="{{ mix('css/laravel-saas.css') }}"> --}}
</head>

<body>
    <div class="position-relative">
        @yield('content')

        @include('LaravelSaas::commons.toast')
    </div>

    @yield('bodyjs')

    {{-- Laravel Mix - JS File --}}
    {{-- <script src="{{ mix('js/laravel-saas.js') }}"></script> --}}
</body>
</html>

