<?php

return [
    'site_url' => rtrim(env('SEO_SITE_URL', env('APP_URL', 'https://enot-24.com.ua')), '/'),
    'site_name' => env('SEO_SITE_NAME', 'ЄНОТ-24'),
    'default_description' => 'Хімчистка та прання одягу в Києві від ЄНОТ-24. Професійна чистка, безпечна хімія, кур\'єрська доставка. Актуальні ціни.',
    'og_image' => '/storage/src/logo/enot-white-bg.png',
    'telephone' => '+380678872233',
    'robots_index' => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
    'robots_noindex' => 'noindex, nofollow',
    'show_page_loader' => env('SHOW_PAGE_LOADER', true),
    'page_loader_ttl_days' => (int) env('PAGE_LOADER_TTL_DAYS', 7),
];
