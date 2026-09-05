<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\IndexServices;

/*
|--------------------------------------------------------------------------
| Backend-only web routes (no public Blade UI)
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pages.xml', [SitemapController::class, 'pages'])->name('sitemap.pages');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-services.xml', [SitemapController::class, 'services'])->name('sitemap.services');
Route::get('/sitemap-posts.xml', [SitemapController::class, 'posts'])->name('sitemap.posts');

Route::get('/blog/feed.xml', [BlogController::class, 'feed'])->name('blog.feed');

Route::get('/lokatsii/{seo_link}', [IndexServices::class, 'location'])
    ->where('seo_link', '.*')
    ->name('location_page');

Route::get('/invoice/{orderId}/download', [CartController::class, 'downloadInvoice'])->name('invoice.download');

Route::post('/admin/services/import', [App\Filament\Resources\ServiceResource\Pages\ImportServices::class, 'import'])
    ->name('filament.resources.services.import');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

/*
|--------------------------------------------------------------------------
| SPA fallback — all public pages served by React frontend
|--------------------------------------------------------------------------
*/

Route::get('/{any?}', [SpaController::class, 'index'])
    ->where('any', '^(?!api|sanctum|admin|filament|storage|build|css|js|vendor|invoice|blog/feed).*$')
    ->name('spa');
