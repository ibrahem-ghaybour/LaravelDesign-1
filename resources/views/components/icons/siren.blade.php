@props(['class' => 'size-6'])

<svg {{ $attributes->merge(['class' => $class, 'viewBox' => '0 0 24 24', 'fill' => 'none', 'stroke' => 'currentColor', 'stroke-width' => '2', 'stroke-linecap' => 'round', 'stroke-linejoin' => 'round']) }} aria-hidden="true">
    <path d="M7 18v-6a5 5 0 0 1 10 0v6"/>
    <path d="M5 18h14"/>
    <path d="M12 2v2"/>
    <path d="M4.93 4.93 6.34 6.34"/>
    <path d="M19.07 4.93 17.66 6.34"/>
</svg>
