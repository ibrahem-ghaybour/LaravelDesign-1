@props(['slides'])

<section id="hero-slider" class="relative h-[75vh] min-h-[520px] max-h-[760px] overflow-hidden bg-secondary">
    @foreach ($slides as $index => $slide)
        <div
            data-slide
            @class([
                'absolute inset-0 transition-opacity duration-1000 ease-in-out',
                'opacity-100 z-10' => $index === 0,
                'opacity-0' => $index !== 0,
            ])
        >
            <img 
                
                src="{{ url('images/images/' . ($slide['image'] ?? '')) }}"
                alt=""
                @class([
                    'hero-slide-img size-full object-cover',
                    'is-active' => $index === 0,
                ])
                @if ($index > 0) loading="lazy" @endif
            >
            <div class="hero-overlay absolute inset-0"></div>
        </div>
    @endforeach

    <div class="container-site relative z-20 flex h-full items-center">
        <div class="max-w-lg text-text-inverse">
            @foreach ($slides as $index => $slide)
                <div
                    data-slide-content
                    @class(['transition-opacity duration-500', 'hidden' => $index !== 0, 'hero-content-enter' => $index === 0])
                >
                    <h1 class="text-4xl font-extrabold leading-tight md:text-5xl lg:text-[3.25rem] text-balance">
                        {!! $slide['title'] !!}
                        <br>
                        <span class="text-primary">{!! $slide['title_highlight'] !!}</span>
                    </h1>
                    <p class="mt-5 max-w-md text-base leading-relaxed text-white/90 md:text-lg">
                        {!! $slide['subtitle'] !!}
                    </p>
                </div>
            @endforeach

            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('donate') }}" class="btn-primary">
                    <x-icons.heart class="size-4" />
                    {{ __('site.hero.donate') }}
                </a>
                <a href="#" class="btn-outline">
                    {{ __('site.hero.learn_more') }}
                </a>
            </div>
        </div>
    </div>

    <button data-prev type="button" class="absolute start-4 top-1/2 z-30 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-text-inverse backdrop-blur-sm transition-all duration-200 hover:scale-110 hover:bg-white/30" aria-label="Previous">
        <x-icons.arrow-back class="size-5" />
    </button>
    <button data-next type="button" class="absolute end-4 top-1/2 z-30 flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/20 text-text-inverse backdrop-blur-sm transition-all duration-200 hover:scale-110 hover:bg-white/30" aria-label="Next">
        <x-icons.arrow-forward class="size-5" />
    </button>

    <div class="absolute inset-x-0 bottom-6 z-30 flex justify-center gap-2.5">
        @foreach ($slides as $index => $slide)
            <button
                data-dot
                type="button"
                @class([
                    'size-2.5 rounded-full transition-all duration-300',
                    'bg-primary scale-125' => $index === 0,
                    'bg-white/50 hover:bg-white/70' => $index !== 0,
                ])
                aria-label="Slide {{ $index + 1 }}"
            ></button>
        @endforeach
    </div>
</section>
