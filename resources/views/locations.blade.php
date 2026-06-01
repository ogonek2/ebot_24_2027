@extends('layouts.app')

@section('title', 'Наші приймальні пункти - Єнот 24 / Хімчистка одягу та килимів у Києві')

@section('styles')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@php
    $siteName = config('app.name', 'ЄНОТ 24');
    $pageTitle = 'Локації - ' . $siteName;
    $pageDescription = 'Знайдіть найближче відділення ЄНОТ 24 у вашому місті. Хімчистка одягу та домашнього текстилю з кур\'єрською доставкою. Адреси, графік роботи, контакти.';
    $pageUrl = route('locations_page');
    
    // Используем дефолтное изображение для локаций
    $ogImage = asset('storage/src/logo/full_logo.svg');
    
    // Формируем keywords из названий городов
    $cityNames = $cities->pluck('city')->implode(', ');
    $keywords = 'локації, адреси, відділення, хімчистка, ЄНОТ 24, ' . $cityNames . ', графік роботи, контакти';
@endphp

@section('seo_tags')
    {{-- Basic Meta Tags --}}
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $keywords }}">
    
    {{-- Open Graph Meta Tags --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $pageUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="{{ $pageTitle }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="uk_UA">
    
    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $pageUrl }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    
    {{-- Additional Meta Tags --}}
    <meta name="robots" content="{{ config('seo.robots_index') }}">
    <link rel="canonical" href="{{ $pageUrl }}">
    <meta name="author" content="{{ $siteName }}">
@endsection

@php
    $breadcrumbs = [breadcrumb_home(), ['name' => 'Локації']];
@endphp

@section('content')
    <div class="container mx-auto px-4 py-8 lg:py-12">
        @include('includes.elements.breadcrumbs', ['breadcrumbs' => $breadcrumbs, 'wrapperClass' => 'px-0 mb-6 md:mb-8'])

        <div class="flex flex-col lg:flex-row gap-6 lg:gap-8">
            {{-- Left Column: Locations List --}}
            <div class="lg:w-1/2 lg:max-w-2xl">
                {{-- Page Header --}}
                <div class="mb-8 lg:mb-12">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-4">
                        Наші локації
                    </h1>
                    <p class="text-lg text-gray-600 max-w-3xl">
                        Знайдіть найближче відділення ЄНОТ 24 у вашому місті
                    </p>
                </div>
                <div class="space-y-8">
                    @forelse($cities as $city)
                        <div class="location-city-group">
                            {{-- City Header --}}
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                {{ $city->city }}
                            </h2>

                            {{-- Locations in this city --}}
                            <div class="space-y-4">
                                @foreach($city->locations as $location)
                                    <div class="location-card bg-white rounded-3xl p-6 hover:shadow-xl transition-all duration-300 border border-gray-100 relative group"
                                        data-id="{{ $location->id }}"
                                        data-lat="{{ $location->lat }}" 
                                        data-lng="{{ $location->lng }}"
                                        data-street="{{ $location->street }}"
                                        onmouseenter="updateMap({{ $location->lat }}, {{ $location->lng }}, '{{ addslashes($location->street) }}')">
                                        {{-- Street and Address --}}
                                        <div class="mb-4">
                                            <h3 class="text-xl font-sans text-gray-900 mb-2">
                                                {{ $location->street }}
                                            </h3>
                                            @if($location->value)
                                                <p class="text-gray-600 text-sm">
                                                    {{ $location->value }}
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Working Hours --}}
                                        @if($location->workinghourse)
                                            <div class="mb-4 flex items-start gap-3">
                                                <i class="fas fa-clock text-primary mt-1"></i>
                                                <span class="font-bold text-primary">{{ $location->workinghourse }}</span>
                                            </div>
                                        @endif

                                        {{-- Google Maps Link with Arrow --}}
                                        @if($location->link_map)
                                            <div class="mt-4 pt-4 border-t border-gray-200">
                                                <a href="{{ $location->link_map }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center gap-2 text-primary hover:text-primary/80 font-semibold transition-all duration-200 group/link"
                                                   onclick="event.stopPropagation()">
                                                    <i class="fab fa-google text-primary"></i>
                                                    <span class="text-sm">Відкрити в Google Maps</span>
                                                    <i class="fas fa-arrow-right group-hover/link:translate-x-1 transition-transform duration-200"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-xl p-8 text-center">
                            <i class="fas fa-map-marker-alt text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-600 text-lg">
                                Локації будуть додані найближчим часом
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Right Column: Google Maps (Sticky) --}}
            <div class="lg:w-1/2">
                <div class="sticky top-24 lg:top-28">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">

                        {{-- Google Maps Container --}}
                        <div id="location-map" class="w-full" style="height: 600px;"></div>

                        {{-- Map Controls Info --}}
                        <div class="p-4 bg-gray-50 border-t border-gray-200">
                            <p class="text-xs text-gray-500 text-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                Наведіть на локацію зліва, щоб переглянути її на карті
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        // Глобальные переменные для карты
        let map;
        let markers = [];
        let hoverTimeout;
        let currentHighlightedCard = null;
        let customMarkerIcon = null;

        @php
            // Получаем все локации для отображения
            $allLocations = [];
            $selectedLocation = null;
            $requestSelectedLocationId = $selectedLocationId ?? null;
            
            foreach ($cities as $city) {
                foreach ($city->locations as $location) {
                    if ($location->lat && $location->lng) {
                        $allLocations[] = [
                            'id' => $location->id,
                            'lat' => $location->lat,
                            'lng' => $location->lng,
                            'street' => $location->street,
                            'link_map' => $location->link_map ?: "https://www.google.com/maps?q={$location->lat},{$location->lng}&hl=uk"
                        ];
                        
                        // Если это выбранная локация, сохраняем её
                        if ($requestSelectedLocationId && $location->id == $requestSelectedLocationId) {
                            $selectedLocation = $location;
                        }
                    }
                }
            }

            // Определяем локацию по умолчанию
            $defaultLocation = $selectedLocation;
            if (!$defaultLocation && $cities->isNotEmpty()) {
                $firstCity = $cities->first();
                if ($firstCity && $firstCity->locations && $firstCity->locations->isNotEmpty()) {
                    $defaultLocation = $firstCity->locations->first();
                }
            }

            // Координаты по умолчанию (Киев)
            $defaultLat = $defaultLocation && $defaultLocation->lat ? $defaultLocation->lat : 50.4501;
            $defaultLng = $defaultLocation && $defaultLocation->lng ? $defaultLocation->lng : 30.5234;
            $defaultZoom = $selectedLocation ? 16 : 13;
            $jsSelectedLocationId = $selectedLocation ? $selectedLocation->id : null;
        @endphp

        // Инициализация карты после загрузки Leaflet
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof L === 'undefined') {
                console.error('Leaflet не загружен');
                return;
            }

            const defaultLat = {{ $defaultLat }};
            const defaultLng = {{ $defaultLng }};
            const defaultZoom = {{ $defaultZoom }};
            const selectedLocationId = @json($jsSelectedLocationId);

            // Создаем карту
            map = L.map("location-map").setView([defaultLat, defaultLng], defaultZoom);

            // Добавляем Google Maps-like стиль
            L.tileLayer("https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}", {
                subdomains: ["mt0", "mt1", "mt2", "mt3"],
                attribution: '&copy; Google Maps',
                maxZoom: 20
            }).addTo(map);

            // Создаем кастомный маркер
            customMarkerIcon = L.icon({
                iconUrl: '{{ asset("storage/src/logo/logo_location.svg") }}',
                iconSize: [50, 50],
                iconAnchor: [25, 50],
                popupAnchor: [0, -50]
            });

            // Добавляем все маркеры
            const locations = @json($allLocations);
            
            locations.forEach(function(location, index) {
                const marker = L.marker([location.lat, location.lng], {
                    icon: customMarkerIcon
                })
                .addTo(map)
                .bindPopup(
                    `<a href="${location.link_map}" target="_blank"><strong>${location.street}</strong></a>`
                );

                // Сохраняем ID локации в маркере для связи
                marker.locationId = location.id;
                markers.push(marker);
            });

            // Выделяем локацию (выбранную или первую по умолчанию)
            let targetLocationCard = null;
            
            if (selectedLocationId) {
                // Находим карточку выбранной локации
                targetLocationCard = document.querySelector(`.location-card[data-id="${selectedLocationId}"]`);
                if (targetLocationCard) {
                    // Получаем координаты выбранной локации
                    const selectedLat = parseFloat(targetLocationCard.dataset.lat);
                    const selectedLng = parseFloat(targetLocationCard.dataset.lng);
                    
                    // Плавно перемещаем карту к выбранной локации
                    setTimeout(() => {
                        map.flyTo([selectedLat, selectedLng], 16, {
                            animate: true,
                            duration: 1.0
                        });
                        
                        // Открываем popup для выбранной локации
                        const selectedMarker = markers.find(m => m.locationId === selectedLocationId);
                        if (selectedMarker) {
                            setTimeout(() => {
                                selectedMarker.openPopup();
                            }, 1200);
                        }
                    }, 300);
                    
                    // Выделяем карточку
                    targetLocationCard.classList.add('ring-2', 'ring-primary', 'border-primary');
                    currentHighlightedCard = targetLocationCard;
                    
                    // Прокручиваем к карточке
                    setTimeout(() => {
                        targetLocationCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 500);
                }
            }
            
            // Если нет выбранной локации, выделяем первую
            if (!targetLocationCard) {
                const firstLocation = document.querySelector('.location-card');
                if (firstLocation) {
                    firstLocation.classList.add('ring-2', 'ring-primary', 'border-primary');
                    currentHighlightedCard = firstLocation;
                }
            }
        });

        // Обновление карты при наведении на локацию
        window.updateMap = function(lat, lng, street) {
            // Очищаем предыдущий таймаут
            if (hoverTimeout) {
                clearTimeout(hoverTimeout);
            }

            // Добавляем небольшую задержку для плавности
            hoverTimeout = setTimeout(() => {
                if (!map) {
                    console.warn('Карта еще не инициализирована');
                    return;
                }

                const position = [parseFloat(lat), parseFloat(lng)];

                // Плавное перемещение карты к новой позиции с зумом
                map.flyTo(position, 16, {
                    animate: true,
                    duration: 1.0
                });

                // Добавляем визуальную обратную связь
                const hoveredCard = event.currentTarget;
                
                // Убираем выделение с предыдущей карточки
                if (currentHighlightedCard && currentHighlightedCard !== hoveredCard) {
                    currentHighlightedCard.classList.remove('ring-2', 'ring-primary', 'border-primary');
                }
                
                // Выделяем текущую карточку
                hoveredCard.classList.add('ring-2', 'ring-primary', 'border-primary');
                currentHighlightedCard = hoveredCard;
            }, 100);
        };
    </script>

    <style>
        .location-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .location-card:hover {
            transform: translateY(-2px);
        }

        .location-card.ring-2 {
            border-color: #7b70c2;
        }

        .location-card.border-primary {
            border-color: #7b70c2 !important;
        }
    </style>
@endsection