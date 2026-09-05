<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\BootstrapController;
use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\PromotionController as V1PromotionController;
use App\Http\Controllers\Api\V1\B2bController;
use App\Http\Controllers\Api\V1\LocationsController;
use App\Http\Controllers\Api\V1\SeoController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\ScheduledPopupController;
use App\Http\Controllers\IndexServices;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| Public JSON API (stateless)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('/bootstrap', [BootstrapController::class, 'show']);
    Route::get('/seo', [SeoController::class, 'show']);
    Route::get('/catalog', [CatalogController::class, 'index']);
    Route::get('/categories/{href}', [CatalogController::class, 'category']);
    Route::get('/services', [CatalogController::class, 'services']);
    Route::get('/services/{category}/{service}', [CatalogController::class, 'service']);
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/blog/{slug}', [BlogController::class, 'show']);
    Route::get('/promotions', [V1PromotionController::class, 'index']);
    Route::get('/promotions/{id}', [V1PromotionController::class, 'show'])->whereNumber('id');
    Route::get('/b2b', [B2bController::class, 'index']);
    Route::get('/b2b/{page}', [B2bController::class, 'show']);
    Route::get('/locations', [LocationsController::class, 'index']);
    Route::get('/search-services', [IndexServices::class, 'searchServices']);
    Route::get('/placeholder-services', [IndexServices::class, 'getPlaceholderServices']);
});

Route::get('/modal-promotion', [PromotionController::class, 'getModalPromotion']);
Route::get('/scheduled-popup-modals', [ScheduledPopupController::class, 'index']);
Route::get('/promotions-banner', [PromotionController::class, 'getPromotionsForBanner']);
Route::post('/contact', [App\Http\Controllers\FeedbackController::class, 'submit']);
Route::post('/b2b/proposal', [App\Http\Controllers\FeedbackController::class, 'submitB2bProposal']);
Route::post('/courier/request', [App\Http\Controllers\FeedbackController::class, 'submitCourierOrder']);

/*
|--------------------------------------------------------------------------
| Session API (cart, orders) — requires cookies + CSRF
|--------------------------------------------------------------------------
*/

Route::middleware('web')->group(function () {
    Route::get('/cart', [CartController::class, 'getCart']);
    Route::post('/cart/add', [CartController::class, 'addToCart']);
    Route::put('/cart/{key}', [CartController::class, 'updateCart']);
    Route::delete('/cart/{key}', [CartController::class, 'removeFromCart']);
    Route::post('/cart/clear', [CartController::class, 'clearCart']);
    Route::get('/pickup-locations', [CartController::class, 'getPickupLocations']);
    Route::post('/order/submit', [CartController::class, 'submitOrder']);
    Route::get('/order/last', [CartController::class, 'getLastOrder']);
    Route::post('/order/consultation', [CartController::class, 'submitConsultation']);
});

Route::middleware('auth:sanctum')->get('/user', function (\Illuminate\Http\Request $request) {
    return $request->user();
});
