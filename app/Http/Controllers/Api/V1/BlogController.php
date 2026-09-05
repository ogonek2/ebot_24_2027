<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->input('per_page', 16);
        $perPage = max(1, min(100, $perPage ?: 16));

        $posts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->paginate($perPage);

        return response()->json([
            'data' => $posts->getCollection()->map(fn (BlogPost $p) => $this->serializePost($p))->values(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $post = BlogPost::query()
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return response()->json([
            'post' => array_merge($this->serializePost($post), [
                'content' => $post->content,
            ]),
            'relatedPosts' => $post->relatedPostsByRelevance(6)->map(fn (BlogPost $p) => $this->serializePost($p))->values(),
            'relatedServices' => $post->relatedServices()->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'href' => $s->transform_url ?? $s->href,
                'categoryHref' => $s->categories->first()?->href,
            ])->values(),
        ]);
    }

    private function serializePost(BlogPost $post): array
    {
        return [
            'slug' => $post->slug,
            'title' => $post->title,
            'publishedAt' => $post->published_at?->format('d.m.Y'),
            'image' => $post->featured_image ? asset('storage/' . $post->featured_image) : null,
            'url' => '/blog/' . $post->slug,
            'excerpt' => \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160),
        ];
    }
}
