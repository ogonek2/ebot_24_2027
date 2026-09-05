<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SpaBootstrap;
use Illuminate\Http\JsonResponse;

class LocationsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'cities' => SpaBootstrap::serializeLocationCities(),
            'branches' => SpaBootstrap::serializeBranches(),
        ]);
    }
}
