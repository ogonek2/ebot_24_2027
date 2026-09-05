<?php

namespace App\Services;

use App\Models\B2b;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\cities;
use App\Models\discount;
use App\Models\SeoPage;
use App\Models\Service;
use App\Support\SeoResolver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpaSeo
{
    /**
     * @param  array<string, mixed>  $query
     * @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>}
     */
    public function resolve(string $path, array $query = []): array
    {
        $path = '/' . trim($path, '/');
        if ($path === '/') {
            return $this->home();
        }
        if ($path === '/poslugi-ta-cini') {
            return $this->servicesIndex();
        }
        if (preg_match('#^/poslugi-ta-cini/([^/]+)/posluga/([^/]+)$#', $path, $m)) {
            return $this->service($m[1], $m[2]);
        }
        if (preg_match('#^/poslugi-ta-cini/([^/]+)$#', $path, $m)) {
            return $this->category($m[1]);
        }
        if ($path === '/dlya-biznesu') {
            return $this->b2bIndex();
        }
        if (preg_match('#^/dlya-biznesu/([^/]+)$#', $path, $m)) {
            return $this->b2bShow($m[1]);
        }
        if ($path === '/viklikati-kuryera') {
            return $this->titleOnly('Викликати кур\'єра - Екочистка одягу та домашнього текстилю', $path);
        }
        if (in_array($path, ['/koshyk', '/oformlennya-zamovlennya'], true)) {
            return $this->noindex('Оформлення замовлення - Екочистка одягу та домашнього текстилю', $path);
        }
        if (preg_match('#^/order-success(?:/[^/]+)?$#', $path)) {
            return $this->noindex('Дякуємо за замовлення - Єнот 24 / Хімчистка одягу та килимів у Києві', $path);
        }
        if ($path === '/dostavka') {
            return $this->delivery();
        }
        if ($path === '/lokatsii') {
            return $this->locations();
        }
        if ($path === '/aktsii') {
            return $this->titleOnly('Акції - Єнот 24 / Знижка на послуги з хімчистки. Актуальні акції', $path);
        }
        if (preg_match('#^/aktsii/(\d+)$#', $path, $m)) {
            return $this->promotion((int) $m[1]);
        }
        if ($path === '/kontakty') {
            return $this->contacts();
        }
        if ($path === '/blog') {
            return $this->blogIndex($query);
        }
        if (preg_match('#^/blog/([^/]+)$#', $path, $m)) {
            return $this->blogPost($m[1]);
        }
        if ($path === '/oferta') {
            return $this->titleOnly('Публічна оферта - Єнот 24 Прання/Хімчистка Килимів та Одягу в Києві', $path);
        }
        if ($path === '/privacy-policy') {
            return $this->titleOnly('Політика конфеденційності - Єнот 24 Прання/Хімчистка Килимів та Одягу в Києві', $path);
        }
        if ($path === '/umovy') {
            return $this->titleOnly('Умови використання - Єнот 24 Прання/Хімчистка Килимів та Одягу в Києві', $path);
        }

        throw new NotFoundHttpException('SEO route not found');
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function home(): array
    {
        $canonical = seo_canonical('/');

        return [
            'title' => 'Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві',
            'meta' => [
                $this->meta('description', 'Замовити послугу прання/хімчистки килимів та одягу в компанії ЄНОТ-24. Краща якість хімчистки. Безпечна хімія. Кур\'єрська доставка по Києву.'),
                $this->prop('og:description', 'Замовити послугу прання/хімчистки килимів та одягу Ви можете в компанії Єнот-24. Краща якість хімчистки. Безпечна хімія. Найкоротші терміни. Замовляйте просто зараз!'),
                $this->prop('og:type', 'website'),
                $this->prop('og:image', seo_og_image()),
                $this->meta('format-detection', 'telephone=no'),
                $this->meta('geo.position', '50.4501;30.5234'),
                $this->meta('geo.region', 'UA-30'),
                $this->meta('geo.placename', 'Київ, Україна'),
                $this->meta('DC.title', 'Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві'),
                $this->meta('DC.description', 'Замовити послугу прання/хімчистки килимів та одягу Ви можете в компанії Єнот-24. Краща якість хімчистки. Безпечна хімія. Найкоротші терміни. Замовляйте просто зараз!'),
                $this->meta('DC.subject', 'Прання, хімчистка, Київ, Єнот-24, чистка килимів, одяг'),
                $this->meta('robots', config('seo.robots_index')),
                $this->prop('og:title', 'Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві'),
                $this->prop('og:description', 'Якісні послуги прання та хімчистки в Києві. Звертайтеся до Єнот-24 для чистоти, яку ви заслуговуєте!'),
                $this->prop('og:url', $canonical),
                $this->meta('city', 'Київ'),
                $this->meta('coverage', 'Київ, Україна'),
                $this->meta('revisit-after', '3 days'),
                $this->meta('author', 'Єнот-24'),
                $this->meta('rating', 'general'),
                $this->meta('business', 'Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві'),
                $this->meta('address', 'Київ, Україна'),
                $this->meta('phone', '+38 (067) 887-2233'),
                $this->http('x-dns-prefetch-control', 'on'),
                $this->meta('format-detection', 'telephone=yes'),
                $this->prop('og:locale', 'uk_UA'),
                $this->prop('og:site_name', 'Єнот-24'),
                $this->meta('twitter:card', 'summary_large_image'),
                $this->meta('twitter:title', 'Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві'),
                $this->meta('twitter:description', 'Професійне прання та хімчистка килимів і одягу в Києві. Чистота, якість та вигідні ціни від Єнот-24!'),
                $this->meta('twitter:url', $canonical),
                $this->itemprop('name', 'Єнот-24 - Прання/Хімчистка Килимів та Одягу в Києві'),
                $this->itemprop('addressLocality', 'Київ'),
                $this->itemprop('telephone', '+38 (067) 887-2233'),
                $this->meta('google-site-verification', '-Tye_Cwi5cK0K8x7A1C8Heuxg5Nmxgjh-H5j3vGd6gQ'),
                $this->meta('service', 'Прання килимів Київ, хімчистка одягу Київ, чистка меблів, професійне прання'),
                $this->meta('distribution', 'global'),
                $this->meta('target', 'all'),
                $this->meta('audience', 'Кияни, люди, зацікавлені у чистоті та догляді за одягом і килимами'),
            ],
            'links' => [
                $this->link('canonical', $canonical),
                $this->link('preconnect', 'https://fonts.gstatic.com'),
                $this->link('preconnect', 'https://enot-24.com.ua/'),
            ],
            'jsonLd' => [[
                '@context' => 'https://schema.org',
                '@type' => 'CleaningService',
                'name' => 'ЄНОТ-24',
                'url' => seo_site_url(),
                'telephone' => config('seo.telephone'),
                'image' => seo_og_image(),
                'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Київ', 'addressCountry' => 'UA'],
                'openingHours' => 'Mo-Su 09:00-21:00',
                'priceRange' => '$$',
                'description' => 'Професійні послуги прання та хімчистки одягу в Києві від ЄНОТ-24.',
            ]],
        ];
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function servicesIndex(): array
    {
        $siteName = config('app.name', 'ЄНОТ 24');
        $pageTitle = 'Послуги та ціни - ' . $siteName;
        $pageDescription = 'Повний перелік послуг хімчистки одягу та домашнього текстилю від ЄНОТ 24. Актуальні ціни, кур\'єрська доставка, професійне обслуговування.';
        $pageUrl = seo_canonical('/poslugi-ta-cini');
        $categories = Category::query()->orderBy('sort_order')->orderBy('name')->get();
        $firstCategory = $categories->first();
        $ogImage = $firstCategory && filled($firstCategory->category_img)
            ? seo_og_image($firstCategory->category_img)
            : seo_og_image('storage/src/logo/full_logo.svg');
        $categoryNames = $categories->pluck('name')->take(5)->implode(', ');
        $keywords = 'хімчистка, послуги, ціни, одяг, текстиль, кур\'єрська доставка, ' . $categoryNames . ', ЄНОТ 24';

        return $this->packExtended(
            'Послуги та ціни - Єнот 24. Послуги на Хімчистку, Прання. Прасування, Ремонт та реставрація взуття',
            $pageTitle,
            $pageDescription,
            $pageUrl,
            $keywords,
            config('seo.robots_index'),
            $pageTitle,
            $pageDescription,
            $ogImage,
            'website',
            $siteName
        );
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function category(string $href): array
    {
        $category = Category::where('href', $href)->with(['services' => fn ($q) => $q->orderBy('name')])->firstOrFail();
        $siteName = config('seo.site_name', 'ЄНОТ-24');
        $servicesCount = $category->getAllServices()->count();
        $fallbackTitle = $category->name . ' у Києві — послуги та ціни | ' . $siteName;
        $fallbackDescription = 'Послуги ' . $category->name . ' від ' . $siteName . '. '
            . ($servicesCount > 0 ? $servicesCount . ' послуг у категорії. ' : '')
            . 'Хімчистка з кур\'єрською доставкою. Актуальні ціни.';
        $serviceNames = $category->services->take(5)->pluck('name')->implode(', ');
        $fallbackKeywords = $category->name . ', хімчистка, послуги, ціни, Київ, ' . $siteName . ($serviceNames ? ', ' . $serviceNames : '');

        return $this->packStandard(
            SeoResolver::title($category, $fallbackTitle),
            SeoResolver::description($category, $fallbackDescription),
            SeoResolver::canonical($category, '/poslugi-ta-cini/' . $category->href),
            SeoResolver::keywords($category, $fallbackKeywords),
            SeoResolver::robots($category),
            SeoResolver::ogTitle($category, SeoResolver::title($category, $fallbackTitle)),
            SeoResolver::ogDescription($category, SeoResolver::description($category, $fallbackDescription)),
            SeoResolver::ogImageUrl($category),
            'website',
            optional($category->updated_at)->toAtomString()
        );
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function service(string $categoryHref, string $serviceSlug): array
    {
        $categoryModel = Category::where('href', $categoryHref)->firstOrFail();
        $serviceQuery = Service::where('transform_url', $serviceSlug)->with(['categories', 'groups']);
        $categoryIds = [$categoryModel->id];
        if ($categoryModel->parent_id === null) {
            $categoryIds = array_merge($categoryIds, Category::where('parent_id', $categoryModel->id)->pluck('id')->toArray());
        } elseif ($categoryModel->parent_id) {
            $categoryIds[] = $categoryModel->parent_id;
        }
        $serviceQuery->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds));
        $service = $serviceQuery->first() ?? Service::where('transform_url', $serviceSlug)->with(['categories', 'groups'])->firstOrFail();
        $primaryCategory = $service->getPrimaryCategory() ?? $categoryModel;
        $slug = $service->transform_url ?? $service->href;
        $defaultPath = '/poslugi-ta-cini/' . $primaryCategory->href . '/posluga/' . $slug;

        $pageUrl = SeoResolver::canonical($service, $defaultPath);
        $pageTitle = SeoResolver::title($service, seo_service_title($service->name));
        $pageDescription = SeoResolver::description($service, seo_service_description($service->name));
        $originalPrice = floatval($service->price ?? 0);
        $schemaPrice = $originalPrice;
        if ($originalPrice > 0 && $primaryCategory->hasActiveDiscount()) {
            $schemaPrice = floatval($primaryCategory->calculateDiscountedPrice($originalPrice));
        }
        $faqItems = seo_service_faq_items($service, $originalPrice > 0, $schemaPrice);

        $result = $this->packStandard(
            $pageTitle,
            $pageDescription,
            $pageUrl,
            SeoResolver::keywords($service, $service->name . ', хімчистка, Київ, ЄНОТ-24'),
            SeoResolver::robots($service),
            SeoResolver::ogTitle($service, $pageTitle),
            SeoResolver::ogDescription($service, $pageDescription),
            SeoResolver::ogImageUrl($service),
            'website',
            optional($service->updated_at)->toAtomString()
        );

        $serviceSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service->name,
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => config('seo.site_name'),
                'url' => seo_site_url(),
                'telephone' => config('seo.telephone'),
            ],
            'areaServed' => 'Київ',
        ];
        if ($schemaPrice > 0) {
            $serviceSchema['offers'] = [
                '@type' => 'Offer',
                'price' => (string) number_format($schemaPrice, 0, '.', ''),
                'priceCurrency' => 'UAH',
                'url' => $pageUrl,
            ];
        }
        $result['jsonLd'][] = $serviceSchema;
        if (! empty($faqItems)) {
            $result['jsonLd'][] = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => collect($faqItems)->map(fn ($item) => [
                    '@type' => 'Question',
                    'name' => $item['question'],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['answer']],
                ])->values()->all(),
            ];
        }

        return $result;
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function b2bIndex(): array
    {
        $siteName = config('app.name', 'ЄНОТ 24');
        $pageTitle = 'Для бізнесу / B2B - ' . $siteName;
        $pageDescription = 'Послуги хімчистки одягу та домашнього текстилю для бізнесу від ЄНОТ 24. Корпоративні рішення, оптові ціни, професійне обслуговування.';
        $pageUrl = seo_canonical('/dlya-biznesu');
        $firstB2b = B2b::query()->first();
        $ogImage = $firstB2b && filled($firstB2b->banner) && $firstB2b->banner !== 'Empty'
            ? seo_og_image($firstB2b->banner)
            : seo_og_image('storage/src/logo/full_logo.svg');

        return $this->packExtended(
            'Для бізнесу / B2B - Єнот 24. Послуги хімчистки для вашого бізнесу',
            $pageTitle,
            $pageDescription,
            $pageUrl,
            'B2B, для бізнесу, корпоративні послуги, оптові ціни, хімчистка, одяг, текстиль, ЄНОТ 24, бізнес-рішення',
            config('seo.robots_index'),
            $pageTitle,
            $pageDescription,
            $ogImage,
            'website',
            $siteName
        );
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function b2bShow(string $href): array
    {
        $b2b = B2b::where('href', $href)->firstOrFail();

        return $this->titleOnly(($b2b->title ?? $b2b->name) . ' / B2B - Екочистка одягу та домашнього текстилю', '/dlya-biznesu/' . $href);
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function delivery(): array
    {
        $siteName = config('app.name', 'ЄНОТ 24');
        $pageTitle = 'Доставка та кур\'єр - ' . $siteName;
        $pageDescription = 'Швидка та надійна доставка одягу та домашнього текстилю прямо до дверей від ЄНОТ 24. Кур\'єрська доставка, зручне обслуговування, професійна хімчистка.';
        $pageUrl = seo_canonical('/dostavka');
        $ogImage = seo_og_image('storage/src/logo/full_logo.svg');

        return $this->packExtended(
            'Доставка - Єнот 24 / Хімчистка одягу та килимів / Доставка Київ',
            $pageTitle,
            $pageDescription,
            $pageUrl,
            'доставка, кур\'єр, хімчистка, одяг, текстиль, кур\'єрська доставка, швидка доставка, ЄНОТ 24, двері до дверей',
            config('seo.robots_index'),
            $pageTitle,
            $pageDescription,
            $ogImage,
            'website',
            $siteName
        );
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function locations(): array
    {
        $siteName = config('app.name', 'ЄНОТ 24');
        $pageTitle = 'Локації - ' . $siteName;
        $pageDescription = 'Знайдіть найближче відділення ЄНОТ 24 у вашому місті. Хімчистка одягу та домашнього текстилю з кур\'єрською доставкою. Адреси, графік роботи, контакти.';
        $pageUrl = seo_canonical('/lokatsii');
        $ogImage = seo_og_image('storage/src/logo/full_logo.svg');
        $cities = cities::with(['locations' => fn ($q) => $q->where('sort_order', '>=', 1)])
            ->whereHas('locations', fn ($q) => $q->where('sort_order', '>=', 1))
            ->orderBy('id')
            ->get();
        $cityNames = $cities->pluck('city')->implode(', ');
        $keywords = 'локації, адреси, відділення, хімчистка, ЄНОТ 24, ' . $cityNames . ', графік роботи, контакти';

        return $this->packExtended(
            'Наші приймальні пункти - Єнот 24 / Хімчистка одягу та килимів у Києві',
            $pageTitle,
            $pageDescription,
            $pageUrl,
            $keywords,
            config('seo.robots_index'),
            $pageTitle,
            $pageDescription,
            $ogImage,
            'website',
            $siteName
        );
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function contacts(): array
    {
        $siteName = config('app.name', 'ЄНОТ 24');
        $pageTitle = 'Контакти - ' . $siteName;
        $pageDescription = 'Зв\'яжіться з ЄНОТ 24 будь-яким зручним способом. Телефони, email, Instagram. Ми завжди на зв\'язку! Хімчистка одягу та домашнього текстилю з кур\'єрською доставкою.';
        $pageUrl = seo_canonical('/kontakty');
        $ogImage = seo_og_image('storage/src/logo/full_logo.svg');

        return $this->packExtended(
            'Контакти - Єнот 24 / Хімчистка одягу та килимів у Києві',
            $pageTitle,
            $pageDescription,
            $pageUrl,
            'контакти, телефон, email, Instagram, зв\'язок, хімчистка, ЄНОТ 24, кур\'єрська доставка, одяг, текстиль',
            config('seo.robots_index'),
            $pageTitle,
            $pageDescription,
            $ogImage,
            'website',
            $siteName
        );
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function promotion(int $id): array
    {
        $promotion = discount::findOrFail($id);
        $siteName = config('app.name', 'ЄНОТ 24');
        $pageTitle = $promotion->name . ' - Акції - ' . $siteName;
        $pageDescription = filled($promotion->discount_action)
            ? seo_truncate(strip_tags((string) $promotion->discount_action), 160)
            : 'Спеціальна акція від ЄНОТ 24. Хімчистка одягу та домашнього текстилю з кур\'єрською доставкою.';
        $pageUrl = seo_canonical('/aktsii/' . $promotion->id);
        $ogImage = filled($promotion->banner)
            ? seo_og_image($promotion->banner)
            : seo_og_image('storage/src/logo/full_logo.svg');

        return $this->packExtended(
            $promotion->name . ' - Акції / Активна знижка на послуги з хімчистки. Єнот 24 / Знижка ' . $promotion->discount_action,
            $pageTitle,
            $pageDescription,
            $pageUrl,
            'акції, знижки, хімчистка, одяг, текстиль, ' . $promotion->name . ', ЄНОТ 24',
            config('seo.robots_index'),
            $pageTitle,
            $pageDescription,
            $ogImage,
            'article',
            $siteName,
            $promotion->name
        );
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>}
     */
    private function blogIndex(array $query): array
    {
        $siteName = config('seo.site_name', 'ЄНОТ-24');
        $perPage = max(1, min(100, (int) ($query['per_page'] ?? 16) ?: 16));
        $currentPage = max(1, (int) ($query['page'] ?? 1));
        $seoPage = SeoPage::forKey(SeoPage::KEY_BLOG_INDEX);

        $defaults = [
            'title' => $currentPage > 1 ? 'Блог — сторінка ' . $currentPage . ' | ' . $siteName : 'Блог | ' . $siteName,
            'description' => $currentPage > 1
                ? 'Сторінка ' . $currentPage . ' блогу ' . $siteName . ': статті про догляд за одягом, килимами та текстилем у Києві.'
                : 'Корисні матеріали про догляд за одягом, килимами та текстилем. Поради від хімчистки у Києві.',
            'url' => seo_canonical('/blog' . ($currentPage > 1 ? '?page=' . $currentPage : '')),
            'keywords' => 'блог, хімчистка, прання, догляд за одягом, ' . $siteName . ', Київ',
        ];

        $seo = SeoResolver::fromSeoPage($seoPage, $defaults);
        if ($currentPage > 1 && blank($seoPage->meta_title)) {
            $seo['pageTitle'] = $defaults['title'];
        }
        if ($currentPage > 1 && blank($seoPage->meta_description)) {
            $seo['pageDescription'] = seo_truncate($defaults['description'], 160);
        }
        if ($currentPage > 1) {
            $seo['pageUrl'] = $defaults['url'];
        }

        $result = $this->packStandard(
            $seo['pageTitle'],
            $seo['pageDescription'],
            $seo['pageUrl'],
            $seo['keywords'],
            $seo['robots'],
            $seo['ogTitle'],
            $seo['ogDescription'],
            $seo['ogImage'],
            'website',
            null
        );

        $result['links'][] = $this->link('alternate', seo_canonical('/blog/feed.xml'), 'application/rss+xml', 'Блог ' . $siteName);

        $posts = BlogPost::query()->published()->orderByDesc('published_at')->paginate($perPage, ['*'], 'page', $currentPage);
        if ($posts->previousPageUrl()) {
            $result['links'][] = $this->link('prev', seo_paginator_url($posts->previousPageUrl()) ?? '');
        }
        if ($posts->nextPageUrl()) {
            $result['links'][] = $this->link('next', seo_paginator_url($posts->nextPageUrl()) ?? '');
        }

        return $result;
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function blogPost(string $slug): array
    {
        $post = BlogPost::query()->where('slug', $slug)->published()->firstOrFail();
        $siteName = config('app.name', 'Єнот-24');
        $fallbackTitle = $post->title . ' | Блог ' . $siteName;
        $fallbackDescription = \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 160);
        $defaultPath = '/blog/' . $post->slug;

        $pageTitle = SeoResolver::title($post, $fallbackTitle);
        $pageDescription = SeoResolver::description($post, $fallbackDescription);
        $pageUrl = SeoResolver::canonical($post, $defaultPath);
        $ogImage = SeoResolver::ogImageUrl($post, $post->featured_image);

        $result = $this->packStandard(
            $pageTitle,
            $pageDescription,
            $pageUrl,
            SeoResolver::keywords($post, 'хімчистка, прання, ' . $post->title . ', ' . $siteName),
            SeoResolver::robots($post),
            SeoResolver::ogTitle($post, $pageTitle),
            SeoResolver::ogDescription($post, $pageDescription),
            $ogImage,
            'article',
            $post->updated_at->toIso8601String()
        );

        $result['meta'][] = $this->prop('og:image:alt', $post->title);
        if ($post->published_at) {
            $result['meta'][] = $this->prop('article:published_time', $post->published_at->toIso8601String());
        }
        $result['meta'][] = $this->meta('author', $siteName);
        $result['links'][] = $this->link('alternate', seo_canonical('/blog/feed.xml'), 'application/rss+xml', 'Блог ' . $siteName);
        $result['jsonLd'][] = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->title,
            'description' => $pageDescription,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at->toIso8601String(),
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $pageUrl],
            'image' => [$ogImage],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'logo' => ['@type' => 'ImageObject', 'url' => seo_og_image('storage/src/logo/logo-enot24.png')],
            ],
        ];

        return $result;
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function titleOnly(string $title, string $path): array
    {
        $pageUrl = seo_canonical($path);

        return [
            'title' => $title,
            'meta' => $this->defaultMetaTags($title, seo_truncate(config('seo.default_description'), 160), $pageUrl),
            'links' => [$this->link('canonical', $pageUrl)],
            'jsonLd' => [],
        ];
    }

    /** @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>} */
    private function noindex(string $title, string $path): array
    {
        return [
            'title' => $title,
            'meta' => [$this->meta('robots', config('seo.robots_noindex'))],
            'links' => [],
            'jsonLd' => [],
        ];
    }

    /**
     * @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>}
     */
    private function packStandard(
        string $pageTitle,
        string $pageDescription,
        string $pageUrl,
        ?string $keywords,
        string $robots,
        string $ogTitle,
        string $ogDescription,
        string $ogImage,
        string $ogType = 'website',
        ?string $modifiedTime = null
    ): array {
        $pageDescription = seo_truncate($pageDescription, 160);
        $meta = [];
        if ($keywords) {
            $meta[] = $this->meta('keywords', $keywords);
        }
        $meta[] = $this->meta('description', $pageDescription);
        $meta[] = $this->meta('robots', $robots);
        if ($modifiedTime) {
            $meta[] = $this->prop('article:modified_time', $modifiedTime);
        }
        $meta = array_merge($meta, [
            $this->prop('og:type', $ogType),
            $this->prop('og:url', $pageUrl),
            $this->prop('og:title', $ogTitle),
            $this->prop('og:description', $ogDescription),
            $this->prop('og:image', $ogImage),
            $this->prop('og:locale', 'uk_UA'),
            $this->prop('og:site_name', config('seo.site_name')),
            $this->meta('twitter:card', 'summary_large_image'),
            $this->meta('twitter:title', $ogTitle),
            $this->meta('twitter:description', $ogDescription),
            $this->meta('twitter:image', $ogImage),
        ]);

        return [
            'title' => $pageTitle,
            'meta' => $meta,
            'links' => [$this->link('canonical', $pageUrl)],
            'jsonLd' => [],
        ];
    }

    /**
     * @return array{title: string, meta: list<array<string, string>>, links: list<array<string, string>>, jsonLd: list<array<string, mixed>>}
     */
    private function packExtended(
        string $documentTitle,
        string $pageTitle,
        string $pageDescription,
        string $pageUrl,
        string $keywords,
        string $robots,
        string $ogTitle,
        string $ogDescription,
        string $ogImage,
        string $ogType,
        string $siteName,
        ?string $ogImageAlt = null
    ): array {
        $ogImageAlt ??= $pageTitle;

        return [
            'title' => $documentTitle,
            'meta' => [
                $this->meta('description', $pageDescription),
                $this->meta('keywords', $keywords),
                $this->prop('og:type', $ogType),
                $this->prop('og:url', $pageUrl),
                $this->prop('og:title', $ogTitle),
                $this->prop('og:description', $ogDescription),
                $this->prop('og:image', $ogImage),
                $this->prop('og:image:width', '1200'),
                $this->prop('og:image:height', '630'),
                $this->prop('og:image:alt', $ogImageAlt),
                $this->prop('og:site_name', $siteName),
                $this->prop('og:locale', 'uk_UA'),
                $this->meta('twitter:card', 'summary_large_image'),
                $this->meta('twitter:url', $pageUrl),
                $this->meta('twitter:title', $ogTitle),
                $this->meta('twitter:description', $ogDescription),
                $this->meta('twitter:image', $ogImage),
                $this->meta('robots', $robots),
                $this->meta('author', $siteName),
            ],
            'links' => [$this->link('canonical', $pageUrl)],
            'jsonLd' => [],
        ];
    }

    /** @return list<array<string, string>> */
    private function defaultMetaTags(string $pageTitle, string $pageDescription, string $pageUrl): array
    {
        $ogImage = seo_og_image();

        return [
            $this->meta('description', $pageDescription),
            $this->meta('robots', config('seo.robots_index')),
            $this->prop('og:type', 'website'),
            $this->prop('og:url', $pageUrl),
            $this->prop('og:title', $pageTitle),
            $this->prop('og:description', $pageDescription),
            $this->prop('og:image', $ogImage),
            $this->prop('og:locale', 'uk_UA'),
            $this->prop('og:site_name', config('seo.site_name')),
            $this->meta('twitter:card', 'summary_large_image'),
            $this->meta('twitter:title', $pageTitle),
            $this->meta('twitter:description', $pageDescription),
            $this->meta('twitter:image', $ogImage),
        ];
    }

    /** @return array<string, string> */
    private function meta(string $name, string $content): array
    {
        return ['name' => $name, 'content' => $content];
    }

    /** @return array<string, string> */
    private function prop(string $property, string $content): array
    {
        return ['property' => $property, 'content' => $content];
    }

    /** @return array<string, string> */
    private function itemprop(string $itemprop, string $content): array
    {
        return ['itemprop' => $itemprop, 'content' => $content];
    }

    /** @return array<string, string> */
    private function http(string $httpEquiv, string $content): array
    {
        return ['httpEquiv' => $httpEquiv, 'content' => $content];
    }

    /** @return array<string, string> */
    private function link(string $rel, string $href, ?string $type = null, ?string $title = null): array
    {
        $link = ['rel' => $rel, 'href' => $href];
        if ($type) {
            $link['type'] = $type;
        }
        if ($title) {
            $link['title'] = $title;
        }

        return $link;
    }
}
