@extends('dashboard.blog.layout')

@section('title', 'Категорії')
@section('heading', 'Категорії')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600">Спільна таблиця категорій для послуг і блогу.</p>
        <a href="{{ route('copywriter.categories.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Нова категорія</a>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Назва</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">URL</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Послуг</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Статей</th>
                    <th class="px-4 py-3"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($categories as $category)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-500">{{ $category->id }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">
                            @if($category->parent_id)
                                <span class="text-slate-400">└</span>
                            @endif
                            {{ $category->name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $category->href }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $category->services_count }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $category->blog_posts_count }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('copywriter.categories.edit', $category) }}" class="mr-2 inline-flex rounded-lg border border-indigo-200 px-3 py-1.5 text-sm text-indigo-700 hover:bg-indigo-50">Редагувати</a>
                            <form action="{{ route('copywriter.categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Видалити категорію?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex rounded-lg border border-red-200 px-3 py-1.5 text-sm text-red-700 hover:bg-red-50">Видалити</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">Категорій ще немає</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">{{ $categories->links() }}</div>
    </div>
@endsection
