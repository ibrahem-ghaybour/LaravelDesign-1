@props(['title', 'subtitle' => null])

<section class="relative overflow-hidden bg-secondary py-12 md:py-14">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -start-20 -top-20 size-64 rounded-full bg-primary"></div>
        <div class="absolute -bottom-16 -end-16 size-48 rounded-full bg-primary"></div>
    </div>

    <div class="container-site relative">
        <nav class="mb-4 flex items-center gap-2 text-sm text-white/70" aria-label="Breadcrumb">
            <a href="{{ route('main') }}" class="transition-colors hover:text-primary">{{ __('site.nav.home') }}</a>
            <x-icons.arrow-forward class="size-3.5" />
            <span class="text-white">{{ $title }}</span>
        </nav>

        <h1 class="text-3xl font-bold text-text-inverse md:text-4xl">{{ $title }}</h1>

        @if ($subtitle)
            <p class="mt-3 max-w-2xl text-base text-white/80">{{ $subtitle }}</p>
        @endif

        <div class="section-heading-line ms-0 mt-4"></div>
    </div>
</section>
