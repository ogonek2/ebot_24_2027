<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Service;
use App\Models\B2b;
use App\Models\discount;
use App\Models\locations;
use App\Models\cities;
use App\Models\BlogPost;
use App\Services\SpaBootstrap;

class IndexServices extends Controller
{
    public function index()
    {
        // Загружаем только родительские категории (без вложенных) с услугами и вложенными категориями
        // Загружаем все услуги, включая с ценой 0 (цена будет скрыта в view, если она 0)
        $categories = Category::with([
            'services' => function ($query) {
                $query->orderBy('name');
            },
            'children.services' => function ($query) {
                $query->orderBy('name');
            }
        ])
        ->whereNull('parent_id') // Только родительские категории
        ->where(function($query) {
            // Категория должна иметь услуги сама или через дочерние категории
            $query->whereHas('services')
                  ->orWhereHas('children.services');
        })
        // Сортировка: сначала по порядку сортировки (sort_order), затем по имени
        ->orderBy('sort_order', 'asc')
        ->orderBy('name')
        ->get();

        $discounts = discount::orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get();

        // Получаем все локации с их городами для слайдера отделений
        $branches = locations::with('cityRelation')
            ->where('sort_order', '>=', 1)
            ->orderBy('sort_order', 'asc')
            ->orderBy('city')
            ->orderBy('street')
            ->get()
            ->map(function($location) {
                return [
                    'id' => $location->id,
                    'city' => $location->cityRelation->city ?? 'Київ',
                    'address' => $location->street,
                    'workingHours' => $location->workinghourse ?? '10:00-20:00 Без Вихідних',
                    'image' => $location->banner ? asset('storage/' . $location->banner) : null,
                    'images' => $location->banner ? [asset('storage/' . $location->banner)] : [],
                    'linkMap' => $location->link_map ?? null,
                    'lat' => $location->lat ?? null,
                    'lng' => $location->lng ?? null,
                ];
            })
            ->toArray();

        $latestBlogPosts = BlogPost::query()
            ->published()
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return view('welcome', [
            'spa' => true,
            'spaBootstrap' => SpaBootstrap::forHome(),
            'categories' => $categories,
            'discounts' => $discounts,
            'branches' => $branches,
            'latestBlogPosts' => $latestBlogPosts,
        ]);
    }
    public function services()
    {
        // Загружаем только родительские категории (без вложенных) с услугами и вложенными категориями
        // Загружаем все услуги, включая с ценой 0 (цена будет скрыта в view, если она 0)
        $categories = Category::with([
            'services' => function ($query) {
                $query->orderBy('name');
            },
            'children.services' => function ($query) {
                $query->orderBy('name');
            }
        ])
        ->whereNull('parent_id') // Только родительские категории
        ->where(function($query) {
            // Категория должна иметь услуги сама или через дочерние категории
            $query->whereHas('services')
                  ->orWhereHas('children.services');
        })
        // Сортировка: сначала по порядку сортировки (sort_order), затем по имени
        ->orderBy('sort_order', 'asc')
        ->orderBy('name')
        ->get();

        return view('poslugi', [
            'spa' => true,
            'spaBootstrap' => SpaBootstrap::forServices(),
            'categories' => $categories,
        ]);
    }
    public function b2b()
    {
        $getB2bItems = B2b::all();

        return view('for-business', [
            'spa' => true,
            'spaBootstrap' => SpaBootstrap::catalog('/dlya-biznesu'),
            'b2b_data' => $getB2bItems,
        ]);
    }
    public function b2bShow($page)
    {
        $b2b = B2b::where('href', $page)->firstOrFail();

        return view('b2b_show', compact('b2b'));
    }
    public function courier()
    {
        return view('courier', [
            'spa' => true,
            'spaBootstrap' => SpaBootstrap::catalog('/viklikati-kuryera'),
        ]);
    }
    public function delivery()
    {
        return view('delivery');
    }

    public function contacts()
    {
        return view('contacts');
    }

    public function locations(Request $request)
    {
        // Получаем все города с их локациями
        // Сортируем города по ID (от 1 и дальше по возрастанию)
        $cities = cities::with(['locations' => function($query) {
            $query->where('sort_order', '>=', 1)
                  ->orderBy('sort_order', 'asc')
                  ->orderBy('street');
        }])
        ->whereHas('locations', function($query) {
            $query->where('sort_order', '>=', 1);
        })
        ->orderBy('id', 'asc')
        ->get();

        // Получаем выбранную локацию из query параметра (если есть)
        $selectedLocationId = $request->get('location');

        return view('locations', [
            'spa' => true,
            'spaBootstrap' => SpaBootstrap::catalog('/lokatsii', [
                'branches' => SpaBootstrap::serializeBranches(),
                'locationCities' => SpaBootstrap::serializeLocationCities(),
            ]),
            'cities' => $cities,
            'selectedLocationId' => $selectedLocationId,
        ]);
    }

    public function location($seo_link)
    {
        // Декодируем URL-кодированный параметр (на случай, если слеши были закодированы)
        $seo_link = urldecode($seo_link);
        
        // Находим локацию по seo_link (точное совпадение)
        $location = locations::where('seo_link', $seo_link)->first();

        if (!$location) {
            // Если не найдено, пробуем найти без учета регистра или с различными вариантами
            // (например, если в базе хранится с другим форматом)
            $location = locations::whereRaw('LOWER(seo_link) = ?', [strtolower($seo_link)])->first();
        }

        if (!$location) {
            return abort(404);
        }

        // Редиректим на страницу списка локаций с параметром выбранной локации
        return redirect()->route('locations_page', ['location' => $location->id]);
    }
    
    public function promotions()
    {
        // Получаем все акции из таблицы discount
        $discounts = discount::orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->get();

        return view('promotions', [
            'promotions' => $discounts,
        ]);
    }
    
    public function promotion($id)
    {
        // Находим акцию по ID
        $discount = discount::findOrFail($id);
        
        // Получаем другие акции (исключая текущую)
        $otherDiscounts = discount::where('id', '!=', $discount->id)
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return view('promotion', [
            'promotion' => $discount,
            'otherPromotions' => $otherDiscounts,
        ]);
    }
    
    public function service_page($category, $service)
    {
        // Находим категорию по href (может быть родительской или дочерней)
        $categoryModel = Category::where('href', $category)->first();
        
        // Находим услугу по transform_url с учетом категории
        $serviceQuery = Service::where('transform_url', $service)
            ->with(['categories', 'groups']);
        
        // Если категория найдена, фильтруем услуги по этой категории или её дочерним
        if ($categoryModel) {
            // Получаем все ID категорий: сама категория + все её дочерние
            $categoryIds = [$categoryModel->id];
            if ($categoryModel->parent_id === null) {
                // Если это родительская категория, добавляем все дочерние
                $childrenIds = Category::where('parent_id', $categoryModel->id)->pluck('id')->toArray();
                $categoryIds = array_merge($categoryIds, $childrenIds);
            } else {
                // Если это дочерняя категория, также добавляем родительскую для полноты
                if ($categoryModel->parent_id) {
                    $categoryIds[] = $categoryModel->parent_id;
                }
            }
            
            $serviceQuery->whereHas('categories', function($query) use ($categoryIds) {
                $query->whereIn('categories.id', $categoryIds);
            });
        }
        
        // Пытаемся найти услугу
        $serviceModel = $serviceQuery->first();
        
        // Если не найдено с фильтром категории, пробуем найти без фильтра
        // (на случай, если категория указана неправильно, но услуга существует)
        if (!$serviceModel) {
            $serviceModel = Service::where('transform_url', $service)
                ->with(['categories', 'groups'])
                ->firstOrFail();
        }
        
        // Используем переданную категорию как основную, если она найдена
        $primaryCategory = $categoryModel ?? $serviceModel->categories->first();
        
        // Получаем другие услуги из той же категории (исключая текущую)
        $otherServices = collect();
        if ($primaryCategory) {
            $otherServices = Service::whereHas('categories', function($query) use ($primaryCategory) {
                $query->where('categories.id', $primaryCategory->id);
            })
            ->where('id', '!=', $serviceModel->id)
            ->with(['categories'])
            ->orderBy('name')
            ->limit(6)
            ->get();
        }
        
        // Получаем другие категории (для навигации)
        $otherCategories = collect();
        if ($primaryCategory) {
            $otherCategories = Category::whereHas('services')
                ->where('id', '!=', $primaryCategory->id)
                ->with(['services'])
                ->orderBy('sort_order', 'asc')
                ->orderBy('name')
                ->limit(4)
                ->get();
        } else {
            $otherCategories = Category::whereHas('services')
                ->with(['services'])
                ->orderBy('sort_order', 'asc')
                ->orderBy('name')
                ->limit(4)
                ->get();
        }
        
        return view('service', [
            'spa' => true,
            'spaBootstrap' => SpaBootstrap::catalog(
                '/poslugi-ta-cini/' . ($primaryCategory->href ?? $category) . '/posluga/' . ($serviceModel->transform_url ?? $service),
                [
                    'serviceHref' => $serviceModel->transform_url ?? $service,
                    'categoryHref' => $primaryCategory->href ?? $category,
                ]
            ),
            'service' => $serviceModel,
            'primaryCategory' => $primaryCategory,
            'otherServices' => $otherServices,
            'otherCategories' => $otherCategories,
        ]);
    }
    
    public function category_page($category)
    {
        // Находим активную категорию с услугами и вложенными категориями (включая с ценой 0)
        $activeCategory = Category::where('href', $category)
            ->with(['services' => function ($query) {
                $query->orderBy('name');
            }, 'children.services' => function ($query) {
                $query->orderBy('name');
            }])
            ->first();

        if (!$activeCategory) {
            return abort(404);
        }

        // Получаем другие родительские категории (исключая текущую, только родительские)
        $otherCategories = Category::where('id', '!=', $activeCategory->id)
            ->whereNull('parent_id')
            ->whereHas('services')
            ->with(['services' => function ($query) {
                $query->orderBy('name');
            }])
            // Сортировка: сначала по порядку сортировки (sort_order), затем по имени
            ->orderBy('sort_order', 'asc')
            ->orderBy('name')
            ->get();

        return view('category', [
            'spa' => true,
            'spaBootstrap' => SpaBootstrap::catalog('/poslugi-ta-cini/' . $activeCategory->href, [
                'activeCategory' => $activeCategory->href,
            ]),
            'category' => $activeCategory,
            'otherCategories' => $otherCategories,
        ]);
    }

    public function searchServices(Request $request)
    {
        $query = $request->get('q', '');
        $category = $request->get('category', '');
        $group = $request->get('group', '');

        $servicesQuery = Service::with(['categories', 'groups'])
            ->where('price', '>', 0);

        // Фильтр по категории
        if (!empty($category)) {
            $servicesQuery->whereHas('categories', function($q) use ($category) {
                $q->where('href', $category);
            });
        }

        // Фильтр по группе
        if (!empty($group) && $group !== 'all') {
            $servicesQuery->whereHas('groups', function($q) use ($group) {
                $q->where('id', $group);
            });
        }

        $services = $servicesQuery->get();

        // Если есть поисковый запрос, применяем нечеткий поиск
        if (!empty($query)) {
            $services = $this->fuzzySearch($services, $query);
        }

        return response()->json([
            'services' => $services->map(function($service) {
                // Если это результат fuzzySearch, то categories уже в правильном формате
                if (isset($service['categories']) && is_array($service['categories'])) {
                    return [
                        'id' => $service['id'],
                        'name' => $service['name'],
                        'price' => $service['price'],
                        'categories' => $service['categories'],
                        'groups' => $service['groups'] ?? [],
                        'relevance_score' => $service['relevance_score'] ?? 0,
                    ];
                }
                
                // Если это обычная коллекция Eloquent, преобразуем categories
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'categories' => $service->categories->map(function($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'href' => $category->href,
                            'discount_percent' => $category->discount_percent,
                            'discount_active' => $category->discount_active,
                        ];
                    })->toArray(),
                    'groups' => $service->groups->pluck('name')->toArray(),
                    'relevance_score' => 0,
                ];
            }),
            'total' => $services->count(),
            'suggestions' => $this->getSearchSuggestions($query)
        ]);
    }

    private function fuzzySearch($services, $query)
    {
        $query = mb_strtolower(trim($query));
        $results = [];

        foreach ($services as $service) {
            $name = mb_strtolower($service->name);
            $relevanceScore = 0;

            // 1. Точное совпадение (наивысший приоритет)
            if ($name === $query) {
                $relevanceScore = 100;
            }
            // 2. Начинается с запроса
            elseif (mb_strpos($name, $query) === 0) {
                $relevanceScore = 95;
            }
            // 3. Содержит запрос
            elseif (mb_strpos($name, $query) !== false) {
                $relevanceScore = 85;
            }
            // 4. Агрессивный поиск по подстрокам
            else {
                $relevanceScore = $this->aggressiveSearch($query, $name);
            }

            // 5. Поиск по словам (разбиваем на слова)
            if ($relevanceScore < 80) {
                $wordScore = $this->wordBasedSearch($query, $name);
                $relevanceScore = max($relevanceScore, $wordScore);
            }

            // 6. Фонетический поиск для украинского языка
            if ($relevanceScore < 70) {
                $phoneticScore = $this->phoneticSearch($query, $name);
                $relevanceScore = max($relevanceScore, $phoneticScore);
            }

            // 7. Поиск по транслитерации
            if ($relevanceScore < 60) {
                $transliterationScore = $this->transliterationSearch($query, $name);
                $relevanceScore = max($relevanceScore, $transliterationScore);
            }

            // 8. Поиск по частичным совпадениям букв
            if ($relevanceScore < 50) {
                $partialScore = $this->partialCharacterSearch($query, $name);
                $relevanceScore = max($relevanceScore, $partialScore);
            }

            // 9. Дополнительные бонусы за совпадения в категориях и группах
            foreach ($service->categories as $category) {
                $categoryName = mb_strtolower($category->name);
                $categoryScore = $this->aggressiveSearch($query, $categoryName);
                if ($categoryScore > 0) {
                    $relevanceScore += min($categoryScore * 0.3, 15);
                }
            }

            foreach ($service->groups as $group) {
                $groupName = mb_strtolower($group->name);
                $groupScore = $this->aggressiveSearch($query, $groupName);
                if ($groupScore > 0) {
                    $relevanceScore += min($groupScore * 0.2, 10);
                }
            }

            if ($relevanceScore > 20) { // Снижаем минимальный порог
                $results[] = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'categories' => $service->categories->map(function($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'href' => $category->href,
                            'discount_percent' => $category->discount_percent,
                            'discount_active' => $category->discount_active,
                        ];
                    })->toArray(),
                    'groups' => $service->groups->pluck('name')->toArray(),
                    'relevance_score' => $relevanceScore
                ];
            }
        }

        // Сортируем по релевантности
        usort($results, function($a, $b) {
            return $b['relevance_score'] <=> $a['relevance_score'];
        });

        return collect($results);
    }

    private function aggressiveSearch($query, $text)
    {
        $score = 0;
        
        // 1. Нечеткое совпадение (расстояние Левенштейна)
        $distance = $this->levenshteinDistance($query, $text);
        $maxLength = max(mb_strlen($query), mb_strlen($text));
        $similarity = (1 - $distance / $maxLength) * 100;
        
        if ($similarity > 40) { // Снижаем порог
            $score = max($score, $similarity);
        }

        // 2. Поиск по подстрокам (скользящее окно)
        $queryLength = mb_strlen($query);
        $textLength = mb_strlen($text);
        
        if ($queryLength <= $textLength) {
            for ($i = 0; $i <= $textLength - $queryLength; $i++) {
                $substring = mb_substr($text, $i, $queryLength);
                $subDistance = $this->levenshteinDistance($query, $substring);
                $subSimilarity = (1 - $subDistance / $queryLength) * 100;
                
                if ($subSimilarity > 50) {
                    $score = max($score, $subSimilarity * 0.8);
                }
            }
        }

        // 3. Поиск по начальным буквам слов
        $queryWords = preg_split('/\s+/', $query);
        $textWords = preg_split('/\s+/', $text);
        
        $wordMatches = 0;
        foreach ($queryWords as $queryWord) {
            foreach ($textWords as $textWord) {
                if (mb_strpos($textWord, $queryWord) === 0) {
                    $wordMatches++;
                    break;
                }
            }
        }
        
        if ($wordMatches > 0) {
            $wordScore = ($wordMatches / count($queryWords)) * 70;
            $score = max($score, $wordScore);
        }

        return $score;
    }

    private function wordBasedSearch($query, $text)
    {
        $queryWords = preg_split('/\s+/', $query);
        $textWords = preg_split('/\s+/', $text);
        
        $totalScore = 0;
        $matchedWords = 0;
        
        foreach ($queryWords as $queryWord) {
            $bestMatch = 0;
            foreach ($textWords as $textWord) {
                $distance = $this->levenshteinDistance($queryWord, $textWord);
                $maxLength = max(mb_strlen($queryWord), mb_strlen($textWord));
                $similarity = (1 - $distance / $maxLength) * 100;
                
                if ($similarity > 60) {
                    $bestMatch = max($bestMatch, $similarity);
                }
            }
            
            if ($bestMatch > 0) {
                $totalScore += $bestMatch;
                $matchedWords++;
            }
        }
        
        if ($matchedWords > 0) {
            return ($totalScore / count($queryWords)) * 0.9;
        }
        
        return 0;
    }

    private function phoneticSearch($query, $text)
    {
        // Фонетические правила для украинского языка
        $phoneticRules = [
            'а' => ['о', 'я', 'е'],
            'о' => ['а', 'у', 'е'],
            'е' => ['и', 'є', 'а', 'о'],
            'и' => ['і', 'е', 'ї'],
            'і' => ['и', 'ї', 'е'],
            'ї' => ['і', 'и', 'е'],
            'у' => ['ю', 'о', 'а'],
            'ю' => ['у', 'і', 'и'],
            'я' => ['а', 'є', 'е'],
            'є' => ['е', 'я', 'а'],
            'ч' => ['ц', 'ш', 'щ'],
            'ц' => ['ч', 'с', 'ш'],
            'ш' => ['щ', 'ч', 'с'],
            'щ' => ['ш', 'ч', 'ц'],
            'г' => ['х', 'к'],
            'х' => ['г', 'к'],
            'к' => ['г', 'х', 'ц'],
            'в' => ['ф', 'б'],
            'ф' => ['в', 'п'],
            'б' => ['п', 'в'],
            'п' => ['б', 'ф'],
            'д' => ['т', 'з'],
            'т' => ['д', 'с'],
            'з' => ['с', 'д'],
            'с' => ['з', 'т', 'ц'],
            'ж' => ['ш', 'з'],
            'р' => ['л'],
            'л' => ['р'],
            'м' => ['н'],
            'н' => ['м']
        ];

        $queryChars = mb_str_split($query);
        $textChars = mb_str_split($text);
        
        $matches = 0;
        $totalChars = max(count($queryChars), count($textChars));
        
        for ($i = 0; $i < min(count($queryChars), count($textChars)); $i++) {
            $queryChar = $queryChars[$i];
            $textChar = $textChars[$i];
            
            if ($queryChar === $textChar) {
                $matches += 2; // Точное совпадение
            } elseif (isset($phoneticRules[$queryChar]) && in_array($textChar, $phoneticRules[$queryChar])) {
                $matches += 1; // Фонетическое совпадение
            } elseif (isset($phoneticRules[$textChar]) && in_array($queryChar, $phoneticRules[$textChar])) {
                $matches += 1; // Обратное фонетическое совпадение
            }
        }
        
        return ($matches / ($totalChars * 2)) * 100;
    }

    private function transliterationSearch($query, $text)
    {
        // Правила транслитерации украинского языка
        $transliteration = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'h', 'ґ' => 'g',
            'д' => 'd', 'е' => 'e', 'є' => 'ye', 'ж' => 'zh', 'з' => 'z',
            'и' => 'y', 'і' => 'i', 'ї' => 'yi', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
            'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f',
            'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch',
            'ь' => '', 'ю' => 'yu', 'я' => 'ya'
        ];

        $queryTranslit = $this->transliterate($query, $transliteration);
        $textTranslit = $this->transliterate($text, $transliteration);
        
        $distance = $this->levenshteinDistance($queryTranslit, $textTranslit);
        $maxLength = max(mb_strlen($queryTranslit), mb_strlen($textTranslit));
        $similarity = (1 - $distance / $maxLength) * 100;
        
        return $similarity > 50 ? $similarity * 0.7 : 0;
    }

    private function transliterate($text, $rules)
    {
        $result = '';
        $chars = mb_str_split($text);
        
        foreach ($chars as $char) {
            $result .= $rules[$char] ?? $char;
        }
        
        return $result;
    }

    private function partialCharacterSearch($query, $text)
    {
        $queryChars = mb_str_split($query);
        $textChars = mb_str_split($text);
        
        $matches = 0;
        $queryLength = count($queryChars);
        
        foreach ($queryChars as $queryChar) {
            foreach ($textChars as $textChar) {
                if ($queryChar === $textChar) {
                    $matches++;
                    break;
                }
            }
        }
        
        $ratio = $matches / $queryLength;
        
        // Бонус за порядок символов
        $orderBonus = 0;
        $queryIndex = 0;
        foreach ($textChars as $textChar) {
            if ($queryIndex < $queryLength && $textChar === $queryChars[$queryIndex]) {
                $queryIndex++;
                $orderBonus += 0.1;
            }
        }
        
        $orderRatio = $queryIndex / $queryLength;
        
        return ($ratio + $orderRatio + $orderBonus) * 30;
    }

    private function levenshteinDistance($str1, $str2)
    {
        $len1 = mb_strlen($str1);
        $len2 = mb_strlen($str2);

        if ($len1 == 0) return $len2;
        if ($len2 == 0) return $len1;

        $matrix = [];
        for ($i = 0; $i <= $len1; $i++) {
            $matrix[$i][0] = $i;
        }
        for ($j = 0; $j <= $len2; $j++) {
            $matrix[0][$j] = $j;
        }

        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = (mb_substr($str1, $i - 1, 1) === mb_substr($str2, $j - 1, 1)) ? 0 : 1;
                $matrix[$i][$j] = min(
                    $matrix[$i - 1][$j] + 1,
                    $matrix[$i][$j - 1] + 1,
                    $matrix[$i - 1][$j - 1] + $cost
                );
            }
        }

        return $matrix[$len1][$len2];
    }

    private function getSearchSuggestions($query)
    {
        if (empty($query) || mb_strlen($query) < 1) { // Снижаем минимальную длину
            return [];
        }

        $services = Service::with(['categories', 'groups'])
            ->where('price', '>', 0)
            ->get();
        $suggestions = [];

        foreach ($services as $service) {
            $name = mb_strtolower($service->name);
            $queryLower = mb_strtolower($query);

            // Используем агрессивный поиск для подсказок
            $serviceScore = $this->aggressiveSearch($queryLower, $name);
            if ($serviceScore > 30) { // Снижаем порог для подсказок
                $suggestions[] = [
                    'text' => $service->name,
                    'type' => 'service',
                    'category' => $service->categories->first()->name ?? 'Послуга',
                    'score' => $serviceScore
                ];
            }

            // Добавляем категории с агрессивным поиском
            foreach ($service->categories as $category) {
                $categoryName = mb_strtolower($category->name);
                $categoryScore = $this->aggressiveSearch($queryLower, $categoryName);
                if ($categoryScore > 30) {
                    $suggestions[] = [
                        'text' => $category->name,
                        'type' => 'category',
                        'category' => 'Категорія',
                        'score' => $categoryScore
                    ];
                }
            }

            // Добавляем группы с агрессивным поиском
            foreach ($service->groups as $group) {
                $groupName = mb_strtolower($group->name);
                $groupScore = $this->aggressiveSearch($queryLower, $groupName);
                if ($groupScore > 30) {
                    $suggestions[] = [
                        'text' => $group->name,
                        'type' => 'group',
                        'category' => 'Група',
                        'score' => $groupScore
                    ];
                }
            }
        }

        // Удаляем дубликаты по тексту, сохраняя лучший результат
        $uniqueSuggestions = [];
        foreach ($suggestions as $suggestion) {
            $text = $suggestion['text'];
            if (!isset($uniqueSuggestions[$text]) || $uniqueSuggestions[$text]['score'] < $suggestion['score']) {
                $uniqueSuggestions[$text] = $suggestion;
            }
        }

        // Сортируем по релевантности
        uasort($uniqueSuggestions, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Возвращаем только текст для совместимости
        return array_slice(array_keys($uniqueSuggestions), 0, 8); // Увеличиваем количество подсказок
    }

    public function getPlaceholderServices()
    {
        $services = Service::with(['categories'])
            ->where('price', '>', 0)
            ->get();
        
        $placeholderServices = $services->map(function($service) {
            return [
                'name' => $service->name,
                'category' => $service->categories->first()->name ?? 'Послуга'
            ];
        })->shuffle()->take(10)->values();

        return response()->json([
            'services' => $placeholderServices
        ]);
    }
}
