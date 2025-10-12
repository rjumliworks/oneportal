<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->middleware('guest')->name('welcome');

Route::middleware(['2fa','auth','verified'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/keep-alive', function () { return response()->json(['status' => 'ok']);});


    Route::resource('/users', App\Http\Controllers\System\UserController::class);
    Route::resource('/references', App\Http\Controllers\System\ReferenceController::class);
});

require __DIR__.'/auth.php';
