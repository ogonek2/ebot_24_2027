<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairItem extends Model
{
    protected $fillable = [
        'repair_section_id',
        'name',
        'price',
        'price_prefix',
        'note',
        'unit',
        'sort_order',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(RepairSection::class, 'repair_section_id');
    }
}
