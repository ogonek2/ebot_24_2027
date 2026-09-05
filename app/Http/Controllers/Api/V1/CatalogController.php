<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Services\SpaBootstrap;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'categories' => SpaBootstrap::serializeCategories(SpaBootstrap::loadCategories()),
            'assets' => SpaBootstrap::assets(),
        ]);
    }

    public function services(): JsonResponse
    {
        $data = SpaBootstrap::serializeAllServicesNav();

        return response()->json([
            'data' => $data,
            'meta' => ['total' => count($data)],
        ]);
    }

    public function category(string $href): JsonResponse
    {
        $categoryModel = Category::where('href', $href)
            ->with([
                'services' => fn ($q) => $q->orderBy('name'),
                'parent',
                'children.services' => fn ($q) => $q->orderBy('name'),
            ])
            ->firstOrFail();

        $parent = $categoryModel->parent;
        $groupParent = $parent ?? $categoryModel;

        $subcategories = Category::query()
            ->where('parent_id', $groupParent->id)
            ->whereHas('services')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->with(['services' => fn ($q) => $q->orderBy('name')])
            ->get()
            ->map(fn (Category $child) => SpaBootstrap::serializeCategoryEntry($child, $groupParent))
            ->values();

        return response()->json([
            'category' => SpaBootstrap::serializeCategoryEntry($categoryModel, $parent),
            'parent' => $parent
                ? SpaBootstrap::serializeCategoryEntry($parent)
                : null,
            'subcategories' => $subcategories,
        ]);
    }

    public function service(string $category, string $service): JsonResponse
    {
        $categoryModel = Category::where('href', $category)->firstOrFail();

        $serviceQuery = Service::where('transform_url', $service)
            ->with(['categories', 'groups']);

        $categoryIds = [$categoryModel->id];
        if ($categoryModel->parent_id === null) {
            $categoryIds = array_merge(
                $categoryIds,
                Category::where('parent_id', $categoryModel->id)->pluck('id')->toArray()
            );
        } elseif ($categoryModel->parent_id) {
            $categoryIds[] = $categoryModel->parent_id;
        }

        $serviceQuery->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds));

        $serviceModel = $serviceQuery->first()
            ?? Service::where('transform_url', $service)->with(['categories', 'groups'])->firstOrFail();

        $primaryCategory = $serviceModel->getPrimaryCategory() ?? $categoryModel;

        $relatedServices = Service::query()
            ->where('id', '!=', $serviceModel->id)
            ->whereHas('categories', fn ($q) => $q->where('categories.id', $primaryCategory->id))
            ->with('categories')
            ->orderBy('name')
            ->limit(8)
            ->get()
            ->map(function (Service $related) use ($primaryCategory) {
                $cat = $related->getPrimaryCategory() ?? $primaryCategory;
                $serialized = SpaBootstrap::serializeService($related, $cat);

                return [
                    'id' => $related->id,
                    'name' => $related->name,
                    'href' => $related->transform_url ?? $related->href,
                    'categoryHref' => $cat->href,
                    'price' => $serialized['price'],
                ];
            })
            ->values();

        return response()->json([
            'service' => SpaBootstrap::serializeServiceDetail($serviceModel, $primaryCategory),
            'relatedServices' => $relatedServices,
        ]);
    }
}
