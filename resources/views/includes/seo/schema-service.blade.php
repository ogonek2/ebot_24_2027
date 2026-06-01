@if(!empty($serviceName))
@php
    $serviceSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $serviceName,
        'provider' => [
            '@type' => 'LocalBusiness',
            'name' => config('seo.site_name'),
            'url' => seo_site_url(),
            'telephone' => config('seo.telephone'),
        ],
        'areaServed' => 'Київ',
    ];
    if (!empty($servicePrice) && (float) $servicePrice > 0) {
        $serviceSchema['offers'] = [
            '@type' => 'Offer',
            'price' => (string) number_format((float) $servicePrice, 0, '.', ''),
            'priceCurrency' => 'UAH',
            'url' => $pageUrl,
        ];
    }
@endphp
<script type="application/ld+json">
{!! json_encode($serviceSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif
