<footer class="bg-secondary text-text-inverse">
    <div class="container-site section-padding pb-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">
            {{-- About --}}
            <div class="space-y-4">
                <x-layout.logo class="size-12" />
                <p class="text-sm leading-relaxed text-white/80">
                    {{ __('site.footer.about') }}
                </p>
            </div>

            {{-- Reports --}}
            <div>
                <h3 class="mb-4 text-lg font-bold">{{ __('site.footer.reports') }}</h3>
                <ul class="space-y-2.5 text-sm text-white/80">
                    <li><a href="#" class="transition-colors hover:text-primary">{{ __('site.footer.annual_report') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-primary">{{ __('site.footer.financial') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-primary">{{ __('site.footer.transparency') }}</a></li>
                    <li><a href="#" class="transition-colors hover:text-primary">{{ __('site.footer.policies') }}</a></li>
                </ul>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="mb-4 text-lg font-bold">{{ __('site.footer.quick_links') }}</h3>
                <ul class="space-y-2.5 text-sm text-white/80">
                    @foreach ($menuItems as $item) 
                        <li>
                            <a href="{{ $item['route'] === 'main' ? route('main') : url($item['route']) }}" class="transition-colors hover:text-primary">
                                {{ __($item['label']) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Contact --}}
            <div>
                <h3 class="mb-4 text-lg font-bold">{{ __('site.footer.contact_us') }}</h3>
                <ul class="space-y-3 text-sm text-white/80">
                    <li class="inline-flex items-start gap-2">
                        <x-icons.map-pin class="mt-0.5 size-4 shrink-0 text-primary" />
                        {{ __('site.contact.location') }}
                    </li>
                    <li>
                        <a href="mailto:{{ config('site.contact.email') }}" class="inline-flex items-center gap-2 transition-colors hover:text-primary">
                            <x-icons.mail class="size-4 shrink-0 text-primary" />
                            {{ $VSPVar['email']  }}
                        </a>
                    </li>
                    <li>
                        <a href="tel:{{ config('site.contact.phone') }}" class="inline-flex items-center gap-2 transition-colors hover:text-primary" dir="ltr">
                            <x-icons.phone class="size-4 shrink-0 text-primary" />
                            {{ $VSPVar['phoneNo'] }}
                        </a>
                    </li>
                </ul>
                <div class="mt-5 flex gap-3">
                    @foreach ($socials as $social)
                        <a href="{{ $social['url'] }}" class="flex size-9 items-center justify-center rounded-full bg-white/10 transition-all duration-200 hover:scale-110 hover:bg-primary" aria-label="{{ $social['name'] }}">
                            <x-dynamic-component :component="'icons.' . $social['icon']" class="size-4" />
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-site flex flex-col items-center justify-between gap-3 py-5 text-sm text-white/70 sm:flex-row">
            <p>{{ __('site.footer.copyright', ['year' => date('Y')]) }}</p>
            <div class="flex gap-5">
                <a href="#" class="transition-colors hover:text-primary">{{ __('site.footer.privacy') }}</a>
                <a href="#" class="transition-colors hover:text-primary">{{ __('site.footer.terms') }}</a>
            </div>
        </div>
    </div>
</footer>
