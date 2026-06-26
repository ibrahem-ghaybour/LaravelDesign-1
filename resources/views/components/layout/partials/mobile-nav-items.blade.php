@foreach ($children as $child)
    @php($childNav = $resolveNav($child))

    @if (! empty($child['children']))
        <div data-mobile-nav-dropdown>
            <button
                type="button"
                data-mobile-nav-dropdown-toggle
                @class([
                    'mobile-nav-sublink w-full',
                    'mobile-nav-sublink-active' => $childNav['isActive'],
                ])
                aria-expanded="false"
            >
                <span>{{ __($child['label']) }}</span>
                <x-icons.chevron-down class="mobile-nav-dropdown-chevron size-4 opacity-40" />
            </button>

            <div class="mobile-nav-submenu" hidden>
                @include('components.layout.partials.mobile-nav-items', [
                    'children' => $child['children'],
                    'resolveNav' => $resolveNav,
                ])
            </div>
        </div>
    @else
        <a
            href="{{ url($childNav['href']) }}"
            data-mobile-nav-link
            @class([
                'mobile-nav-sublink',
                'mobile-nav-sublink-active' => $childNav['isActive'],
            ])
        >
            {{ __($child['label']) }}
        </a>
    @endif
@endforeach
