<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexServices;
use App\Http\Controllers\CartController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SitemapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexServices::class, 'index'])->name('welcome');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-services.xml', [SitemapController::class, 'services'])->name('sitemap.services');
Route::get('/sitemap-posts.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');
Route::get('/poslugi-ta-cini', [IndexServices::class, 'services'])->name('services');
Route::get('/poslugi-ta-cini/{category}/posluga/{service}', [IndexServices::class, 'service_page'])->name('service_page');
Route::get('/poslugi-ta-cini/{category}', [IndexServices::class, 'category_page'])->name('category_page');
Route::get('/api/search-services', [IndexServices::class, 'searchServices'])->name('search_services');
Route::get('/api/placeholder-services', [IndexServices::class, 'getPlaceholderServices'])->name('placeholder_services');
Route::get('/dlya-biznesu', [IndexServices::class, 'b2b'])->name('b2b_page');
Route::get('/dlya-biznesu/{page}', [IndexServices::class, 'b2bShow'])->name('b2b_page_show');
Route::get('/viklikati-kuryera', [IndexServices::class, 'courier'])->name('courier_page');
Route::get('/dostavka', [IndexServices::class, 'delivery'])->name('delivery_page');
Route::get('/lokatsii', [IndexServices::class, 'locations'])->name('locations_page');
Route::get('/lokatsii/{seo_link}', [IndexServices::class, 'location'])->where('seo_link', '.*')->name('location_page');
Route::get('/aktsii', [IndexServices::class, 'promotions'])->name('promotions');
Route::get('/aktsii/{id}', [IndexServices::class, 'promotion'])->name('promotion_page');
Route::get('/kontakty', [IndexServices::class, 'contacts'])->name('contacts_page');
Route::get('/blog/feed.xml', [BlogController::class, 'feed'])->name('blog.feed');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Cart routes
Route::get('/api/cart', [CartController::class, 'getCart'])->name('cart.get');
Route::post('/api/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::put('/api/cart/{key}', [CartController::class, 'updateCart'])->name('cart.update');
Route::delete('/api/cart/{key}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/api/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');
Route::get('/api/pickup-locations', [CartController::class, 'getPickupLocations'])->name('pickup.locations');

// Checkout routes
Route::get('/oformlennya-zamovlennya', [CartController::class, 'checkout'])->name('checkout');
Route::post('/api/order/submit', [CartController::class, 'submitOrder'])->name('order.submit');
Route::post('/api/order/consultation', [CartController::class, 'submitConsultation'])->name('order.consultation');

// Order success and invoice routes
Route::get('/order-success', [CartController::class, 'orderSuccess'])->name('order.success');
Route::get('/order-success/{orderId}', [CartController::class, 'orderSuccess'])->name('order.success.id');
Route::get('/invoice/{orderId}/download', [CartController::class, 'downloadInvoice'])->name('invoice.download');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/oferta', function() { 
    return view('oferta'); 
})->name('oferta'); 
Route::get('/privacy-policy', function() { 
    return view('privacy_policy'); 
})->name('privacy_policy'); 
Route::get('/umovy', function() {
    return view('umovy');
})->name('umovy');

// Feedback form route
Route::post('/feedback', [App\Http\Controllers\FeedbackController::class, 'submit'])->name('feedback.submit');

// Import services route
Route::post('/admin/services/import', [App\Filament\Resources\ServiceResource\Pages\ImportServices::class, 'import'])->name('filament.resources.services.import');

// Test Telegram notification (remove in production)
Route::get('/test-telegram', function() {
    $controller = new App\Http\Controllers\FeedbackController();
    $reflection = new ReflectionClass($controller);
    $method = $reflection->getMethod('sendTelegramNotification');
    $method->setAccessible(true);
    
    try {
        $result = $method->invoke($controller, 'Test User', '+380123456789', 'Test message from website');
        return response()->json(['success' => true, 'message' => 'Telegram notification sent successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
});

Auth::routes();