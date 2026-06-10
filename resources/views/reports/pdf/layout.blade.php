<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h1 { font-size: 16px; margin-bottom: 4px; }
        .meta { font-size: 10px; color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #198754; color: #fff; }
        tr:nth-child(even) { background: #f8f9fa; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }}</h1>
    <div class="meta">@yield('subtitle') | Dicetak: {{ $generatedAt->format('d/m/Y H:i') }}</div>
    @yield('content')
</body>
</html>
