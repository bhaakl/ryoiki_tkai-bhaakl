<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite([
        'resources/sass/app.scss',
        'resources/sass/admin.scss',
        'resources/js/app.js',
        'resources/js/admin.js'
    ])
</head>
<body class="admin-body">
    @include('admin/shared/navbar')

    
    <div class="container flex-grow-1">
        @include('shared/alerts')

        <x-card>
            @yield('content')
        </x-card>
    </div>
</body>
</html>
