@props([
    'image',
    'date',
    'title',
])

<article {{ $attributes->merge(['class' => 'card-base card-hover group overflow-hidden']) }}>
    <div class="aspect-[16/10] overflow-hidden">
        <img src="{{ asset($image) }}" alt="{{ $title }}" class="size-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
    </div>
    <div class="space-y-2 p-4">
        <time class="text-xs text-text-muted">{{ $date }}</time>
        <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-secondary transition-colors group-hover:text-primary">
            <a href="#">{{ $title }}</a>
        </h3>
    </div>
</article>
