<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', config('app.name'))</title>
    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">
    @yield('styles')
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <main class="px-4">
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
