@if ($paginator->hasPages())
    <nav class="blog-pager" role="navigation" aria-label="Пагінація блогу">
        <ul class="blog-pager__list">
            {{-- Попередня --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="blog-pager__btn blog-pager__btn--nav is-disabled" aria-disabled="true">
                        <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
                        <span class="hidden sm:inline">Назад</span>
                    </span>
                @else
                    <a href="{{ seo_paginator_url($paginator->previousPageUrl()) }}" rel="prev"
                       class="blog-pager__btn blog-pager__btn--nav">
                        <i class="fas fa-chevron-left text-xs" aria-hidden="true"></i>
                        <span class="hidden sm:inline">Назад</span>
                    </a>
                @endif
            </li>

            {{-- Номери сторінок --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="blog-pager__ellipsis" aria-hidden="true">{{ $element }}</li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="blog-pager__btn is-active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ seo_paginator_url($url) }}" class="blog-pager__btn">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Наступна --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ seo_paginator_url($paginator->nextPageUrl()) }}" rel="next"
                       class="blog-pager__btn blog-pager__btn--nav">
                        <span class="hidden sm:inline">Далі</span>
                        <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                    </a>
                @else
                    <span class="blog-pager__btn blog-pager__btn--nav is-disabled" aria-disabled="true">
                        <span class="hidden sm:inline">Далі</span>
                        <i class="fas fa-chevron-right text-xs" aria-hidden="true"></i>
                    </span>
                @endif
            </li>
        </ul>

        <p class="blog-pager__meta">
            Сторінка <strong>{{ $paginator->currentPage() }}</strong> з <strong>{{ $paginator->lastPage() }}</strong>
            <span class="text-gray-400">·</span>
            {{ $paginator->total() }} {{ $paginator->total() === 1 ? 'стаття' : ($paginator->total() < 5 ? 'статті' : 'статей') }}
        </p>
    </nav>
@endif
