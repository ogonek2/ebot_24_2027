@extends('dashboard.blog.layout')

@section('title', 'Статті блогу')
@section('heading', 'Блог')

@section('content')
    <div class="mb-5 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs uppercase tracking-wide text-slate-400">Всього статей</p>
            <p class="mt-2 text-2xl font-semibold text-slate-800">{{ $posts->total() }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs uppercase tracking-wide text-slate-400">На сторінці</p>
            <p class="mt-2 text-2xl font-semibold text-slate-800">{{ $posts->count() }}</p>
        </div>
        <div class="rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200">
            <p class="text-xs uppercase tracking-wide text-slate-400">Опубліковано</p>
            <p class="mt-2 text-2xl font-semibold text-slate-800">{{ $posts->whereNotNull('published_at')->count() }}</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <strong class="text-base font-semibold">Публікації</strong>
            <a href="{{ route('copywriter.posts.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700">Нова стаття</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Заголовок</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Категорії</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Публікація</th>
                    <th class="px-4 py-3"></th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                @forelse($posts as $post)
                    <tr class="transition hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-500">{{ $post->id }}</td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $post->title }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">{{ $post->categories->pluck('name')->join(', ') ?: '—' }}</td>
                        <td class="px-4 py-3 text-sm text-slate-600">
                            @if($post->published_at)
                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    {{ optional($post->published_at)->format('d.m.Y H:i') }}
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700">Чернетка</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('copywriter.posts.edit', $post) }}" class="inline-flex rounded-lg border border-indigo-200 px-3 py-1.5 text-sm font-medium text-indigo-700 transition hover:bg-indigo-50">Редагувати</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Публікацій поки немає</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-4">
            {{ $posts->links() }}
        </div>
    </div>
@endsection
