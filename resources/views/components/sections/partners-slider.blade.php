@props([
    'partners' => [],
])

@if (count($partners) > 0)
    <section class="section-padding bg-surface">
        <div class="container-site">
            <div class="reveal">
                <x-ui.section-heading :title="__('site.partners.title')" />
            </div>

            <div class="partners-slider reveal">
                <button
                    type="button"
                    class="partners-swiper-nav partners-swiper-prev"
                    aria-label="{{ __('site.partners.prev') }}"
                >
                    <x-icons.arrow-left class="size-5" />
                </button>

                <div class="partners-swiper swiper">
                    <div class="swiper-wrapper">
                        @foreach ($partners as $partner)
                            <div class="swiper-slide">
                                <div class="partner-logo">
                                    <img
                                        src="{{ url('images/thumb_100/' . ($partner['logo'] ?? '')) }}"
                                        alt="{{ $partner['name'] }}"
                                        class="max-h-full max-w-full object-contain"
                                        loading="lazy"
                                    >
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="partners-swiper-pagination"></div>
                </div>

                <button
                    type="button"
                    class="partners-swiper-nav partners-swiper-next"
                    aria-label="{{ __('site.partners.next') }}"
                >
                    <x-icons.arrow-right class="size-5" />
                </button>
            </div>
        </div>
    </section>
@endif
