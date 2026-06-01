<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Service;
use App\Support\SeoResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogDashboardController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->with('categories')
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('dashboard.blog.index', compact('posts'));
    }

    public function create(): View
    {
        return view('dashboard.blog.form', $this->formData(new BlogPost()));
    }

    public function store(Request $request): RedirectResponse
    {
        $post = new BlogPost();
        $this->savePost($request, $post);

        return redirect()
            ->route('copywriter.posts.index')
            ->with('status', 'Статтю створено.');
    }

    public function edit(BlogPost $post): View
    {
        return view('dashboard.blog.form', $this->formData($post, true));
    }

    public function update(Request $request, BlogPost $post): RedirectResponse
    {
        $this->savePost($request, $post);

        return redirect()
            ->route('copywriter.posts.index')
            ->with('status', 'Статтю оновлено.');
    }

    private function savePost(Request $request, BlogPost $post): void
    {
        $data = $request->validate(array_merge([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug,' . $post->id],
            'content' => ['nullable', 'string'],
            'publish_mode' => ['required', 'in:now,schedule,draft'],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'image', 'max:3072'],
            'related_service_ids' => ['nullable', 'array', 'max:6'],
            'related_service_ids.*' => ['integer', 'exists:services,id'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ], SeoResolver::validationRules()));

        if (blank($data['slug'] ?? null)) {
            $baseSlug = Service::generateHref($data['title']);
            $data['slug'] = BlogPost::uniqueSlug($baseSlug, $post->id);
        } else {
            $data['slug'] = BlogPost::uniqueSlug($data['slug'], $post->id);
        }

        if (($data['publish_mode'] ?? 'draft') === 'now') {
            $data['published_at'] = now();
        } elseif (($data['publish_mode'] ?? 'draft') === 'draft') {
            $data['published_at'] = null;
        }

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('src/blog', 'public');
        }

        unset($data['publish_mode']);

        $data['related_service_ids'] = array_values(array_unique(array_filter(
            array_map('intval', $data['related_service_ids'] ?? [])
        )));

        $categoryIds = array_values(array_unique(array_filter(
            array_map('intval', $data['category_ids'] ?? [])
        )));
        unset($data['category_ids']);

        $data['robots'] = $data['robots'] ?? null;
        $post->fill(collect($data)->except(['og_image'])->all());
        SeoResolver::applyOgImageUpload($post, $request, 'src/seo/blog');
        $post->save();
        $post->categories()->sync($categoryIds);
    }

    private function formData(BlogPost $post, bool $isEdit = false): array
    {
        if ($post->exists) {
            $post->load('categories');
        }

        return [
            'post' => $post,
            'action' => $isEdit
                ? route('copywriter.posts.update', $post)
                : route('copywriter.posts.store'),
            'method' => $isEdit ? 'PUT' : 'POST',
            'services' => Service::query()->orderBy('name')->get(['id', 'name']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'parent_id']),
            'selectedCategoryIds' => old(
                'category_ids',
                $post->exists ? $post->categories->pluck('id')->all() : []
            ),
        ];
    }
}
