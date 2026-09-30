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
// /api/lead-log, /api/order/submit, /api/order/last — RouteServiceProvider
// WITHOUT Sanctum session (browser cart + DB reprice; no SameSite cookie noise).

/*
| SPA CSRF: plain token for X-CSRF-TOKEN (after GET /sanctum/csrf-cookie).
| Session comes from Sanctum EnsureFrontendRequestsAreStateful when Origin is stateful.
*/
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

/*
|--------------------------------------------------------------------------
| Legacy session cart (unused by SPA — cart is localStorage now)
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'getCart']);
Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::put('/cart/{key}', [CartController::class, 'updateCart']);
Route::delete('/cart/{key}', [CartController::class, 'removeFromCart']);
Route::post('/cart/clear', [CartController::class, 'clearCart']);
Route::get('/pickup-locations', [CartController::class, 'getPickupLocations']);
Route::post('/order/consultation', [CartController::class, 'submitConsultation']);

Route::middleware('auth:sanctum')->get('/user', function (\Illuminate\Http\Request $request) {
    return $request->user();
});
