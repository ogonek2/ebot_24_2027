<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SpaSeo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function __construct(private readonly SpaSeo $spaSeo) {}

    public function show(Request $request): JsonResponse
    {
        $path = $request->query('path', '/');
        $query = $request->only(['page', 'per_page']);

        return response()->json($this->spaSeo->resolve((string) $path, $query));
    }
}
