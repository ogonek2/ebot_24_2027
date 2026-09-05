@php
    $blogSliderPosts = $latestBlogPosts ?? collect();
@endphp
@if($blogSliderPosts->count() > 0)
<section class="py-14 sm:py-16" id="blog">
    <div class="site-container">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <div class="tag-badge mb-3 w-fit">Блог</div>
                <h2 class="text-section text-[#1A1A2E]">Останні публікації</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="btn-outline px-4 py-2.5 text-[13px] no-underline">Усі статті →</a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
            @foreach($blogSliderPosts as $bpost)
                <a href="{{ route('blog.show', $bpost->slug) }}" class="glass-card overflow-hidden no-underline block p-0">
                    <div class="aspect-[16/10] bg-[#EDE8FF]/50">
                        @if($bpost->featured_image)
                            <img src="{{ asset('storage/' . $bpost->featured_image) }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-newspaper text-3xl text-[#FF2D6A]/30"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <time class="text-[11px] text-[#1A1A2E]/40">{{ $bpost->published_at?->format('d.m.Y') }}</time>
                        <h3 class="font-bold text-[14px] text-[#1A1A2E] mt-1 line-clamp-2">{{ $bpost->title }}</h3>
                        <span class="text-[#FF2D6A] text-[12px] font-bold mt-2 inline-block">Читати →</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
