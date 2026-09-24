<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#fff6f8">
    <meta name="description" content="Ha Thu Perfume — Khám phá nước hoa chính hãng, chọn hương thơm mang dấu ấn của riêng bạn.">
    <title>@yield('title', 'Ha Thu Perfume · Hương thơm của riêng bạn')</title>
    <link rel="icon" href="{{ asset('images/ha-thu-mark.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="store-page boutique-store" style="--ht-auth-image:url('{{ asset('images/products/narciso-musc-noir-rose.jpg') }}')">
    @include('partials.store-header')
    <main class="public-main" id="main-content" tabindex="-1">
        @if(session('success'))<div class="store-container public-flash" role="status">✓ {{ session('success') }}</div>@endif
        @if(session('error'))<div class="store-container public-flash alert-danger" role="alert">{{ session('error') }}</div>@endif
        @yield('content')
    </main>
    @include('partials.store-footer')
    @include('partials.chat_popup')
    {{-- Shared theme follows the existing page-specific styles. --}}
    @vite('resources/css/boutique.css')
    @stack('scripts')
</body>
</html>