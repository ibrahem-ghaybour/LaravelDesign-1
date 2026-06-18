@props([
    'image',
    'date',
    'title',
])

<article class="card-base group overflow-hidden">
    <div class="aspect-[16/10] overflow-hidden">
        <img src="{{ asset($image) }}" alt="{{ $title }}" class="size-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
    </div>
    <div class="space-y-2 p-4">
        <time class="text-xs text-text-muted">{{ $date }}</time>
        <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-secondary group-hover:text-primary transition-colors">
            <a href="#">{{ $title }}</a>
        </h3>
    </div>
</article>
