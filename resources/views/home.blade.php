<x-layout.app :title="__('site.nav.home')">
    {{-- Hero Slider --}}
    <x-sections.hero :slides="$heroSlides" />

    {{-- Statistics --}}
    <section class="relative z-10 -mt-16 pb-4">
        <div class="container-site">
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($stats as $stat)
                    <x-ui.stat-card
                        :icon="$stat['icon']"
                        :value="$stat['value']"
                        :label="__($stat['label_key'])"
                    />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Programs --}}
    <section class="section-padding bg-surface">
        <div class="container-site">
            <x-ui.section-heading :title="__('site.programs.title')" />

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($programs as $program)
                    <x-ui.program-card
                        :icon="$program['icon']"
                        :title="__($program['title_key'])"
                    />
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="#" class="btn-secondary">{{ __('site.programs.view_all') }}</a>
            </div>
        </div>
    </section>

    {{-- Projects --}}
    <section class="section-padding bg-surface-muted">
        <div class="container-site">
            <x-ui.section-heading :title="__('site.projects.title')" />

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
                    <x-ui.project-card
                        :image="$project['image']"
                        :title="__($project['title_key'])"
                        :location="__($project['location_key'])"
                        :progress="$project['progress']"
                    />
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="#" class="btn-primary">{{ __('site.projects.view_all') }}</a>
            </div>
        </div>
    </section>

    {{-- News --}}
    <section class="section-padding bg-surface">
        <div class="container-site">
            <x-ui.section-heading :title="__('site.news.title')" />

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($news as $item)
                    <x-ui.news-card
                        :image="$item['image']"
                        :date="__($item['date_key'])"
                        :title="__($item['title_key'])"
                    />
                @endforeach
            </div>

            <div class="mt-10 text-center">
                <a href="#" class="btn-secondary">{{ __('site.news.view_all') }}</a>
            </div>
        </div>
    </section>

    {{-- Impact --}}
    <section class="relative overflow-hidden bg-secondary section-padding">
        <div class="absolute inset-0 opacity-20">
            <img src="{{ asset('images/impact-bg.jpg') }}" alt="" class="size-full object-cover" loading="lazy">
        </div>
        <div class="container-site relative">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="text-text-inverse">
                    <h2 class="text-3xl font-bold md:text-4xl text-balance">
                        {{ __('site.impact.title') }}
                        <span class="text-primary">{{ __('site.impact.title_highlight') }}</span>
                    </h2>
                    <p class="mt-4 text-base leading-relaxed text-white/80">
                        {{ __('site.impact.subtitle') }}
                    </p>
                </div>

                <div class="grid gap-8 sm:grid-cols-3">
                    @foreach ($impactSteps as $index => $step)
                        <div class="relative text-center text-text-inverse">
                            @if ($index < count($impactSteps) - 1)
                                <div class="absolute top-8 start-[calc(50%+2rem)] hidden h-px w-[calc(100%-4rem)] border-t-2 border-dashed border-white/30 sm:block"></div>
                            @endif
                            <div class="mx-auto flex size-16 items-center justify-center rounded-full border-2 border-primary bg-white/10 text-primary">
                                <x-dynamic-component :component="'icons.' . $step['icon']" class="size-7" />
                            </div>
                            <h3 class="mt-4 font-semibold">{{ __($step['title_key']) }}</h3>
                            <p class="mt-2 text-sm text-white/70">{{ __($step['desc_key']) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Partners --}}
    <section class="section-padding bg-surface-muted">
        <div class="container-site">
            <x-ui.section-heading :title="__('site.partners.title')" />

            <div class="flex flex-wrap items-center justify-center gap-8 md:gap-12">
                @foreach ($partners as $partner)
                    <div class="flex h-16 w-32 items-center justify-center grayscale transition-all hover:grayscale-0">
                        <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }}" class="max-h-full max-w-full object-contain" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layout.app>
