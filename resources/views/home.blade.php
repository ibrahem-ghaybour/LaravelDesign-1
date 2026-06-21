<x-layout.app :title="__('site.nav.home')">
    <x-sections.hero :slides="$heroSlides" />

    {{-- Statistics Bar --}}
    <section class="relative z-10 -mt-20 pb-4">
        <div class="container-site reveal">
            <div class="stats-bar grid grid-cols-2 divide-x divide-y divide-border sm:divide-y-0 lg:grid-cols-4 rtl:divide-x-reverse">
                @foreach ($stats as $index => $stat)
                    <x-ui.stat-card
                        :icon="$stat['icon']"
                        :value="$stat['value']"
                        :label="__($stat['label_key'])"
                        :count="$stat['count'] ?? null"
                        :suffix="$stat['suffix'] ?? ''"
                        :prefix="$stat['prefix'] ?? ''"
                        @class(['reveal', 'reveal-delay-' . min($index + 1, 4)])
                    />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Programs --}}
    <section class="section-padding bg-surface">
        <div class="container-site">
            <div class="reveal">
                <x-ui.section-heading :title="__('site.programs.title')" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($programs as $index => $program)
                    <x-ui.program-card
                        :icon="$program['icon']"
                        :title="__($program['title_key'])"
                        @class(['reveal', 'reveal-delay-' . min(($index % 3) + 1, 3)])
                    />
                @endforeach
            </div>

            <div class="mt-10 text-center reveal">
                <a href="#" class="btn-secondary">{{ __('site.programs.view_all') }}</a>
            </div>
        </div>
    </section>

    {{-- Projects --}}
    <section class="section-padding bg-surface-muted">
        <div class="container-site">
            <div class="reveal">
                <x-ui.section-heading :title="__('site.projects.title')" />
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $index => $project)
                    <x-ui.project-card
                        :image="$project['image']"
                        :title="__($project['title_key'])"
                        :location="__($project['location_key'])"
                        :progress="$project['progress']"
                        @class(['reveal', 'reveal-delay-' . min($index + 1, 3)])
                    />
                @endforeach
            </div>

            <div class="mt-10 text-center reveal">
                <a href="#" class="btn-primary">{{ __('site.projects.view_all') }}</a>
            </div>
        </div>
    </section>

    {{-- News --}}
    <section class="section-padding bg-surface">
        <div class="container-site">
            <div class="reveal">
                <x-ui.section-heading :title="__('site.news.title')" />
            </div>

            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($news as $index => $item)
                    <x-ui.news-card
                        :image="$item['image']"
                        :date="__($item['date_key'])"
                        :title="__($item['title_key'])"
                        @class(['reveal', 'reveal-delay-' . min($index + 1, 4)])
                    />
                @endforeach
            </div>

            <div class="mt-10 text-center reveal">
                <a href="#" class="btn-secondary">{{ __('site.news.view_all') }}</a>
            </div>
        </div>
    </section>

    {{-- Impact --}}
    <section class="relative overflow-hidden bg-secondary section-padding">
        <div class="absolute inset-0">
            <img src="{{ asset('images/impact-bg.jpg') }}" alt="" class="size-full object-cover opacity-15" loading="lazy">
            <div class="absolute inset-0 bg-secondary/80"></div>
        </div>

        <div class="container-site relative">
            <div class="grid items-center gap-12 lg:grid-cols-2">
                <div class="reveal text-text-inverse">
                    <h2 class="text-3xl font-bold leading-snug md:text-4xl lg:text-[2.75rem] text-balance">
                        {{ __('site.impact.title') }}
                        <span class="text-primary">{{ __('site.impact.title_highlight') }}</span>
                    </h2>
                </div>

                <div class="grid gap-8 sm:grid-cols-3">
                    @foreach ($impactSteps as $index => $step)
                        <div @class(['relative text-center text-text-inverse reveal', 'reveal-delay-' . min($index + 1, 3)])>
                            @if ($index < count($impactSteps) - 1)
                                <div class="absolute top-12 start-[calc(50%+2.5rem)] hidden h-px w-[calc(100%-5rem)] border-t-2 border-dashed border-primary/50 sm:block"></div>
                            @endif
                            <div class="impact-step-number" dir="ltr">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="impact-step-circle">
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
            <div class="reveal">
                <x-ui.section-heading :title="__('site.partners.title')" />
            </div>

            <div class="flex flex-wrap items-center justify-center gap-8 md:gap-14">
                @foreach ($partners as $index => $partner)
                    <div @class(['partner-logo reveal', 'reveal-delay-' . min($index + 1, 4)])>
                        <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }}" class="max-h-full max-w-full object-contain" loading="lazy">
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layout.app>
