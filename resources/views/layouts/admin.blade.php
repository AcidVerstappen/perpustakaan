<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Perpustakaan') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        @include('partials.admin.sidebar')
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        @include('partials.admin.navbar')

        <main class="admin-content">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    <h4 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h4>
                    @hasSection('breadcrumb')
                        <nav aria-label="breadcrumb" class="mt-1">
                            <ol class="breadcrumb mb-0 small">
                                @yield('breadcrumb')
                            </ol>
                        </nav>
                    @endif
                </div>
            </div>

            @include('partials.admin.alerts')
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
