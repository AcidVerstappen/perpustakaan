<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Perpustakaan') }} - @yield('title', 'Autentikasi')</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <i class="bi bi-book-half text-success fs-1"></i>
                    <h4 class="mt-2 mb-0 fw-bold">{{ config('app.name') }}</h4>
                    <p class="text-muted small mb-0">@yield('subtitle', 'Masuk ke akun Anda')</p>
                </div>
                @include('partials.admin.alerts')
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
