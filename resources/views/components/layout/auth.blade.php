<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    data-theme="{{ $currentTheme ?? session('theme', config('themes.default')) }}"
    class="theme-animate"
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? __('site.auth.login') }} — {{ __('site.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-surface-muted">
    <header class="border-b border-border bg-surface/95 backdrop-blur-sm">
        <div class="container-site flex items-center justify-between py-4">
            <a href="{{ route('main') }}" class="flex items-center gap-2.5">
                <x-layout.logo class="size-10" />
                <span class="text-base font-bold text-secondary">{{ __('site.name') }}</span>
            </a>

            <div class="flex items-center gap-4">
                <a
                    href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-secondary transition-colors hover:text-primary"
                >
                    <x-icons.globe class="size-4" />
                    {{ __('site.locale.switch_to') }}
                </a>
                <a href="{{ route('main') }}" class="text-sm font-medium text-text-muted transition-colors hover:text-primary">
                    {{ __('site.auth.back_home') }}
                </a>
            </div>
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-12">
        {{ $slot }}
    </main>
</body>
</html>
