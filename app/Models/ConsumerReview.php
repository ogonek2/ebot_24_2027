<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumerReview extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'order_number',
        'type',
        'message',
        'status',
        'admin_notes',
    ];

    public const TYPES = [
        'review' => 'Відгук',
        'claim' => 'Претензія',
        'suggestion' => 'Пропозиція',
    ];

    public const STATUSES = [
        'new' => 'Нове',
        'in_progress' => 'В роботі',
        'done' => 'Опрацьовано',
        'archived' => 'Архів',
    ];

    public static function getTypes(): array
    {
        return self::TYPES;
    }

    public static function getStatuses(): array
    {
        return self::STATUSES;
    }
}
