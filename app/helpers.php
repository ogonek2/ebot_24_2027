<?php

use Illuminate\Support\Str;
use App\Models\Category;

if (!function_exists('generate_random_code')) {
    function generate_random_code($length = 10)
    {
        return strtoupper(Str::random($length));
    }

    function get_categories()
    {
        return Category::all();
    }
}

if (!function_exists('seo_site_url')) {
    function seo_site_url(): string
    {
        return rtrim(config('seo.site_url'), '/');
    }
}

if (!function_exists('seo_canonical')) {
    function seo_canonical(?string $url = null): string
    {
        if ($url && (Str::startsWith($url, ['http://', 'https://']))) {
            return strtok($url, '?') ?: $url;
        }

        $path = $url ?? request()->getPathInfo();
        $path = '/' . ltrim($path, '/');

        return seo_site_url() . $path;
    }
}

if (!function_exists('seo_truncate')) {
    function seo_truncate(string $text, int $max): string
    {
        $text = trim($text);

        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, max(1, $max - 1))) . '…';
    }
}

if (!function_exists('seo_service_title')) {
    function seo_service_title(string $serviceName): string
    {
        return seo_truncate("{$serviceName} у Києві — ціна, кур'єр | ЄНОТ-24", 60);
    }
}

if (!function_exists('seo_service_description')) {
    function seo_service_description(string $serviceName): string
    {
        return seo_truncate(
            "{$serviceName} у Києві від ЄНОТ-24. Професійна чистка, безпечна хімія, кур'єрська доставка. Актуальні ціни.",
            160
        );
    }
}

if (!function_exists('seo_og_image')) {
    function seo_og_image(?string $path = null): string
    {
        $path = $path ?? config('seo.og_image');

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return seo_site_url() . '/' . ltrim($path, '/');
    }
}

if (!function_exists('seo_paginator_url')) {
    function seo_paginator_url(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        $query = parse_url($url, PHP_URL_QUERY);

        return seo_site_url() . $path . ($query ? '?' . $query : '');
    }
}

if (!function_exists('breadcrumb_home')) {
    function breadcrumb_home(): array
    {
        return ['name' => 'Головна', 'url' => route('welcome')];
    }
}

if (!function_exists('breadcrumb_schema_trail')) {
    function breadcrumb_schema_trail(array $items): array
    {
        return collect($items)->values()->map(function ($item, $index) use ($items) {
            $url = $item['url'] ?? null;

            if (!empty($item['schema_url'])) {
                $url = $item['schema_url'];
            } elseif ($url) {
                if (Str::startsWith($url, ['http://', 'https://'])) {
                    $path = parse_url($url, PHP_URL_PATH) ?: '/';
                    $query = parse_url($url, PHP_URL_QUERY);
                    $url = seo_site_url() . $path . ($query ? '?' . $query : '');
                } else {
                    $url = seo_site_url() . '/' . ltrim($url, '/');
                }
            } elseif ($index === count($items) - 1) {
                $url = seo_canonical();
            }

            return [
                'name' => $item['name'] ?? '',
                'url' => $url ?: seo_canonical('/'),
            ];
        })->all();
    }
}

if (!function_exists('seo_service_faq_items')) {
    function seo_service_faq_items(\App\Models\Service $service, bool $hasPrice, float $displayPrice): array
    {
        $custom = collect($service->faq ?? [])
            ->filter(fn ($item) => filled($item['question'] ?? null) && filled($item['answer'] ?? null))
            ->map(fn ($item) => [
                'question' => trim((string) $item['question']),
                'answer' => trim((string) $item['answer']),
            ])
            ->values()
            ->all();

        if (!empty($custom)) {
            return $custom;
        }

        $name = $service->name;
        $priceAnswer = $hasPrice && $displayPrice > 0
            ? 'Потокова ціна — від ' . number_format($displayPrice, 0, ',', ' ') . ' грн за одиницю. Точну вартість можна додати в кошик або уточнити у менеджера.'
            : 'Вартість залежить від типу тканини та ступеня забруднення. Залиште заявку — ми повідомимо ціну перед прийомом речей.';

        return [
            [
                'question' => 'Скільки коштує ' . mb_strtolower($name) . ' у Києві?',
                'answer' => $priceAnswer,
            ],
            [
                'question' => 'Чи є кур\'єрська доставка для ' . mb_strtolower($name) . '?',
                'answer' => 'Так, ЄНОТ-24 забирає та повертає речі кур\'єром по Києву. Замовити можна на сайті або за телефоном +38 (067) 887-22-33.',
            ],
            [
                'question' => 'Скільки часу займає ' . mb_strtolower($name) . '?',
                'answer' => 'Стандартний термін — від 2 до 5 робочих днів залежно від типу виробу та завантаженості цеху. Термінові замовлення узгоджуються окремо.',
            ],
            [
                'question' => 'Чи безпечна хімія для делікатних тканин?',
                'answer' => 'Ми використовуємо професійні гіпоалергенні засоби та дотримуємося маркування виробу. Для складних речей доступна індивідуальна обробка.',
            ],
        ];
    }
}

if (! function_exists('blog_post_content_html')) {
    /**
     * Обгортає кожну <table> у скрол-контейнер (лише таблиця скролиться, не сторінка).
     */
    function blog_post_content_html(?string $html): string
    {
        $html = (string) $html;

        if ($html === '' || stripos($html, '<table') === false) {
            return $html;
        }

        libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'UTF-8');
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="blog-post-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();

        $tables = $document->getElementsByTagName('table');

        for ($i = $tables->length - 1; $i >= 0; $i--) {
            $table = $tables->item($i);
            if (! $table instanceof DOMElement) {
                continue;
            }

            $parent = $table->parentNode;
            if ($parent instanceof DOMElement && str_contains($parent->getAttribute('class'), 'blog-post-table-scroll')) {
                continue;
            }

            $table->removeAttribute('width');
            if ($table->hasAttribute('style')) {
                $style = preg_replace('/\bwidth\s*:\s*[^;]+;?/i', '', (string) $table->getAttribute('style'));
                $style = trim($style);
                $style === '' ? $table->removeAttribute('style') : $table->setAttribute('style', $style);
            }
            if ($table->hasAttribute('class')) {
                $class = trim(preg_replace('/\b(w-full|max-w-full|table-fixed)\b/', '', (string) $table->getAttribute('class')));
                $class === '' ? $table->removeAttribute('class') : $table->setAttribute('class', $class);
            }

            $wrapper = $document->createElement('div');
            $wrapper->setAttribute('class', 'blog-post-table-scroll w-full max-w-full min-w-0 overflow-x-auto');
            $parent?->replaceChild($wrapper, $table);
            $wrapper->appendChild($table);

            $node = $wrapper->parentNode;
            while ($node instanceof DOMElement && $node->getAttribute('id') !== 'blog-post-root') {
                if ($node->hasAttribute('style')) {
                    $style = preg_replace('/\boverflow(-[xy])?\s*:\s*hidden\s*;?/i', '', (string) $node->getAttribute('style'));
                    $style = trim($style);
                    $style === '' ? $node->removeAttribute('style') : $node->setAttribute('style', $style);
                }
                if ($node->hasAttribute('class')) {
                    $class = trim(preg_replace('/\boverflow-hidden\b/', '', (string) $node->getAttribute('class')));
                    $class === '' ? $node->removeAttribute('class') : $node->setAttribute('class', $class);
                }
                $node = $node->parentNode;
            }
        }

        $root = $document->getElementById('blog-post-root');
        if (! $root) {
            return $html;
        }

        $result = '';
        foreach ($root->childNodes as $child) {
            $result .= $document->saveHTML($child);
        }

        return $result;
    }
}