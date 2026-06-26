@php
    $resolveNav = function (array $item): array {
        $routeName = $item['route'];

        $href = match (true) {
            $routeName === '#' => '#',
            \Illuminate\Support\Facades\Route::has($routeName) => route($routeName),
            default => $routeName,
        };

        $isActive = match (true) {
            $routeName === 'main' => request()->routeIs('main'),
            $routeName === 'news.index' => request()->routeIs('news.*'),
            $routeName === 'contact.index' => request()->routeIs('contact.*'),
            default => false,
        };

        return compact('href', 'isActive');
    };
@endphp

<header class="sticky top-0 z-40 border-b border-border bg-surface/95 backdrop-blur-sm">
    <div class="container-site">
        <div class="grid grid-cols-[auto_1fr_auto] items-center gap-4 py-3.5">
            <a href="{{ route('main') }}" class="flex shrink-0 items-center gap-2.5">
                <x-layout.logo class="size-11" />
                <div class="hidden sm:block">
                    <span class="block text-base font-bold leading-tight text-secondary">{{ __('site.name') }}</span>
                    <span class="block text-[11px] text-text-muted">{{ __('site.tagline') }}</span>
                </div>
            </a>

            <nav class="hidden items-center justify-center gap-0.5 xl:flex" aria-label="{{ __('site.nav.home') }}">
                @foreach ($menuItems as $item)
                    @php($nav = $resolveNav($item))

                    @if (! empty($item['children']))
                        <div class="nav-dropdown" data-nav-dropdown>
                            <button
                                type="button"
                                data-nav-dropdown-trigger
                                @class([
                                    'nav-link nav-dropdown-trigger',
                                    'nav-link-active' => $nav['isActive'],
                                ])
                                aria-expanded="false"
                                aria-haspopup="true"
                            >
                                {{ __($item['label']) }}
                                <x-icons.chevron-down class="nav-dropdown-chevron size-3.5" />
                            </button>

                            <div class="nav-dropdown-menu" role="menu">
                                @foreach ($item['children'] as $child)
                                    @php($childNav = $resolveNav($child))
                                    <a
                                        href="{{ url($childNav['href']) }}"
                                        role="menuitem"
                                        @class(['nav-dropdown-item', 'nav-dropdown-item-active' => $childNav['isActive']])
                                    >
                                        {{ __($child['label']) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ url($nav['href']) }}" @class(['nav-link', 'nav-link-active' => $nav['isActive']])>
                            {{ __($item['label']) }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('donate') }}" class="btn-primary hidden sm:inline-flex">
                    <x-icons.heart class="size-4" />
                    {{ __('site.nav.donate') }}
                </a>

                <button
                    id="mobile-nav-toggle"
                    type="button"
                    class="inline-flex size-10 items-center justify-center rounded-theme text-secondary transition-colors hover:bg-surface-muted xl:hidden"
                    aria-expanded="false"
                    aria-controls="mobile-nav-menu"
                    aria-label="{{ __('site.nav.menu') }}"
                >
                    <x-icons.menu id="mobile-nav-icon-open" class="size-6" />
                    <x-icons.x id="mobile-nav-icon-close" class="hidden size-6" />
                </button>
            </div>
        </div>

        <nav
            id="mobile-nav-menu"
            class="mobile-nav-panel xl:hidden"
            aria-label="Mobile"
            aria-hidden="true"
        >
            <div class="mobile-nav-panel-inner">
                <div class="flex flex-col gap-1 py-3">
                    @foreach ($menuItems as $item)
                        @php($nav = $resolveNav($item))

                        @if (! empty($item['children']))
                            <div data-mobile-nav-dropdown>
                                <button
                                    type="button"
                                    data-mobile-nav-dropdown-toggle
                                    @class([
                                        'mobile-nav-link w-full',
                                        'mobile-nav-link-active' => $nav['isActive'],
                                    ])
                                    aria-expanded="false"
                                >
                                    <span>{{ __($item['label']) }}</span>
                                    <x-icons.chevron-down class="mobile-nav-dropdown-chevron size-4 opacity-40" />
                                </button>

                                <div class="mobile-nav-submenu" hidden>
                                    @foreach ($item['children'] as $child)
                                        @php($childNav = $resolveNav($child))
                                        <a
                                            href="{{ $childNav['href'] }}"
                                            data-mobile-nav-link
                                            @class([
                                                'mobile-nav-sublink',
                                                'mobile-nav-sublink-active' => $childNav['isActive'],
                                            ])
                                        >
                                            {{ __($child['label']) }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a
                                href="{{ $nav['href'] }}"
                                data-mobile-nav-link
                                @class([
                                    'mobile-nav-link',
                                    'mobile-nav-link-active' => $nav['isActive'],
                                ])
                            >
                                <span>{{ __($item['label']) }}</span>
                                @if ($nav['isActive'])
                                    <span class="mobile-nav-link-dot" aria-hidden="true"></span>
                                @else
                                    <x-icons.arrow-forward class="size-4 opacity-40" />
                                @endif
                            </a>
                        @endif
                    @endforeach
                </div>

                <div class="border-t border-border py-3">
                    <a href="{{ route('donate') }}" data-mobile-nav-link class="btn-primary w-full">
                        <x-icons.heart class="size-4" />
                        {{ __('site.nav.donate') }}
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>
