<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Response;

class BlogController extends Controller
{
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
