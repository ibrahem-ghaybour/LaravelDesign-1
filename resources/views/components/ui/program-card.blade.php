@props([
    'icon',
    'title',
])

<div {{ $attributes->merge(['class' => 'card-base card-hover group flex flex-col items-center gap-4 p-6 text-center']) }}>
    <div class="flex size-16 items-center justify-center rounded-full bg-primary-light text-primary transition-all duration-300 group-hover:scale-110 group-hover:bg-primary group-hover:text-text-inverse">
        <x-dynamic-component :component="'icons.' . $icon" class="size-8" />
    </div>
    <h3 class="text-base font-semibold text-secondary">{{ $title }}</h3>
</div>
