<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ItemController::class, 'index'])->name('dashboard');

    // Inventory Routes
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/add', [ItemController::class, 'addStock'])->name('items.add');
    Route::post('/items/{item}/deduct', [ItemController::class, 'deductStock'])->name('items.deduct');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
});

require __DIR__ . '/auth.php';
