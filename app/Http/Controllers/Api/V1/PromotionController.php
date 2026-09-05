<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\discount;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    public function index(): JsonResponse
    {
        $promotions = discount::orderBy('sort_order')->orderByDesc('created_at')->get();

        return response()->json([
            'data' => $promotions->map(fn ($d) => $this->serialize($d))->values(),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $promotion = discount::findOrFail($id);
        $others = discount::where('id', '!=', $promotion->id)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return response()->json([
            'promotion' => array_merge($this->serialize($promotion), [
                'terms' => $promotion->umowy,
            ]),
            'otherPromotions' => $others->map(fn ($d) => $this->serialize($d))->values(),
        ]);
    }

    private function serialize(discount $d): array
    {
        return [
            'id' => $d->id,
            'name' => $d->name ?? 'Акція',
            'discountAction' => $d->discount_action,
            'locations' => $d->locations,
            'banner' => $d->banner ? asset('storage/' . $d->banner) : null,
            'color' => $d->color,
            'textColor' => $d->text_color,
            'discountColor' => $d->discount_color,
            'url' => '/aktsii/' . $d->id,
        ];
    }
}
