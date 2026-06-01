<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoResolver
{
    public static function title(Model $model, string $fallback, ?string $suffix = null): string
    {
        $title = filled($model->meta_title ?? null) ? (string) $model->meta_title : $fallback;

        if ($suffix && ! Str::contains($title, $suffix)) {
            $title = seo_truncate(rtrim($title, ' |') . ' | ' . $suffix, 70);
        }

        return seo_truncate($title, 70);
    }

    public static function description(Model $model, string $fallback): string
    {
        if ($model instanceof Service && filled($model->seo_description)) {
            return seo_truncate((string) $model->seo_description, 160);
        }

        if (filled($model->meta_description ?? null)) {
            return seo_truncate((string) $model->meta_description, 160);
        }

        return seo_truncate($fallback, 160);
    }

    public static function keywords(Model $model, ?string $fallback = null): ?string
    {
        if ($model instanceof Service && filled($model->seo_keywords)) {
            return (string) $model->seo_keywords;
        }

        if (filled($model->meta_keywords ?? null)) {
            return (string) $model->meta_keywords;
        }

        return $fallback;
    }

    public static function robots(?Model $model = null): string
    {
        $robots = $model->robots ?? null;

        if (filled($robots)) {
            return (string) $robots;
        }

        return config('seo.robots_index');
    }

    public static function canonical(?Model $model, string $defaultPath): string
    {
        $path = filled($model->canonical_path ?? null)
            ? '/' . ltrim((string) $model->canonical_path, '/')
            : $defaultPath;

        return seo_canonical($path);
    }

    public static function ogTitle(Model $model, string $pageTitle): string
    {
        if (filled($model->og_title ?? null)) {
            return (string) $model->og_title;
        }

        if (filled($model->meta_title ?? null)) {
            return (string) $model->meta_title;
        }

        return $pageTitle;
    }

    public static function ogDescription(Model $model, string $pageDescription): string
    {
        if (filled($model->og_description ?? null)) {
            return seo_truncate((string) $model->og_description, 200);
        }

        if ($model instanceof Service && filled($model->seo_description)) {
            return seo_truncate((string) $model->seo_description, 200);
        }

        if (filled($model->meta_description ?? null)) {
            return seo_truncate((string) $model->meta_description, 200);
        }

        return $pageDescription;
    }

    public static function ogImageUrl(?Model $model, ?string $fallbackPath = null): string
    {
        $path = $model->og_image ?? null;

        if (blank($path) && $model instanceof Category && filled($model->category_img)) {
            $path = $model->category_img;
        }

        if (blank($path) && $fallbackPath) {
            $path = $fallbackPath;
        }

        if (blank($path)) {
            return seo_og_image();
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return seo_og_image($path);
    }

    public static function fromSeoPage(\App\Models\SeoPage $page, array $defaults): array
    {
        $pageTitle = filled($page->meta_title)
            ? $page->meta_title
            : $defaults['title'];
        $pageDescription = filled($page->meta_description)
            ? seo_truncate($page->meta_description, 160)
            : seo_truncate($defaults['description'], 160);
        $pageUrl = filled($page->canonical_path)
            ? seo_canonical($page->canonical_path)
            : ($defaults['url'] ?? seo_canonical('/blog'));

        return [
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
            'pageUrl' => $pageUrl,
            'keywords' => $page->meta_keywords ?: ($defaults['keywords'] ?? null),
            'robots' => filled($page->robots) ? $page->robots : config('seo.robots_index'),
            'ogTitle' => filled($page->og_title) ? $page->og_title : $pageTitle,
            'ogDescription' => filled($page->og_description)
                ? seo_truncate($page->og_description, 200)
                : $pageDescription,
            'ogImage' => self::ogImageUrl($page, $defaults['og_image'] ?? null),
        ];
    }

    public static function validationRules(bool $withImage = true): array
    {
        $rules = [
            'meta_title' => ['nullable', 'string', 'max:70'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'og_title' => ['nullable', 'string', 'max:70'],
            'og_description' => ['nullable', 'string', 'max:300'],
            'robots' => ['nullable', 'string', 'max:120'],
            'canonical_path' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9\-\/]*$/i'],
        ];

        if ($withImage) {
            $rules['og_image'] = ['nullable', 'image', 'max:3072'];
        }

        return $rules;
    }

    public static function applyOgImageUpload(Model $model, $request, string $directory = 'src/seo'): void
    {
        if ($request->hasFile('og_image')) {
            $model->og_image = $request->file('og_image')->store($directory, 'public');
        }
    }
}
