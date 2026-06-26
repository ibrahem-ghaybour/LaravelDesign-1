@foreach ($children as $child)
    @php($childNav = $resolveNav($child))

    @if (! empty($child['children']))
        <div class="nav-submenu" data-nav-submenu>
            <button
                type="button"
                class="nav-dropdown-item nav-submenu-trigger"
                aria-expanded="false"
                aria-haspopup="true"
            >
                <span>{{ __($child['label']) }}</span>
                <x-icons.arrow-forward class="nav-submenu-chevron size-3.5 opacity-50" />
            </button>

            <div class="nav-submenu-menu" role="menu">
                @include('components.layout.partials.nav-dropdown-items', [
                    'children' => $child['children'],
                    'resolveNav' => $resolveNav,
                ])
            </div>
        </div>
    @else
        <a
            href="{{ url($childNav['href']) }}"
            role="menuitem"
            @class(['nav-dropdown-item', 'nav-dropdown-item-active' => $childNav['isActive']])
        >
            {{ __($child['label']) }}
        </a>
    @endif
@endforeach
