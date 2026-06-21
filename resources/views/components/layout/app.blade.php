<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }} — {{ __('site.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    <x-layout.top-bar />
    <x-layout.navbar />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-layout.footer />

    <button
        id="back-to-top"
        type="button"
        class="fixed bottom-6 end-6 z-50 flex size-11 translate-y-4 items-center justify-center rounded-full bg-primary text-text-inverse opacity-0 shadow-lg transition-all duration-300 pointer-events-none hover:scale-110 hover:bg-primary-hover"
        aria-label="{{ __('site.footer.back_to_top') }}"
    >
        <x-icons.arrow-up class="size-5" />
    </button>
</body>
</html>
