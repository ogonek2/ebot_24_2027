<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SpaBootstrap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BootstrapController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $route = $request->query('route', '/');

        return response()->json(SpaBootstrap::resolveForRoute($route));
    }
}
