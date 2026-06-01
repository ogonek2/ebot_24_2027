<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'href', 'discount_percent', 'discount_active', 'category_type', 'category_img', 'sort_order', 'parent_id',
        'meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description', 'og_image', 'robots', 'canonical_path',
    ];

    protected $casts = [
        'discount_active' => 'boolean',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function blogPosts()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_category');
    }

    /**
     * Родительская категория
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Вложенные категории
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order', 'asc');
    }

    /**
     * Получить все услуги категории и всех её дочерних категорий
     */
    public function getAllServices()
    {
        // Загружаем услуги текущей категории, если они еще не загружены
        if (!$this->relationLoaded('services')) {
            $this->load('services');
        }
        
        $services = $this->services;
        
        // Загружаем дочерние категории, если они еще не загружены
        if (!$this->relationLoaded('children')) {
            $this->load('children.services');
        }
        
        foreach ($this->children as $child) {
            $services = $services->merge($child->getAllServices());
        }
        
        return $services->unique('id');
    }

    /**
     * Проверить, является ли категория вложенной
     */
    public function isChild()
    {
        return !is_null($this->parent_id);
    }

    /**
     * Получить все дочерние категории рекурсивно
     */
    public function getAllDescendants()
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        
        return $descendants;
    }

    /**
     * Проверить, активна ли скидка на категорию
     */
    public function hasActiveDiscount()
    {
        return $this->discount_active && $this->discount_percent > 0;
    }

    /**
     * Получить размер скидки в процентах
     */
    public function getDiscountPercent()
    {
        return $this->hasActiveDiscount() ? $this->discount_percent : 0;
    }

    /**
     * Вычислить цену со скидкой
     */
    public function calculateDiscountedPrice($originalPrice)
    {
        if (!$this->hasActiveDiscount()) {
            return $originalPrice;
        }

        $discountAmount = ($originalPrice * $this->discount_percent) / 100;
        return $originalPrice - $discountAmount;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            if (empty($category->href)) {
                $category->href = self::generateHref($category->name);
            }
        });
    }

    // Метод для генерации href (базовый, используется текущей логикой сохранения)
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

    // Новый генератор с более жёсткими правилами транслитерации для ссылок категорий
    // Пример: "Одяг Текстиль" -> "odyag-tekstil"
    public static function generateCategorySlug(string $name): string
    {
        $map = [
            'а' => 'a','б' => 'b','в' => 'v','г' => 'g','ґ' => 'g','д' => 'd','е' => 'e','є' => 'e',
            'ж' => 'zh','з' => 'z','и' => 'i','і' => 'i','ї' => 'yi','й' => 'y','к' => 'k','л' => 'l',
            'м' => 'm','н' => 'n','о' => 'o','п' => 'p','р' => 'r','с' => 's','т' => 't','у' => 'u',
            'ф' => 'f','х' => 'kh','ц' => 'ts','ч' => 'ch','ш' => 'sh','щ' => 'shch','ь' => '',
            'ю' => 'yu','я' => 'ya',
        ];

        $slug = mb_strtolower($name);
        $slug = strtr($slug, $map);
        // Заменяем всё, кроме латиницы/цифр, на тире
        $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug);
        // Схлопываем повторяющиеся тире
        $slug = preg_replace('/-+/', '-', $slug);
        // Убираем тире по краям
        $slug = trim($slug, '-');
        return $slug;
    }
}
