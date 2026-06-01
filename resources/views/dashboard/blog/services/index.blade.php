@extends('dashboard.blog.layout')

@section('title', 'Контент послуг')
@section('heading', 'Контент послуг')

@section('content')
    <p class="mb-4 text-sm text-slate-600">Редагування тексту та SEO. Ціни змінюються лише в основній адмін-панелі.</p>

    <form method="GET" class="mb-5 flex flex-wrap gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Пошук за назвою…"
               class="min-w-[200px] flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <select name="category_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Усі категорії</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-900">Фільтр</button>
    </form>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Назва</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Категорії</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Ціна</th>
                    <th class="px-4 py-3"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($services as $service)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $service->name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $service->categories->pluck('name')->join(', ') ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-500">
                            {{ is_numeric($service->price) ? number_format((float) $service->price, 0, ',', ' ') . ' ₴' : $service->price }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('copywriter.services.edit', $service) }}"
                               class="inline-flex rounded-lg border border-indigo-200 px-3 py-1.5 text-sm text-indigo-700 hover:bg-indigo-50">Контент</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">Послуг не знайдено</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $services->links() }}</div>
    </div>
@endsection
