@props([
    'image',
    'title',
    'location',
    'progress',
])

<div {{ $attributes->merge(['class' => 'card-base card-hover overflow-hidden']) }}>
    <div class="aspect-[4/3] overflow-hidden">
        <img src="{{ asset($image) }}" alt="{{ $title }}" class="size-full object-cover transition-transform duration-500 group-hover:scale-105 hover:scale-105" loading="lazy">
    </div>
    <div class="space-y-4 p-5">
        <div>
            <h3 class="font-semibold text-secondary">{{ $title }}</h3>
            <p class="mt-1 flex items-center gap-1.5 text-sm text-text-muted">
                <x-icons.map-pin class="size-3.5 shrink-0 text-primary" />
                {{ $location }}
            </p>
        </div>
        <div>
            <div class="mb-1.5 flex items-center justify-between text-xs">
                <span class="text-text-muted">{{ __('site.projects.funded') }}</span>
                <span class="font-semibold text-primary" dir="ltr">{{ $progress }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" data-progress="{{ $progress }}"></div>
            </div>
        </div>
        <a href="#" class="inline-flex items-center gap-1 text-sm font-semibold text-primary transition-colors hover:text-primary-hover">
            {{ __('site.projects.details') }}
            <x-icons.arrow-left class="size-4 rtl:rotate-180" />
        </a>
    </div>
</div>
