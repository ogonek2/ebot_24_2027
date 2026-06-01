@php
    $entity = $entity ?? null;
    $descriptionField = $descriptionField ?? 'meta_description';
    $keywordsField = $keywordsField ?? 'meta_keywords';
    $descriptionValue = old($descriptionField, $entity ? ($entity->{$descriptionField} ?? '') : '');
    $robotsValue = old('robots', optional($entity)->robots ?? '');
    $robotsOptions = [
        '' => 'За замовчуванням (index, follow)',
        'index, follow' => 'index, follow',
        'noindex, follow' => 'noindex, follow',
        'noindex, nofollow' => 'noindex, nofollow',
    ];
@endphp

<div class="mt-6 rounded-xl border border-indigo-100 bg-indigo-50/40 p-4 md:p-5">
    <h3 class="mb-1 text-sm font-semibold text-indigo-900">SEO</h3>
    <p class="mb-4 text-xs text-slate-600">Порожні поля — автоматичні шаблони на сайті. Canonical — відносний шлях, напр. <code class="text-indigo-700">/blog/moya-stattya</code></p>

    <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Meta title <span class="font-normal text-slate-400">(до 70 символів)</span></label>
            <input type="text" name="meta_title" maxlength="70" value="{{ old('meta_title', optional($entity)->meta_title ?? '') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Meta description</label>
            <textarea name="{{ $descriptionField }}" rows="3" maxlength="500"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ $descriptionValue }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Meta keywords</label>
            <input type="text" name="{{ $keywordsField }}" value="{{ old($keywordsField, optional($entity)->{$keywordsField} ?? '') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                   placeholder="хімчистка, прання, київ">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Open Graph title</label>
            <input type="text" name="og_title" maxlength="70" value="{{ old('og_title', optional($entity)->og_title ?? '') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Robots</label>
            <select name="robots" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                @foreach($robotsOptions as $val => $label)
                    <option value="{{ $val }}" @selected($robotsValue === $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="mb-1 block text-sm font-medium text-slate-700">Open Graph description</label>
            <textarea name="og_description" rows="2" maxlength="300"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('og_description', optional($entity)->og_description ?? '') }}</textarea>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">OG-зображення</label>
            <input type="file" name="og_image" accept="image/*" class="w-full text-sm">
            @if(optional($entity)->og_image)
                <img src="{{ asset('storage/' . $entity->og_image) }}" alt="" class="mt-2 h-16 rounded-lg object-cover">
            @endif
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Canonical (шлях)</label>
            <input type="text" name="canonical_path" value="{{ old('canonical_path', optional($entity)->canonical_path ?? '') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                   placeholder="/poslugi/katygoriya/posluga">
        </div>
    </div>
</div>
