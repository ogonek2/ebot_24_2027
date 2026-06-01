<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->paginate(16);

        return view('blog', ['posts' => $posts]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view('blog-post', [
            'post' => $post,
            'relatedPosts' => $post->relatedPostsByRelevance(6),
            'relatedServices' => $post->relatedServices(),
        ]);
    }

    public function feed(): Response
    {
        $posts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(50)
            ->get();

        $xml = view('feeds.blog', ['posts' => $posts])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
        ]);
    }
}
