@if(isset($relatedPosts) && $relatedPosts->count() > 0)
    <div class="rounded-2xl bg-white ring-1 ring-gray-200/80 p-5">
        <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="fas fa-newspaper text-primary text-sm"></i>
            Схожі статті
        </h2>
        <ul class="space-y-4">
            @foreach($relatedPosts as $rel)
                <li>
                    <a href="{{ route('blog.show', $rel->slug) }}"
                       class="group flex gap-3 items-start">
                        <div class="shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-100 ring-1 ring-gray-200/80">
                            @if($rel->featured_image)
                                <img src="{{ asset('storage/' . $rel->featured_image) }}"
                                     alt=""
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/20 to-enot-pink/20">
                                    <i class="fas fa-file-alt text-primary/40 text-sm"></i>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="text-sm font-semibold text-gray-900 group-hover:text-primary line-clamp-2 leading-snug transition-colors">
                                {{ $rel->title }}
                            </span>
                            <time class="text-xs text-gray-500 mt-1 block" datetime="{{ $rel->published_at?->toIso8601String() }}">
                                {{ $rel->published_at?->format('d.m.Y') }}
                            </time>
                        </div>
                    </a>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('blog.index') }}"
           class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-enot-pink transition-colors">
            Усі публікації <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
@endif
