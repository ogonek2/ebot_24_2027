<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class discount extends Model
{
    protected $table = 'discounts';
    
    protected $fillable = [
        'name', 'link_name', 'banner', 'color', 'text_color', 'discount_color', 'sort_order', 'locations', 'umowy', 'discount_action', 'discount_percent', 'telegram_name',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'discount_service')
            ->withPivot(['attach_mode', 'custom_percent'])
            ->withTimestamps();
    }

    /**
     * Sync attached services and write sale_* when mode is shared/custom.
     *
     * @param  array<int, array{service_id:int, attach_mode:string, custom_percent?:int|null}>  $rows
     */
    public function syncPromoServices(array $rows): void
    {
        $previousIds = $this->services()->pluck('services.id')->all();
        $sync = [];
        foreach ($rows as $row) {
            $serviceId = (int) ($row['service_id'] ?? 0);
            if ($serviceId <= 0) {
                continue;
            }
            $mode = $row['attach_mode'] ?? 'shared';
            if (!in_array($mode, ['shared', 'custom', 'none'], true)) {
                $mode = 'shared';
            }
            $sync[$serviceId] = [
                'attach_mode' => $mode,
                'custom_percent' => $mode === 'custom' ? ($row['custom_percent'] ?? null) : null,
            ];
        }

        $this->services()->sync($sync);

        $detached = array_diff($previousIds, array_keys($sync));
        if ($detached) {
            Service::query()
                ->whereIn('id', $detached)
                ->where('sale_source_discount_id', $this->id)
                ->update([
                    'sale_price' => null,
                    'individual_sale_price' => null,
                    'sale_source_discount_id' => null,
                ]);
        }

        $sharedPercent = (int) ($this->discount_percent ?? 0);

        foreach ($sync as $serviceId => $pivot) {
            $service = Service::find($serviceId);
            if (!$service) {
                continue;
            }

            $mode = $pivot['attach_mode'];
            if ($mode === 'none') {
                if ((int) $service->sale_source_discount_id === (int) $this->id) {
                    $service->sale_price = null;
                    $service->individual_sale_price = null;
                    $service->sale_source_discount_id = null;
                    $service->save();
                }
                continue;
            }

            $percent = $mode === 'custom'
                ? (int) ($pivot['custom_percent'] ?? 0)
                : $sharedPercent;

            if ($percent <= 0) {
                continue;
            }

            $baseStream = floatval($service->price ?? 0);
            $baseIndividual = floatval($service->individual_price ?? 0);

            if ($baseStream > 0) {
                $service->sale_price = Service::applyPercentToPrice($baseStream, $percent);
            }
            if ($baseIndividual > 0) {
                $service->individual_sale_price = Service::applyPercentToPrice($baseIndividual, $percent);
            }
            $service->sale_source_discount_id = $this->id;
            $service->save();
        }
    }
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($discount) {
            // Генерируем link_name если не указан
            if (empty($discount->link_name) && !empty($discount->name)) {
                $discount->link_name = self::generateLinkName($discount->name);
            }
            
            // Преобразуем пустую строку banner в null
            if ($discount->banner === '') {
                $discount->banner = null;
            }
        });
    }

    /**
     * Получить полный URL для баннера
     */
    public function getBannerUrlAttribute()
    {
        if (!$this->banner) {
            return null;
        }
        return asset('storage/' . $this->banner);
    }

    /**
     * Генерация URL-friendly имени из текста
     */
    public static function generateLinkName($text)
    {
        // Транслитерация кириллицы в латиницу
        $transliteration = [
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
            'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
            'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
            'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
            'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
            'Е' => 'E', 'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
            'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
            'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
            'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        ];

        $text = mb_strtolower($text, 'UTF-8');
        $text = strtr($text, $transliteration);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        $text = trim($text, '-');

        return $text;
    }
}