@extends('dashboard.blog.layout')

@section('title', $post->exists ? 'Редагування статті' : 'Нова стаття')

@section('content')
    <div class="mb-5 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 md:col-span-2">
            <p class="text-xs uppercase tracking-wide text-slate-400">Режим</p>
            <p class="mt-2 text-lg font-semibold text-slate-800">{{ $post->exists ? 'Редагування' : 'Створення' }} статті</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs uppercase tracking-wide text-slate-400">Підказка</p>
            <p class="mt-2 text-sm text-slate-600">Використовуйте редактор для списків, таблиць, цитат та форматування тексту.</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-5 py-4">
            <strong class="text-base font-semibold">{{ $post->exists ? 'Редагування статті' : 'Створення статті' }}</strong>
        </div>
        <div class="px-5 py-5">
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf
                @if($method !== 'POST')
                    @method($method)
                @endif

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="mb-1">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Заголовок</label>
                        <input id="title-input" type="text" name="title" value="{{ old('title', $post->title) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-indigo-200 focus:ring @error('title') border-red-400 @enderror" required>
                        @error('title') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-1">
                        <label class="mb-1 block text-sm font-medium text-slate-700">Slug</label>
                        <input id="slug-input" type="text" name="slug" value="{{ old('slug', $post->slug) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-indigo-200 focus:ring @error('slug') border-red-400 @enderror">
                        @error('slug') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 mb-4">
                    <p class="text-xs text-slate-500">Текст візуального редактора:</p>
                </div>

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Контент</label>
                    <textarea id="content-editor" name="content" class="@error('content') border-red-400 @enderror">{{ old('content', $post->content) }}</textarea>
                    @error('content') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                @include('includes.dashboard.seo-fields', ['entity' => $post])

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Категорії блогу</label>
                    <p class="mb-2 text-xs text-slate-500">Ті самі категорії, що й для послуг (таблиця categories).</p>
                    <select id="blog-category-ids" name="category_ids[]" multiple
                            class="js-select2 w-full @error('category_ids') border-red-400 @enderror"
                            data-placeholder="Оберіть категорії…">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ in_array($category->id, $selectedCategoryIds, false) ? 'selected' : '' }}>
                                {{ $category->parent_id ? '— ' : '' }}{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_ids') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                @php
                    $selectedServiceIds = old('related_service_ids', $post->related_service_ids ?? []);
                @endphp
                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Релевантні послуги</label>
                    <p class="mb-2 text-xs text-slate-500">Оберіть до 6 послуг — вони з’являться в блоці на сторінці статті (посилання на комерційні сторінки для SEO).</p>
                    <select id="blog-related-services" name="related_service_ids[]" multiple
                            class="js-select2 w-full @error('related_service_ids') border-red-400 @enderror"
                            data-placeholder="Пошук послуг…"
                            data-max-selection="6">
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ in_array($service->id, $selectedServiceIds, false) ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('related_service_ids') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                    @error('related_service_ids.*') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Зображення</label>
                    <input type="file" name="featured_image" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm @error('featured_image') border-red-400 @enderror">
                    @error('featured_image') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                @php
                    $oldMode = old('publish_mode');
                    $defaultMode = optional($post->published_at)->isFuture() ? 'schedule' : (optional($post->published_at)->isPast() ? 'now' : 'draft');
                    $mode = $oldMode ?: $defaultMode;
                @endphp

                <div class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <label class="mb-2 block text-sm font-medium text-slate-700">Публікація</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input class="h-4 w-4" type="radio" name="publish_mode" id="publish-draft" value="draft" {{ $mode === 'draft' ? 'checked' : '' }}>
                            <span>Чернетка (не публікувати)</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-slate-700">
                            <input class="h-4 w-4" type="radio" name="publish_mode" id="publish-now" value="now" {{ $mode === 'now' ? 'checked' : '' }}>
                            <span>Опублікувати зараз</span>
                        </label>
                        <label class="mb-1 flex items-center gap-2 text-sm text-slate-700">
                            <input class="h-4 w-4" type="radio" name="publish_mode" id="publish-schedule" value="schedule" {{ $mode === 'schedule' ? 'checked' : '' }}>
                            <span>Запланувати публікацію</span>
                        </label>
                    </div>
                    <input
                        type="datetime-local"
                        name="published_at"
                        id="published-at-input"
                        value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}"
                        class="mt-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none ring-indigo-200 focus:ring @error('published_at') border-red-400 @enderror"
                    >
                    @error('published_at') <div class="mt-1 text-sm text-red-600">{{ $message }}</div> @enderror
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700">Зберегти</button>
                    <a href="{{ route('copywriter.posts.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Назад</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css">
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/i18n/uk.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
                return;
            }

            var $ = jQuery;

            $('.js-select2').each(function () {
                var $el = $(this);
                var max = parseInt($el.data('max-selection'), 10);
                var options = {
                    width: '100%',
                    placeholder: $el.data('placeholder') || 'Оберіть…',
                    allowClear: true,
                    closeOnSelect: false,
                    language: 'uk',
                };

                if (!isNaN(max) && max > 0) {
                    options.maximumSelectionLength = max;
                }

                $el.select2(options);
            });
        });
    </script>
@endsection
