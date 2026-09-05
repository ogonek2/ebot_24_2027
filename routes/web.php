<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CartController;

/*
|--------------------------------------------------------------------------
| API host web routes — admin only (no public storefront)
|--------------------------------------------------------------------------
| Document root of enot-api.* serves JSON API + Filament + dashboards.
| Public SPA lives on FRONTEND_URL / SEO_SITE_URL.
*/

// Filament admin panel is at /admin (config/filament.php)

Route::redirect('/', '/admin')->name('welcome');
Route::redirect('/home', '/admin')->name('home');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-services.xml', [SitemapController::class, 'services'])->name('sitemap.services');
Route::get('/sitemap-posts.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');

Route::get('/blog/feed.xml', [BlogController::class, 'feed'])->name('blog.feed');

Route::get('/invoice/{orderId}/download', [CartController::class, 'downloadInvoice'])->name('invoice.download');

/*
| SPA CSRF: plain token for X-CSRF-TOKEN header (web session, no api+web double stack).
| Call after GET /sanctum/csrf-cookie so the same session cookie is used.
*/
Route::get('/sanctum/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

Route::post('/admin/services/import', [App\Filament\Resources\ServiceResource\Pages\ImportServices::class, 'import'])
    ->name('filament.resources.services.import');

Auth::routes(['register' => false]);

/*
|--------------------------------------------------------------------------
| Anything else → admin (API host is not a public website)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return redirect('/admin');
});
