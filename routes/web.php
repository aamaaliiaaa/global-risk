<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard.index');

Route::get('/countries',[CountryController::class,'index'])->name('countries.index');
Route::get('/countries/{country}', [CountryController::class, 'show'])
    ->name('countries.show');
Route::get('/countries/create', [CountryController::class, 'create'])
    ->name('countries.create');
Route::post('/countries', [CountryController::class, 'store'])
    ->name('countries.store');
Route::get('/countries/{country}/edit', [CountryController::class, 'edit'])
    ->name('countries.edit');

Route::put('/countries/{country}', [CountryController::class, 'update'])
    ->name('countries.update');

Route::delete('/countries/{country}', [CountryController::class, 'destroy'])
    ->name('countries.destroy');

Route::get('/weather',[WeatherController::class,'index'])->name('weather.index');

Route::get('/currency',[CurrencyController::class,'index'])->name('currency.index');

Route::get('/news',[NewsController::class,'index'])->name('news.index');

Route::get('/ports',[PortController::class,'index'])->name('ports.index');

Route::get('/compare',[CompareController::class,'index'])->name('compare.index');

Route::get('/watchlist',[WatchlistController::class,'index'])->name('watchlist.index');

Route::get('/admin',[AdminController::class,'index'])->name('admin.index');

require __DIR__.'/auth.php';
