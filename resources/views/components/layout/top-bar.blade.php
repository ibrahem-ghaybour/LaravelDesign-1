<div class="bg-topbar border-b border-border text-sm text-text-muted">
    <div class="container-site">
        <div class="flex flex-col items-center justify-between gap-3 py-2 sm:flex-row">
            {{-- Contact info --}}
            <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-1">
                <span class="inline-flex items-center gap-1.5">
                    <x-icons.map-pin class="size-4 text-primary" />
                    {{ __('site.contact.location') }}
                </span>
                <a href="mailto:{{ config('site.contact.email') }}" class="inline-flex items-center gap-1.5 hover:text-primary transition-colors">
                    <x-icons.mail class="size-4 text-primary" />
                    {{ config('site.contact.email') }}
                </a>
                <a href="tel:{{ config('site.contact.phone') }}" class="inline-flex items-center gap-1.5 hover:text-primary transition-colors" dir="ltr">
                    <x-icons.phone class="size-4 text-primary" />
                    {{ config('site.contact.phone') }}
                </a>
            </div>

            {{-- Language switcher --}}
            <a
                href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                class="inline-flex items-center gap-1.5 font-medium text-secondary hover:text-primary transition-colors"
            >
                <x-icons.globe class="size-4" />
                {{ __('site.locale.switch_to') }}
            </a>

            {{-- Social links --}}
            <div class="flex items-center gap-3">
                @foreach (config('site.social') as $social)
                    <a href="{{ $social['url'] }}" class="text-text-muted hover:text-primary transition-colors" aria-label="{{ $social['name'] }}">
                        <x-dynamic-component :component="'icons.' . $social['icon']" class="size-4" />
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
