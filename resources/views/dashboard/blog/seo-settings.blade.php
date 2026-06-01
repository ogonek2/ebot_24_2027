@extends('dashboard.blog.layout')

@section('title', 'SEO списку блогу')
@section('heading', 'SEO — сторінка /blog')

@section('content')
    <p class="mb-4 text-sm text-slate-600">Мета-теги для головної сторінки блогу та пагінації (canonical для стор. 2+ формується автоматично).</p>

    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <div class="border-b border-slate-200 px-5 py-4">
            <strong class="text-base font-semibold">Список статей (/blog)</strong>
        </div>
        <div class="px-5 py-5">
            <form method="POST" action="{{ $action }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('includes.dashboard.seo-fields', ['entity' => $seoPage])
                <div class="mt-6 flex gap-2">
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700">Зберегти</button>
                    <a href="{{ route('copywriter.posts.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">До статей</a>
                </div>
            </form>
        </div>
    </div>
@endsection
