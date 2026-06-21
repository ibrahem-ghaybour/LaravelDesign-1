@props([
    'icon',
    'value',
    'label',
    'count' => null,
    'suffix' => '',
    'prefix' => '',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-3 px-4 py-6 text-center sm:py-8']) }}>
    <div class="stat-icon-ring">
        <x-dynamic-component :component="'icons.' . $icon" class="size-6" />
    </div>
    @if ($count !== null)
        <p
            class="text-2xl font-bold text-secondary md:text-3xl"
            dir="ltr"
            data-count="{{ $count }}"
            data-suffix="{{ $suffix }}"
            data-prefix="{{ $prefix }}"
        >0</p>
    @else
        <p class="text-2xl font-bold text-secondary md:text-3xl" dir="ltr">{{ $value }}</p>
    @endif
    <p class="text-sm text-text-muted">{{ $label }}</p>
</div>
