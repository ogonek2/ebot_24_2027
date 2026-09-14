<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairSection extends Model
{
    protected $fillable = [
        'repair_price_list_id',
        'title',
        'sort_order',
    ];

    public function priceList(): BelongsTo
    {
        return $this->belongsTo(RepairPriceList::class, 'repair_price_list_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RepairItem::class)->orderBy('sort_order')->orderBy('id');
    }
}
