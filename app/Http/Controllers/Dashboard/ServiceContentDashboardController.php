<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Support\SeoResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceContentDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::query()->with('categories')->orderBy('name');

        $search = trim((string) $request->input('q', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('transform_url', 'like', "%{$search}%");
            });
        }

        $categoryId = (int) $request->input('category_id');
        if ($categoryId > 0) {
            $query->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId));
        }

        $services = $query->paginate(20)->withQueryString();
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('dashboard.blog.services.index', compact('services', 'categories'));
    }

    public function edit(Service $service): View
    {
        $service->load('categories');

        return view('dashboard.blog.services.edit', [
            'service' => $service,
            'action' => route('copywriter.services.update', $service),
        ]);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $request->validate(array_merge([
            'description' => ['nullable', 'string'],
            'title' => ['nullable', 'string', 'max:255'],
            'value' => ['nullable', 'string', 'max:1000'],
            'article' => ['nullable', 'string', 'max:255'],
            'marker' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'faq' => ['nullable', 'array'],
            'faq.*.question' => ['nullable', 'string', 'max:255'],
            'faq.*.answer' => ['nullable', 'string'],
        ], SeoResolver::validationRules()));

        $faq = collect($data['faq'] ?? [])
            ->filter(fn ($row) => filled($row['question'] ?? null) && filled($row['answer'] ?? null))
            ->values()
            ->all();

        $service->fill([
            'description' => $data['description'] ?? null,
            'title' => $data['title'] ?? null,
            'value' => $data['value'] ?? null,
            'article' => $data['article'] ?? null,
            'marker' => $data['marker'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
            'seo_keywords' => $data['seo_keywords'] ?? null,
            'meta_title' => $data['meta_title'] ?? null,
            'og_title' => $data['og_title'] ?? null,
            'og_description' => $data['og_description'] ?? null,
            'robots' => $data['robots'] ?? null,
            'canonical_path' => $data['canonical_path'] ?? null,
            'faq' => $faq,
        ]);
        SeoResolver::applyOgImageUpload($service, $request, 'src/seo/services');
        $service->save();

        return redirect()
            ->route('copywriter.services.edit', $service)
            ->with('status', 'Контент послуги оновлено.');
    }
}
