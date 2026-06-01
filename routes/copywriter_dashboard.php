<?php

use App\Http\Controllers\Dashboard\BlogDashboardController;
use App\Http\Controllers\Dashboard\BlogSeoSettingsController;
use App\Http\Controllers\Dashboard\CategoryDashboardController;
use App\Http\Controllers\Dashboard\ServiceContentDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'role:admin,copywriter'])
    ->prefix('dashboard/copywriter')
    ->name('copywriter.')
    ->group(function () {
        Route::redirect('/', '/dashboard/copywriter/posts')->name('home');

        Route::get('/posts', [BlogDashboardController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [BlogDashboardController::class, 'create'])->name('posts.create');
        Route::post('/posts', [BlogDashboardController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [BlogDashboardController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{post}', [BlogDashboardController::class, 'update'])->name('posts.update');

        Route::get('/blog-seo', [BlogSeoSettingsController::class, 'edit'])->name('blog-seo.edit');
        Route::put('/blog-seo', [BlogSeoSettingsController::class, 'update'])->name('blog-seo.update');

        Route::get('/categories', [CategoryDashboardController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryDashboardController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryDashboardController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryDashboardController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryDashboardController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryDashboardController::class, 'destroy'])->name('categories.destroy');

        Route::get('/services', [ServiceContentDashboardController::class, 'index'])->name('services.index');
        Route::get('/services/{service}/edit', [ServiceContentDashboardController::class, 'edit'])->name('services.edit');
        Route::put('/services/{service}', [ServiceContentDashboardController::class, 'update'])->name('services.update');
    });

// Старі URL блог-дашборду
Route::middleware(['web', 'auth', 'role:admin,copywriter'])->group(function () {
    Route::redirect('/dashboard/blog', '/dashboard/copywriter/posts');
    Route::redirect('/dashboard/blog/create', '/dashboard/copywriter/posts/create');
    Route::get('/dashboard/blog/{post}/edit', function (\App\Models\BlogPost $post) {
        return redirect()->route('copywriter.posts.edit', $post);
    });
});
