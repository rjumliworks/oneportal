<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->middleware('guest')->name('welcome');
Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index']);
Route::post('/attendance', [App\Http\Controllers\AttendanceController::class, 'store']);

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
        Route::resource('/dtrs', App\Http\Controllers\Hr\DtrController::class);
        Route::resource('/credits', App\Http\Controllers\Hr\CreditController::class);
        Route::resource('/calendar', App\Http\Controllers\Hr\CalendarController::class);
    });
    Route::resource('/surveys', App\Http\Controllers\Hr\SurveyController::class);
});

require __DIR__.'/auth.php';
