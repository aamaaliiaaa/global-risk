<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortController;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\CompareController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Countries List and Show
    Route::get('/countries', [CountryController::class, 'index'])->name('countries.index');
    Route::get('/countries/{country}', [CountryController::class, 'show'])->name('countries.show');

    // Weather
    Route::get('/weather', [WeatherController::class, 'index'])->name('weather.index');

    // Currency
    Route::get('/currency', [CurrencyController::class, 'index'])->name('currency.index');

    // News
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');

    // Ports
    Route::get('/ports', [PortController::class, 'index'])->name('ports.index');
    Route::get('/ports/{port}', [PortController::class, 'show'])->name('ports.show');

    // Compare
    Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');

    // Watchlist
    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
    Route::delete('/watchlist/{id}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

    // Admin
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::post('/admin/ports', [AdminController::class, 'storePort'])->name('admin.ports.store');
    Route::delete('/admin/ports/{port}', [AdminController::class, 'destroyPort'])->name('admin.ports.destroy');
    Route::post('/admin/words', [AdminController::class, 'storeWord'])->name('admin.words.store');
    Route::delete('/admin/words/{type}/{id}', [AdminController::class, 'destroyWord'])->name('admin.words.destroy');
    Route::post('/admin/articles', [AdminController::class, 'storeArticle'])->name('admin.articles.store');
    Route::delete('/admin/articles/{article}', [AdminController::class, 'destroyArticle'])->name('admin.articles.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
