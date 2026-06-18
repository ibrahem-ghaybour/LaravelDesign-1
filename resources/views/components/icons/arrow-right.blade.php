@props(['class' => 'size-6'])

<svg {{ $attributes->merge(['class' => $class, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round']) }} aria-hidden="true">
    <path d="M5 12h14"/>
    <path d="m12 5 7 7-7 7"/>
</svg>
