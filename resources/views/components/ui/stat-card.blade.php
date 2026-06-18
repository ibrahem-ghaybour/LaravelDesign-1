@props([
    'icon',
    'value',
    'label',
])

<div class="card-base flex flex-col items-center gap-3 p-6 text-center">
    <div class="stat-icon-ring">
        <x-dynamic-component :component="'icons.' . $icon" class="size-6" />
    </div>
    <p class="text-2xl font-bold text-secondary md:text-3xl" dir="ltr">{{ $value }}</p>
    <p class="text-sm text-text-muted">{{ $label }}</p>
</div>
