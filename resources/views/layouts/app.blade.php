<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mạng XH</title>

    {{-- Link CSS --}}
    <link rel="stylesheet" href="{{ asset('public/css/index.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/story.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/sidebar.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
</head>

<body>

    {{-- Navbar --}}
    @include('layouts.navbar')

    @yield('full')

    <div class="container">
        @if (Route::currentRouteName() == 'trangchu' && !Request::is('admin*'))
        @include('layouts.sidebar-left')
        @endif

        <main class="feed1">
            @yield('content')
        </main>

        @if (Route::currentRouteName() == 'trangchu' && !Request::is('admin*'))
        @include('layouts.sidebar-right')
        @endif
    </div>


    <script src="{{ asset('public/js/sangtoi.js') }}"></script>
    <script src="{{ asset('public/js/sidebar.js') }}"></script>
</body>

<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>

</html>