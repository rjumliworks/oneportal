<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->middleware('guest')->name('welcome');

Route::middleware(['2fa','auth','verified','is_active'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [App\Http\Controllers\DashboardController::class, 'search']);
    Route::get('/keep-alive', function () { return response()->json(['status' => 'ok']);});

    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('/users', App\Http\Controllers\System\UserController::class);
        Route::resource('/references', App\Http\Controllers\System\ReferenceController::class);
    });
    Route::middleware(['role:Human Resource Officer'])->group(function () {
        Route::resource('/employees', App\Http\Controllers\Hr\EmployeeController::class);
    });
});

require __DIR__.'/auth.php';
