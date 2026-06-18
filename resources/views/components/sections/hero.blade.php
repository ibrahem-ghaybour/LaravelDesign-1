@props(['slides'])

<section id="hero-slider" class="relative h-[70vh] min-h-[480px] max-h-[720px] overflow-hidden bg-secondary">
    @foreach ($slides as $index => $slide)
        <div
            data-slide
            @class([
                'absolute inset-0 transition-opacity duration-700',
                'opacity-100 z-10' => $index === 0,
                'opacity-0' => $index !== 0,
            ])
        >
            <img
                src="{{ asset($slide['image']) }}"
                alt=""
                class="size-full object-cover"
                @if ($index > 0) loading="lazy" @endif
            >
            <div class="hero-overlay absolute inset-0"></div>
        </div>
    @endforeach

    <div class="container-site relative z-20 flex h-full items-center">
        <div class="max-w-xl text-text-inverse">
            @foreach ($slides as $index => $slide)
                <div
                    data-slide-content
                    @class(['transition-opacity duration-500', 'hidden' => $index !== 0])
                >
                    <h1 class="text-4xl font-extrabold leading-tight md:text-5xl lg:text-6xl text-balance">
                        {{ __($slide['title_key']) }}
                        <span class="text-primary">{{ __($slide['title_highlight_key']) }}</span>
                    </h1>
                    <p class="mt-5 text-base leading-relaxed text-white/90 md:text-lg">
                        {{ __($slide['subtitle_key']) }}
                    </p>
                </div>
            @endforeach

            <div class="mt-8 flex flex-wrap gap-4">
                <a href="#" class="btn-primary">
                    <x-icons.heart class="size-4" />
                    {{ __('site.hero.donate') }}
                </a>
                <a href="#" class="btn-outline">
                    {{ __('site.hero.learn_more') }}
                </a>
            </div>
        </div>
    </div>

    {{-- Slider controls --}}
    <button data-prev type="button" class="absolute start-4 top-1/2 z-30 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-text-inverse backdrop-blur-sm hover:bg-white/30 transition-colors" aria-label="Previous">
        <x-icons.arrow-left class="size-5" />
    </button>
    <button data-next type="button" class="absolute end-4 top-1/2 z-30 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-text-inverse backdrop-blur-sm hover:bg-white/30 transition-colors" aria-label="Next">
        <x-icons.arrow-right class="size-5" />
    </button>

    <div class="absolute bottom-6 inset-x-0 z-30 flex justify-center gap-2">
        @foreach ($slides as $index => $slide)
            <button
                data-dot
                type="button"
                @class([
                    'size-2.5 rounded-full transition-colors',
                    'bg-primary' => $index === 0,
                    'bg-white/50' => $index !== 0,
                ])
                aria-label="Slide {{ $index + 1 }}"
            ></button>
        @endforeach
    </div>
</section>
