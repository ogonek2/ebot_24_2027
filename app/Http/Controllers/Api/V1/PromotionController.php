<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\discount;
use App\Services\SpaBootstrap;
use Illuminate\Http\JsonResponse;

class PromotionController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => SpaBootstrap::serializeDiscounts(),
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
            'promotion' => SpaBootstrap::serializeDiscountDetail($promotion),
            'otherPromotions' => collect(SpaBootstrap::serializeDiscounts())
                ->where('id', '!=', $promotion->id)
                ->take(3)
                ->values(),
        ]);
    }
}
