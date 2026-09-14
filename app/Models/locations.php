<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class locations extends Model
{
    protected $fillable = [
        'street', 'district', 'city', 'workinghourse', 'link_map', 'lat', 'lng', 'banner', 'value', 'seo_link', 'sort_order',
    ];

    /**
     * Получить город, к которому принадлежит локация
     * 
     * Использование:
     * $location->cityRelation - получить связанную модель города
     * $location->city - получить ID города (поле в таблице)
     */
    public function cityRelation()
    {
        return $this->belongsTo(cities::class, 'city', 'id');
    }
}