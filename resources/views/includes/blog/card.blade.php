@php
    $compact = $compact ?? false;
    $excerpt = \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), $compact ? 80 : 110);
    $readingMin = max(1, (int) ceil(mb_strlen(strip_tags($post->content ?? '')) / 1200));
@endphp

<a href="{{ route('blog.show', $post->slug) }}"
   class="blog-card group relative flex flex-col h-full overflow-hidden rounded-2xl bg-white ring-1 ring-gray-200/80 shadow-sm hover:shadow-lg hover:ring-primary/25 transition-all duration-300">
    <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
        @if($post->featured_image)
            <img src="{{ asset('storage/' . $post->featured_image) }}"
                 alt="{{ $post->title }}"
                 class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                 loading="lazy">
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-primary/25 via-enot-pink/15 to-secondary/25 flex items-center justify-center">
                <i class="fas fa-feather-alt text-3xl sm:text-4xl text-primary/35"></i>
            </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900/75 via-gray-900/15 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-3 sm:p-4">
            <div class="flex flex-wrap items-center gap-1.5 text-[10px] sm:text-xs text-white/90 mb-1.5">
                <time datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ $post->published_at?->format('d.m.Y') }}
                </time>
                <span class="w-1 h-1 rounded-full bg-white/50"></span>
                <span>{{ $readingMin }} хв</span>
            </div>
            <h2 class="font-bold text-white text-sm sm:text-base leading-snug line-clamp-2">
                {{ $post->title }}
            </h2>
        </div>
    </div>
    @if(!$compact)
        <div class="flex flex-col flex-grow p-3 sm:p-4">
            <p class="text-gray-600 text-xs sm:text-sm leading-relaxed line-clamp-2 flex-grow">
                {{ $excerpt }}
            </p>
            <span class="mt-3 inline-flex items-center justify-between gap-2">
                <span class="text-xs sm:text-sm font-semibold text-primary group-hover:text-enot-pink transition-colors">Читати</span>
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary/10 text-primary text-xs group-hover:bg-primary group-hover:text-white transition-all">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </span>
        </div>
    @endif
</a>
