<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ListingController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    // مسارات تعديل وحذف وإلغاء حجز الإعلانات
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::post('/listings/{listing}/cancel', [ListingController::class, 'cancel'])->name('listings.cancel');

    Route::post('/listings/{listing}/reserve', [ListingController::class, 'reserve'])->name('listings.reserve');

    // مسارات المندوب والتوصيل اللوجستي
    Route::post('/listings/{listing}/accept-delivery', [ListingController::class, 'acceptDelivery'])->name('listings.accept-delivery');
    Route::post('/listings/{listing}/pickup-delivery', [ListingController::class, 'pickupDelivery'])->name('listings.pickup-delivery');
    Route::post('/listings/{listing}/complete-delivery', [ListingController::class, 'completeDelivery'])->name('listings.complete-delivery');
});

require __DIR__ . '/auth.php';
