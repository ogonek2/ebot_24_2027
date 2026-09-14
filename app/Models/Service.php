<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'article', 'price', 'individual_price', 'sale_price', 'individual_sale_price', 'sale_source_discount_id',
        'category_id', 'marker', 'title', 'value', 'href', 'transform_url',
        'seo_description', 'seo_keywords', 'meta_title', 'og_title', 'og_description', 'og_image', 'robots', 'canonical_path',
        'faq', 'description', 'type_page', 'promotion', 'created_at', 'updated_at', 'sort_order',
    ];

    protected $casts = [
        'faq' => 'array',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'service_group');
    }

    public function discounts()
    {
        return $this->belongsToMany(discount::class, 'discount_service')
            ->withPivot(['attach_mode', 'custom_percent'])
            ->withTimestamps();
    }

    public function saleSourceDiscount()
    {
        return $this->belongsTo(discount::class, 'sale_source_discount_id');
    }

    /** Effective stream unit price (sale if set). */
    public function effectiveStreamPrice(): float
    {
        $base = floatval($this->price ?? 0);
        $sale = floatval($this->sale_price ?? 0);
        if ($sale > 0) {
            return $sale;
        }
        return $base;
    }

    /** Effective individual unit price (sale if set), or 0 if unavailable. */
    public function effectiveIndividualPrice(): float
    {
        $base = floatval($this->individual_price ?? 0);
        if ($base <= 0) {
            return 0;
        }
        $sale = floatval($this->individual_sale_price ?? 0);
        if ($sale > 0) {
            return $sale;
        }
        return $base;
    }

    public static function discountPercentFromPrices(float $base, float $sale): ?int
    {
        if ($base <= 0 || $sale <= 0 || $sale >= $base) {
            return null;
        }
        return (int) round((($base - $sale) / $base) * 100);
    }

    public static function applyPercentToPrice(float $base, int $percent): int
    {
        if ($base <= 0 || $percent <= 0) {
            return (int) round($base);
        }
        $percent = min(100, max(0, $percent));
        return (int) max(0, round($base * (100 - $percent) / 100));
    }

    /**
     * Получить первичную категорию услуги (конкретную категорию, к которой привязана услуга)
     * Приоритет: сначала дочерние категории, потом родительские
     */
    public function getPrimaryCategory()
    {
        // Загружаем категории с их родителями, если еще не загружены
        if (!$this->relationLoaded('categories')) {
            $this->load('categories');
        }
        
        $categories = $this->categories;
        
        if ($categories->isEmpty()) {
            return null;
        }
        
        // Ищем дочернюю категорию (с parent_id) - это приоритет
        $childCategory = $categories->first(function($category) {
            return $category->parent_id !== null;
        });
        
        // Если есть дочерняя категория, возвращаем её, иначе первую категорию
        return $childCategory ?? $categories->first();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($service) {
            // Генерируем transform_url если не указан и есть название
            if (empty($service->transform_url) && !empty($service->name)) {
                $service->transform_url = self::generateHref($service->name);
            }
        });
    }
    
    /**
     * Accessor для href - возвращает transform_url для обратной совместимости
     */
    public function getHrefAttribute()
    {
        return $this->attributes['transform_url'] ?? null;
    }

    // Метод для генерации href
    public static function generateHref($text)
    {
        $text = mb_strtolower($text);

        $text = str_replace(
            ['а','б','в','г','ґ','д','е','є','ж','з','и','і','ї','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ь','ю','я'],
            ['a','b','v','g','g','d','e','ye','zh','z','y','i','yi','y','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch','','yu','ya'],
            $text
        );

        $text = preg_replace('/[^\w\-]+/', '-', $text);
        $text = trim($text, '-');

        return $text;
    }
}
