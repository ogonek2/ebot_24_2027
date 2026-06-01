@extends('dashboard.blog.layout')

@section('title', 'Контент: ' . $service->name)
@section('heading', 'Контент послуги')

@section('content')
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        Ціни та прив’язку до категорій змінює адміністратор. Тут доступні лише текстові поля.
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-5 py-4">
            <strong class="text-base font-semibold">{{ $service->name }}</strong>
            <p class="mt-1 text-sm text-slate-500">URL: {{ $service->transform_url ?? $service->href }}</p>
        </div>
        <div class="px-5 py-5">
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-6 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-500">Ціна (потокова)</label>
                        <input type="text" readonly disabled
                               value="{{ is_numeric($service->price) ? number_format((float) $service->price, 0, ',', ' ') . ' ₴' : $service->price }}"
                               class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-600">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-500">Ціна (індивідуальна)</label>
                        <input type="text" readonly disabled
                               value="{{ is_numeric($service->individual_price) && $service->individual_price > 0 ? number_format((float) $service->individual_price, 0, ',', ' ') . ' ₴' : '—' }}"
                               class="w-full cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-2 text-sm text-slate-600">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Опис (HTML)</label>
                    <textarea id="content-editor" name="description" rows="12"
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-mono">{{ old('description', $service->description) }}</textarea>
                </div>

                <div class="mb-4 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Заголовок (title)</label>
                        <input type="text" name="title" value="{{ old('title', $service->title) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Артикул</label>
                        <input type="text" name="article" value="{{ old('article', $service->article) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Маркер</label>
                        <input type="text" name="marker" value="{{ old('marker', $service->marker) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Значення (value)</label>
                        <textarea name="value" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('value', $service->value) }}</textarea>
                    </div>
                </div>

                @include('includes.dashboard.seo-fields', [
                    'entity' => $service,
                    'descriptionField' => 'seo_description',
                    'keywordsField' => 'seo_keywords',
                ])

                @php
                    $faqItems = old('faq', $service->faq ?? []);
                    if (empty($faqItems)) {
                        $faqItems = [['question' => '', 'answer' => '']];
                    }
                @endphp
                <div class="mb-6">
                    <label class="mb-2 block text-sm font-medium text-slate-700">FAQ</label>
                    <div id="faq-rows" class="space-y-3">
                        @foreach($faqItems as $i => $item)
                            <div class="rounded-lg border border-slate-200 p-3 faq-row">
                                <input type="text" name="faq[{{ $i }}][question]" value="{{ $item['question'] ?? '' }}" placeholder="Питання"
                                       class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                <textarea name="faq[{{ $i }}][answer]" rows="2" placeholder="Відповідь"
                                          class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $item['answer'] ?? '' }}</textarea>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" id="faq-add" class="mt-2 text-sm font-medium text-indigo-600 hover:text-indigo-800">+ Додати питання</button>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Зберегти контент</button>
                    <a href="{{ route('copywriter.services.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Назад</a>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.getElementById('faq-add')?.addEventListener('click', function () {
    const wrap = document.getElementById('faq-rows');
    const i = wrap.querySelectorAll('.faq-row').length;
    const div = document.createElement('div');
    div.className = 'rounded-lg border border-slate-200 p-3 faq-row';
    div.innerHTML = `
        <input type="text" name="faq[${i}][question]" placeholder="Питання" class="mb-2 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <textarea name="faq[${i}][answer]" rows="2" placeholder="Відповідь" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
    `;
    wrap.appendChild(div);
});
</script>
@endsection
