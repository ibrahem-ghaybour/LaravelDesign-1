@props([
    'title',
    'subtitle' => null,
    'align' => 'center',
])

@php
    $alignClass = match ($align) {
        'start' => 'text-start',
        'end' => 'text-end',
        default => 'text-center',
    };

    $lineClass = match ($align) {
        'start' => 'ms-0',
        'end' => 'me-0 ms-auto',
        default => 'mx-auto',
    };
@endphp

<div {{ $attributes->merge(['class' => "mb-12 {$alignClass}"]) }}>
    <h2 class="text-2xl font-bold text-secondary md:text-3xl">{{ $title }}</h2>
    @if ($subtitle)
        <p class="mt-3 max-w-2xl text-text-muted {{ $align === 'center' ? 'mx-auto' : '' }}">{{ $subtitle }}</p>
    @endif
    <div @class(['section-heading-line', $lineClass])></div>
</div>
