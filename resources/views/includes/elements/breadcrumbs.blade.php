{{--
    $breadcrumbs — масив елементів:
      ['name' => '...', 'url' => route(...)] — посилання
      ['name' => '...'] — поточна сторінка (без url)
    $withSchema — JSON-LD BreadcrumbList (за замовчуванням true)
    $wrapperClass — клас контейнера
--}}
@php
    $breadcrumbs = $breadcrumbs ?? [];
    $withSchema = $withSchema ?? true;
    $wrapperClass = $wrapperClass ?? 'container mx-auto px-4 md:px-6';
@endphp

@if(count($breadcrumbs) > 0)
    @if($withSchema)
        @include('includes.seo.schema-breadcrumbs', ['breadcrumbs' => breadcrumb_schema_trail($breadcrumbs)])
    @endif

    <div class="{{ $wrapperClass }}">
        <nav class="mb-4 md:mb-6" aria-label="Навігація: хлібні крихти">
            <ol class="enot-breadcrumbs flex items-center gap-1 sm:gap-1.5 text-xs sm:text-sm text-gray-600 min-w-0 overflow-x-auto pb-1 -mx-1 px-1 sm:flex-wrap sm:overflow-visible">
                @foreach($breadcrumbs as $index => $crumb)
                    @php
                        $isLast = $loop->last;
                        $label = $crumb['name'] ?? '';
                    @endphp
                    <li class="flex items-center gap-1 sm:gap-1.5 min-w-0 shrink-0 {{ $isLast ? 'shrink min-w-0' : '' }}">
                        @if(!$isLast && !empty($crumb['url']))
                            <a href="{{ $crumb['url'] }}"
                               class="text-gray-600 hover:text-primary transition-colors truncate max-w-[6.5rem] xs:max-w-[9rem] sm:max-w-[11rem] md:max-w-none whitespace-nowrap">
                                {{ $label }}
                            </a>
                            <i class="fas fa-chevron-right text-[10px] sm:text-xs text-gray-400 shrink-0" aria-hidden="true"></i>
                        @else
                            <span class="font-semibold text-gray-900 truncate max-w-[10rem] xs:max-w-[14rem] sm:max-w-[18rem] md:max-w-none whitespace-nowrap"
                                  @if($isLast) aria-current="page" @endif>
                                {{ $label }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    </div>
@endif
