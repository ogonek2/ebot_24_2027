@extends('dashboard.blog.layout')

@section('title', $category->exists ? 'Редагування категорії' : 'Нова категорія')
@section('heading', $category->exists ? 'Редагування категорії' : 'Нова категорія')

@section('content')
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-5 py-4">
            <strong class="text-base font-semibold">{{ $category->exists ? $category->name : 'Нова категорія' }}</strong>
        </div>
        <div class="px-5 py-5">
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf
                @if($method !== 'POST')
                    @method($method)
                @endif

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Назва *</label>
                        <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm @error('name') border-red-400 @enderror">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">URL (href)</label>
                        <input type="text" name="href" value="{{ old('href', $category->href) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"
                               placeholder="автогенерація з назви">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Батьківська категорія</label>
                        <select name="parent_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="">— коренева —</option>
                            @foreach($parentOptions as $id => $name)
                                <option value="{{ $id }}" @selected((string) old('parent_id', $category->parent_id) === (string) $id)>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Порядок сортування</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Тип категорії</label>
                        <input type="number" name="category_type" value="{{ old('category_type', $category->category_type ?? 1) }}"
                               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Іконка</label>
                        <input type="file" name="category_img" accept="image/*" class="w-full text-sm">
                        @if($category->category_img)
                            <img src="{{ asset('storage/' . $category->category_img) }}" alt="" class="mt-2 h-16 w-16 rounded-lg object-cover">
                        @endif
                    </div>
                </div>

                @include('includes.dashboard.seo-fields', ['entity' => $category])

                <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <input type="hidden" name="discount_active" value="0">
                        <input type="checkbox" name="discount_active" value="1" class="h-4 w-4"
                            @checked(old('discount_active', $category->discount_active))>
                        Активна знижка на категорію
                    </label>
                    <div class="mt-3">
                        <label class="mb-1 block text-sm text-slate-600">Знижка, %</label>
                        <input type="number" name="discount_percent" min="0" max="100"
                               value="{{ old('discount_percent', $category->discount_percent ?? 0) }}"
                               class="w-full max-w-xs rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Зберегти</button>
                    <a href="{{ route('copywriter.categories.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Назад</a>
                </div>
            </form>
        </div>
    </div>
@endsection
