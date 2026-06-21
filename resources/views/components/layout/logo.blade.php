@props(['class' => ''])

<svg {{ $attributes->merge(['class' => $class, 'viewBox' => '0 0 48 48', 'fill' => 'none', 'xmlns' => 'http://www.w3.org/2000/svg']) }} aria-hidden="true">
    <circle cx="24" cy="24" r="22" fill="#e8f5e9"/>
    <path d="M24 8C18 8 14 14 14 20C14 28 24 38 24 38C24 38 34 28 34 20C34 14 30 8 24 8Z" fill="#4caf50"/>
    <path d="M24 14C21 14 19 17 19 20C19 24 24 30 24 30C24 30 29 24 29 20C29 17 27 14 24 14Z" fill="#ffffff"/>
    <path d="M20 22C20 20 21 19 22 19C23 19 24 20 24 21C24 20 25 19 26 19C27 19 28 20 28 22C28 24 24 28 24 28C24 28 20 24 20 22Z" fill="#4caf50" opacity="0.6"/>
</svg>
