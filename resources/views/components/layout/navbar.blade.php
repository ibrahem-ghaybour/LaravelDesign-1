<header class="sticky top-0 z-40 border-b border-border bg-surface/95 backdrop-blur-sm">
    <div class="container-site">
        <div class="grid grid-cols-[auto_1fr_auto] items-center gap-4 py-3.5">
            {{-- Logo (start in RTL) --}}
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5">
                <x-layout.logo class="size-11" />
                <div class="hidden sm:block">
                    <span class="block text-base font-bold leading-tight text-secondary">{{ __('site.name') }}</span>
                    <span class="block text-[11px] text-text-muted">{{ __('site.tagline') }}</span>
                </div>
            </a>

            {{-- Desktop navigation (centered) --}}
            <nav class="hidden items-center justify-center gap-0.5 xl:flex" aria-label="{{ __('site.nav.home') }}">
                @foreach (config('site.nav') as $item)
                    @php
                        $isActive = ($item['route'] === 'home' && request()->routeIs('home'));
                        $href = $item['route'] === 'home' ? route('home') : $item['route'];
                    @endphp
                    <a href="{{ $href }}" @class(['nav-link', 'nav-link-active' => $isActive])>
                        {{ __($item['label']) }}
                    </a>
                @endforeach
            </nav>

            {{-- Donate CTA + Mobile toggle (end in RTL) --}}
            <div class="flex items-center justify-end gap-3">
                <a href="#" class="btn-primary hidden sm:inline-flex">
                    <x-icons.heart class="size-4" />
                    {{ __('site.nav.donate') }}
                </a>

                <button
                    id="mobile-nav-toggle"
                    type="button"
                    class="inline-flex items-center justify-center rounded-theme p-2 text-secondary xl:hidden"
                    aria-expanded="false"
                    aria-controls="mobile-nav-menu"
                >
                    <x-icons.menu class="size-6" />
                </button>
            </div>
        </div>

        {{-- Mobile navigation --}}
        <nav id="mobile-nav-menu" class="hidden border-t border-border pb-4 xl:hidden" aria-label="Mobile">
            <div class="flex flex-col gap-1 pt-3">
                @foreach (config('site.nav') as $item)
                    @php
                        $isActive = ($item['route'] === 'home' && request()->routeIs('home'));
                        $href = $item['route'] === 'home' ? route('home') : $item['route'];
                    @endphp
                    <a
                        href="{{ $href }}"
                        @class([
                            'rounded-theme px-3 py-2.5 text-sm font-medium transition-colors',
                            'bg-primary-light text-primary' => $isActive,
                            'text-text hover:bg-surface-muted' => ! $isActive,
                        ])
                    >
                        {{ __($item['label']) }}
                    </a>
                @endforeach
                <a href="#" class="btn-primary mt-2 w-full sm:hidden">
                    <x-icons.heart class="size-4" />
                    {{ __('site.nav.donate') }}
                </a>
            </div>
        </nav>
    </div>
</header>
