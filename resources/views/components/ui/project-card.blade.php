@props([
    'image',
    'title',
    'location',
    'progress',
])

<div class="card-base overflow-hidden">
    <div class="aspect-[4/3] overflow-hidden">
        <img src="{{ asset($image) }}" alt="{{ $title }}" class="size-full object-cover transition-transform duration-300 hover:scale-105" loading="lazy">
    </div>
    <div class="space-y-4 p-5">
        <div>
            <h3 class="font-semibold text-secondary">{{ $title }}</h3>
            <p class="mt-1 flex items-center gap-1.5 text-sm text-text-muted">
                <x-icons.map-pin class="size-3.5 text-primary" />
                {{ $location }}
            </p>
        </div>
        <div>
            <div class="mb-1.5 flex items-center justify-between text-xs">
                <span class="text-text-muted">{{ __('site.projects.funded') }}</span>
                <span class="font-semibold text-primary" dir="ltr">{{ $progress }}%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ $progress }}%"></div>
            </div>
        </div>
        <a href="#" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary-hover transition-colors">
            {{ __('site.projects.details') }}
            <x-icons.arrow-left class="size-4 rtl:rotate-180" />
        </a>
    </div>
</div>
