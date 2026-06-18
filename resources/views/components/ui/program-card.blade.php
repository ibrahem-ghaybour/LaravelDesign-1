@props([
    'icon',
    'title',
])

<div class="card-base group flex flex-col items-center gap-4 p-6 text-center">
    <div class="flex size-16 items-center justify-center rounded-full bg-primary-light text-primary transition-colors group-hover:bg-primary group-hover:text-text-inverse">
        <x-dynamic-component :component="'icons.' . $icon" class="size-8" />
    </div>
    <h3 class="text-base font-semibold text-secondary">{{ $title }}</h3>
</div>
