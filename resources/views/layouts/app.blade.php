<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')

</head>

<body>

    @include('layouts.sidebar')

    <div class="main-wrapper">

        @include('layouts.navbar')

        <div class="content">

            @yield('content')

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Leaflet JS -->
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    @stack('scripts')
    @yield('scripts')

    <!-- Twemoji JS -->
    <script src="https://cdn.jsdelivr.net/npm/twemoji@14.0.2/dist/twemoji.min.js" crossorigin="anonymous"></script>
    <style>
        img.emoji {
            height: 1.2em;
            width: 1.2em;
            margin: 0 .05em 0 .1em;
            vertical-align: -0.1em;
            border-radius: 2px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (typeof twemoji !== 'undefined') {
                twemoji.parse(document.body, {
                    folder: 'svg',
                    ext: '.svg',
                    base: 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/'
                });
            }
        });
    </script>
</body>

</html>