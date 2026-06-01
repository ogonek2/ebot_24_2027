<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\SeoResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryDashboardController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->with('parent')
            ->withCount('services', 'blogPosts')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('dashboard.blog.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('dashboard.blog.categories.form', $this->formData(new Category()));
    }

    public function store(Request $request): RedirectResponse
    {
        $category = new Category();
        $this->saveCategory($request, $category);

        return redirect()
            ->route('copywriter.categories.index')
            ->with('status', 'Категорію створено.');
    }

    public function edit(Category $category): View
    {
        return view('dashboard.blog.categories.form', $this->formData($category, true));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->saveCategory($request, $category);

        return redirect()
            ->route('copywriter.categories.index')
            ->with('status', 'Категорію оновлено.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->children()->exists()) {
            return back()->withErrors(['category' => 'Спочатку видаліть або перенесіть вкладені категорії.']);
        }

        $category->services()->detach();
        $category->blogPosts()->detach();
        $category->delete();

        return redirect()
            ->route('copywriter.categories.index')
            ->with('status', 'Категорію видалено.');
    }

    private function saveCategory(Request $request, Category $category): void
    {
        $data = $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255'],
            'href' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'category_type' => ['nullable', 'integer'],
            'discount_active' => ['nullable', 'boolean'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'category_img' => ['nullable', 'image', 'max:3072'],
        ], SeoResolver::validationRules()));

        if (! empty($data['parent_id']) && (int) $data['parent_id'] === (int) $category->id) {
            $data['parent_id'] = null;
        }

        $data['discount_active'] = $request->boolean('discount_active');
        if (! $data['discount_active']) {
            $data['discount_percent'] = 0;
        }

        if ($request->hasFile('category_img')) {
            $category->category_img = $request->file('category_img')->store('src/categories_images', 'public');
        }

        $data['robots'] = $data['robots'] ?? null;
        $category->fill(collect($data)->except(['category_img', 'og_image'])->all());
        SeoResolver::applyOgImageUpload($category, $request, 'src/seo/categories');
        $category->save();
    }

    private function formData(Category $category, bool $isEdit = false): array
    {
        $parentOptions = Category::query()
            ->whereNull('parent_id')
            ->when($category->exists, function ($q) use ($category) {
                $q->where('id', '!=', $category->id);
                $descendantIds = $category->getAllDescendants()->pluck('id')->all();
                if ($descendantIds !== []) {
                    $q->whereNotIn('id', $descendantIds);
                }
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'id');

        return [
            'category' => $category,
            'parentOptions' => $parentOptions,
            'action' => $isEdit
                ? route('copywriter.categories.update', $category)
                : route('copywriter.categories.store'),
            'method' => $isEdit ? 'PUT' : 'POST',
        ];
    }
}
