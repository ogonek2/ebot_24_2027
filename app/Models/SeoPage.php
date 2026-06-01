<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{
    public const KEY_BLOG_INDEX = 'blog.index';

    protected $fillable = [
        'key',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'robots',
        'canonical_path',
    ];

    public static function forKey(string $key): self
    {
        return static::query()->firstOrCreate(['key' => $key]);
    }
}
