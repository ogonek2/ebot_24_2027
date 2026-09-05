<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\B2b;
use Illuminate\Http\JsonResponse;

class B2bController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => B2b::all()->map(fn ($item) => $this->serialize($item))->values(),
        ]);
    }

    public function show(string $page): JsonResponse
    {
        $b2b = B2b::where('href', $page)->firstOrFail();

        return response()->json([
            'item' => $this->serialize($b2b, true),
        ]);
    }

    private function serialize(B2b $item, bool $detailed = false): array
    {
        $data = [
            'id' => $item->id,
            'name' => $item->name,
            'title' => $item->title,
            'href' => $item->href,
            'banner' => $item->banner && $item->banner !== 'Empty'
                ? url('/storage/' . ltrim($item->banner, '/'))
                : null,
            'url' => '/dlya-biznesu/' . $item->href,
        ];

        if ($detailed) {
            $data['description'] = $item->description ?? null;
        }

        return $data;
    }
}
