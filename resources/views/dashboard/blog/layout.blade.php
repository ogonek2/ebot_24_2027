<!DOCTYPE html>

<html lang="uk">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Контент') — {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">

    <link rel="stylesheet" href="{{ asset('css/blog-dashboard.css') }}">

    @yield('styles')

</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

<div class="min-h-screen lg:grid lg:grid-cols-12">

    <aside class="hidden border-r border-slate-200 bg-white lg:col-span-3 lg:block xl:col-span-2">

        <div class="flex h-16 items-center border-b border-slate-200 px-5">

            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-sm font-bold text-white">E</div>

            <div class="ml-3">

                <p class="text-sm font-semibold">Контент</p>

                <p class="text-xs text-slate-500">Панель копірайтера</p>

            </div>

        </div>

        <nav class="space-y-1 p-3">

            <a href="{{ route('copywriter.posts.index') }}"

               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('copywriter.posts.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">

                <span class="inline-block h-2 w-2 rounded-full bg-indigo-500"></span>

                Блог

            </a>

            <a href="{{ route('copywriter.categories.index') }}"

               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('copywriter.categories.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">

                <span class="inline-block h-2 w-2 rounded-full bg-violet-500"></span>

                Категорії

            </a>

            <a href="{{ route('copywriter.services.index') }}"

               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('copywriter.services.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">

                <span class="inline-block h-2 w-2 rounded-full bg-emerald-500"></span>

                Контент послуг

            </a>

            <a href="{{ route('copywriter.blog-seo.edit') }}"

               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('copywriter.blog-seo.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50' }}">

                <span class="inline-block h-2 w-2 rounded-full bg-amber-500"></span>

                SEO списку блогу

            </a>

        </nav>

    </aside>



    <div class="min-w-0 lg:col-span-9 xl:col-span-10">

        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">

            <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">

                <div>

                    <p class="text-xs uppercase tracking-wide text-slate-400">Dashboard</p>

                    <h1 class="text-base font-semibold">@yield('heading', 'Керування контентом')</h1>

                </div>

                <div class="flex items-center gap-3">

                    <span class="hidden text-sm text-slate-500 sm:inline">{{ auth()->user()->name }}</span>

                    <a href="{{ route('logout') }}"

                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"

                       class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Вийти</a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">

                        @csrf

                    </form>

                </div>

            </div>

        </header>



        <main class="min-w-0 px-4 py-6 sm:px-6 lg:px-8">

            @if(session('status'))

                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">

                    {{ session('status') }}

                </div>

            @endif



            @if($errors->any())

                <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">

                    <ul class="list-disc pl-5">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif



            @yield('content')

        </main>

    </div>

</div>



<script src="{{ asset('js/blog-dashboard.js') }}" defer></script>

@yield('scripts')

</body>

</html>


