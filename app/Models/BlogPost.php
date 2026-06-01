<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'robots',
        'canonical_path',
        'published_at',
        'featured_image',
        'related_service_ids',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'related_service_ids' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (BlogPost $post) {
            if (blank($post->slug) && filled($post->title)) {
                $post->slug = static::uniqueSlug(Service::generateHref($post->title), $post->id);
            }
        });
    }

    public static function uniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base ?: 'post';
        $n = 0;
        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . ++$n;
        }

        return $slug;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at && $this->published_at->lte(now());
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'blog_post_category');
    }

    public function relatedServices()
    {
        $ids = array_values(array_filter(array_map('intval', (array) ($this->related_service_ids ?? []))));

        if (empty($ids)) {
            return collect();
        }

        $services = Service::query()
            ->whereIn('id', $ids)
            ->with('categories')
            ->get();

        return $services->sortBy(function (Service $service) use ($ids) {
            return array_search($service->id, $ids, true);
        })->values();
    }

    public function relatedPostsByRelevance(int $limit = 6)
    {
        $candidates = static::query()
            ->published()
            ->where('id', '!=', $this->id)
            ->orderByDesc('published_at')
            ->limit(80)
            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        $myServices = array_map('intval', (array) ($this->related_service_ids ?? []));
        $myTokens = $this->relevanceTokens(
            ($this->title ?? '') . ' ' . ($this->meta_keywords ?? '') . ' ' . strip_tags($this->meta_description ?? '')
        );

        $scored = $candidates->map(function (BlogPost $other) use ($myServices, $myTokens) {
            $score = 0;

            $otherServices = array_map('intval', (array) ($other->related_service_ids ?? []));
            $score += count(array_intersect($myServices, $otherServices)) * 15;

            $otherTokens = $other->relevanceTokens(
                ($other->title ?? '') . ' ' . ($other->meta_keywords ?? '') . ' ' . strip_tags($other->meta_description ?? '')
            );
            $score += count(array_intersect($myTokens, $otherTokens));

            return ['post' => $other, 'score' => $score];
        })->sortByDesc('score');

        $picked = $scored->filter(fn ($row) => $row['score'] > 0)
            ->take($limit)
            ->pluck('post');

        if ($picked->count() < $limit) {
            $exclude = $picked->pluck('id')->push($this->id)->all();
            $fallback = $candidates->whereNotIn('id', $exclude)->take($limit - $picked->count());
            $picked = $picked->merge($fallback);
        }

        return $picked->values();
    }

    private function relevanceTokens(string $text): array
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $stop = [
            'та', 'і', 'й', 'в', 'у', 'на', 'до', 'з', 'із', 'що', 'як', 'для', 'або', 'але',
            'це', 'той', 'та', 'ті', 'при', 'від', 'про', 'без', 'над', 'під', 'між', 'ще', 'вже',
            'the', 'and', 'for', 'with', 'from', 'your', 'our',
        ];

        return array_values(array_unique(array_filter($words, function ($word) use ($stop) {
            return mb_strlen($word) >= 3 && !in_array($word, $stop, true);
        })));
    }
}
