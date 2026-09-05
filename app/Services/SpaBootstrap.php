<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\CtaHeader;
use App\Models\cities;
use App\Models\discount;
use App\Models\locations;
use App\Models\Service;
use Illuminate\Support\Collection;

class SpaBootstrap
{
    private static function storageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return url('/storage/' . ltrim($path, '/'));
    }

    private static function parseCoordinate(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        return (float) str_replace(',', '.', $raw);
    }

    public static function forHome(): array
    {
        return [
            'route' => '/',
            'categories' => self::serializeCategories(self::loadCategories()),
            'discounts' => self::serializeDiscounts(),
            'branches' => self::serializeBranches(),
            'blogPosts' => self::serializeBlogPosts(),
            'ctaHeaders' => self::serializeCtaHeaders(),
            'assets' => self::assets(),
        ];
    }

    public static function forServices(): array
    {
        return self::catalog('/poslugi-ta-cini');
    }

    public static function catalog(string $route, array $extra = []): array
    {
        return array_merge([
            'route' => $route,
            'categories' => self::serializeCategories(self::loadCategories()),
            'assets' => self::assets(),
        ], $extra);
    }

    public static function resolveForRoute(string $route): array
    {
        $route = '/' . trim($route, '/');
        if ($route === '/') {
            $route = '/';
        } elseif ($route !== '/') {
            $route = rtrim($route, '/') ?: '/';
        }

        if ($route === '/') {
            return self::forHome();
        }

        if ($route === '/poslugi-ta-cini' || str_starts_with($route, '/poslugi-ta-cini/')) {
            if (preg_match('#^/poslugi-ta-cini/([^/]+)/posluga/([^/]+)$#', $route, $m)) {
                return self::catalog($route, [
                    'categoryHref' => $m[1],
                    'serviceHref' => $m[2],
                ]);
            }

            if (preg_match('#^/poslugi-ta-cini/([^/]+)$#', $route, $m)) {
                return self::catalog($route, ['activeCategory' => $m[1]]);
            }

            return self::forServices();
        }

        if ($route === '/lokatsii') {
            return self::catalog($route, [
                'branches' => self::serializeBranches(),
                'locationCities' => self::serializeLocationCities(),
            ]);
        }

        if ($route === '/aktsii' || str_starts_with($route, '/aktsii/')) {
            return self::catalog($route, ['discounts' => self::serializeDiscounts()]);
        }

        if ($route === '/blog' || str_starts_with($route, '/blog/')) {
            return self::catalog($route, ['blogPosts' => self::serializeBlogPosts()]);
        }

        return self::catalog($route);
    }

    public static function serializeServiceDetail(Service $service, ?Category $category): array
    {
        $primary = $service->getPrimaryCategory() ?? $category ?? $service->categories->first();
        if ($primary && !$primary->relationLoaded('parent')) {
            $primary->load('parent');
        }

        $base = $primary
            ? self::serializeService($service, $primary)
            : self::serializeService($service, $service->categories->first());

        $slug = $service->transform_url ?? $service->href;
        $htmlBody = $service->description ?: $service->value;
        $parent = $primary?->parent;

        return array_merge($base, [
            'description' => $service->description,
            'content' => $htmlBody,
            'excerpt' => $htmlBody
                ? \Illuminate\Support\Str::limit(strip_tags($htmlBody), 160)
                : null,
            'image' => $service->og_image ? asset('storage/' . $service->og_image) : null,
            'categoryTitle' => $primary?->name,
            'categoryHref' => $primary?->href,
            'parentCategoryTitle' => $parent?->name,
            'parentCategoryHref' => $parent?->href,
            'url' => $primary && $slug
                ? '/poslugi-ta-cini/' . $primary->href . '/posluga/' . $slug
                : null,
            'faq' => $service->faq ?? [],
        ]);
    }

    public static function serializeServiceNavItem(Service $service): ?array
    {
        $category = $service->getPrimaryCategory();
        $slug = $service->transform_url ?? $service->href;

        if (!$category?->href || !$slug) {
            return null;
        }

        $base = self::serializeService($service, $category);

        return [
            'id' => $service->id,
            'name' => $service->name,
            'href' => $slug,
            'categoryHref' => $category->href,
            'categoryTitle' => $category->name,
            'price' => $base['price'],
            'url' => '/poslugi-ta-cini/' . $category->href . '/posluga/' . $slug,
        ];
    }

    public static function serializeAllServicesNav(): array
    {
        $items = [];

        Service::query()
            ->whereNotNull('transform_url')
            ->with('categories')
            ->orderBy('name')
            ->chunk(200, function ($chunk) use (&$items) {
                foreach ($chunk as $service) {
                    $row = self::serializeServiceNavItem($service);
                    if ($row) {
                        $items[] = $row;
                    }
                }
            });

        return $items;
    }

    public static function loadCategories(): Collection
    {
        return Category::with([
            'services' => fn ($q) => $q->orderBy('name'),
            'children.services' => fn ($q) => $q->orderBy('name'),
        ])
            ->whereNull('parent_id')
            ->where(fn ($q) => $q->whereHas('services')->orWhereHas('children.services'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public static function serializeCategories(Collection $categories): array
    {
        $result = [];

        foreach ($categories->filter(fn (Category $c) => $c->getAllServices()->isNotEmpty()) as $category) {
            $result[] = self::serializeCategoryEntry($category);

            foreach ($category->children as $child) {
                if ($child->services->isNotEmpty()) {
                    $result[] = self::serializeCategoryEntry($child, $category);
                }
            }
        }

        return $result;
    }

    public static function serializeCategoryEntry(Category $category, ?Category $parent = null): array
    {
        $services = $parent === null
            ? $category->getAllServices()->sortBy('name')->values()
            : $category->services->sortBy('name')->values();

        return [
            'id' => $category->href,
            'title' => $category->name,
            'parentId' => $parent?->href,
            'parentTitle' => $parent?->name,
            'iconUrl' => self::storageUrl($category->category_img)
                ?: self::storageUrl($parent?->category_img),
            'serviceCount' => $services->count(),
            'items' => $services
                ->map(fn (Service $s) => self::serializeService($s, $category))
                ->values()
                ->all(),
        ];
    }

    public static function serializeService(Service $service, Category $category): array
    {
        $originalPrice = floatval($service->price ?? 0);
        $individualPrice = floatval($service->individual_price ?? 0);
        $hasPrice = $originalPrice > 0;
        $discountedPrice = $originalPrice;
        $hasDiscount = false;

        if ($hasPrice) {
            foreach ($service->categories as $serviceCategory) {
                if ($serviceCategory->hasActiveDiscount()) {
                    $discountedPrice = floatval($serviceCategory->calculateDiscountedPrice($originalPrice));
                    $hasDiscount = true;
                    break;
                }
            }
            if (!$hasDiscount && $category->hasActiveDiscount()) {
                $discountedPrice = floatval($category->calculateDiscountedPrice($originalPrice));
                $hasDiscount = true;
            }
        }

        $primary = $service->getPrimaryCategory() ?? $category;

        return [
            'id' => $service->id,
            'name' => $service->name,
            'href' => $service->transform_url ?? $service->href,
            'categoryHref' => $primary->href,
            'price' => $hasPrice ? number_format($hasDiscount ? $discountedPrice : $originalPrice, 0, '.', ',') . '₴' : 'Ціна за запитом',
            'priceBatch' => $hasPrice ? number_format($hasDiscount ? $discountedPrice : $originalPrice, 0, '.', ',') . '₴' : 'Ціна за запитом',
            'individualPrice' => $individualPrice > 0
                ? number_format($individualPrice, 0, '.', ',') . '₴'
                : null,
            'oldPrice' => $hasDiscount ? number_format($originalPrice, 0, '.', ',') . '₴' : null,
            'promo' => $hasDiscount || !empty($service->marker),
            'marker' => $service->marker,
        ];
    }

    public static function serializeDiscounts(): array
    {
        return discount::orderBy('sort_order')->orderByDesc('created_at')->get()->map(fn ($d) => [
            'id' => $d->id,
            'name' => $d->name ?? 'Акція',
            'discountAction' => $d->discount_action,
            'locations' => $d->locations,
            'banner' => self::storageUrl($d->banner),
            'color' => $d->color,
            'textColor' => $d->text_color,
            'discountColor' => $d->discount_color,
            'url' => '/aktsii/' . $d->id,
        ])->values()->all();
    }

    public static function serializeBranches(): array
    {
        return locations::with('cityRelation')
            ->where('sort_order', '>=', 1)
            ->orderBy('sort_order')
            ->orderBy('city')
            ->get()
            ->map(fn ($loc) => [
                'id' => $loc->id,
                'city' => $loc->cityRelation->city ?? 'Київ',
                'address' => $loc->street,
                'workingHours' => $loc->workinghourse ?? '10:00-20:00 Без Вихідних',
                'image' => self::storageUrl($loc->banner),
                'linkMap' => $loc->link_map,
                'lat' => self::parseCoordinate($loc->lat),
                'lng' => self::parseCoordinate($loc->lng),
                'value' => $loc->value,
                'seoLink' => $loc->seo_link,
            ])
            ->values()
            ->all();
    }

    public static function serializeLocationCities(): array
    {
        return cities::with(['locations' => function ($query) {
            $query->where('sort_order', '>=', 1)
                ->orderBy('sort_order')
                ->orderBy('street');
        }])
            ->whereHas('locations', fn ($query) => $query->where('sort_order', '>=', 1))
            ->orderBy('id')
            ->get()
            ->map(fn ($city) => [
                'id' => $city->id,
                'name' => $city->city,
                'locations' => $city->locations->map(function ($loc) {
                    $lat = self::parseCoordinate($loc->lat);
                    $lng = self::parseCoordinate($loc->lng);

                    return [
                        'id' => $loc->id,
                        'street' => $loc->street,
                        'value' => $loc->value,
                        'workingHours' => $loc->workinghourse,
                        'linkMap' => $loc->link_map ?: (
                            $lat !== null && $lng !== null
                                ? 'https://www.google.com/maps?q=' . $lat . ',' . $lng . '&hl=uk'
                                : null
                        ),
                        'lat' => $lat,
                        'lng' => $lng,
                        'image' => self::storageUrl($loc->banner),
                        'seoLink' => $loc->seo_link,
                    ];
                })->values()->all(),
            ])
            ->values()
            ->all();
    }

    public static function serializeBlogPosts(): array
    {
        return BlogPost::published()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'slug' => $p->slug,
                'title' => $p->title,
                'publishedAt' => $p->published_at?->format('d.m.Y'),
                'image' => self::storageUrl($p->featured_image),
                'url' => '/blog/' . $p->slug,
                'excerpt' => \Illuminate\Support\Str::limit(strip_tags($p->content ?? ''), 160),
            ])
            ->values()
            ->all();
    }

    public static function serializeCtaHeaders(): array
    {
        return CtaHeader::with('iconRelation')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'subtitle' => $c->subtitle,
                'url' => $c->resolved_url,
                'iconUrl' => self::storageUrl($c->iconRelation?->file_path ?? $c->icon),
            ])
            ->values()
            ->all();
    }

    public static function assets(): array
    {
        return [
            'logo' => url('/storage/src/logo/nobg_enot24.svg'),
            'logoFull' => url('/storage/src/logo/enot24.svg'),
            'linesPattern' => url('/storage/src/ill/lines.svg'),
            'storageBase' => url('/storage'),
        ];
    }
}
