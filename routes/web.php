<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->middleware('guest')->name('welcome');
Route::get('/verification', [App\Http\Controllers\WelcomeController::class, 'verification']);
Route::post('/verify', [App\Http\Controllers\WelcomeController::class, 'verify']);
Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index']);
Route::post('/attendance', [App\Http\Controllers\AttendanceController::class, 'store']);

Route::middleware(['2fa','auth','verified','is_active'])->group(function () {
    Route::get('/', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [App\Http\Controllers\DashboardController::class, 'search']);
    Route::get('/keep-alive', function () { return response()->json(['status' => 'ok']);});

    Route::middleware(['role:Administrator'])->group(function () {
        Route::resource('/users', App\Http\Controllers\System\UserController::class);
        Route::resource('/references', App\Http\Controllers\System\ReferenceController::class);
        Route::resource('/signatories', App\Http\Controllers\System\SignatoryController::class);
    });
    Route::middleware(['role:Human Resource Officer'])->group(function () {
        Route::resource('/humanresource', App\Http\Controllers\Hr\DashboardController::class);
        Route::resource('/employees', App\Http\Controllers\Hr\EmployeeController::class);
        Route::resource('/dtrs', App\Http\Controllers\Hr\DtrController::class);
        Route::resource('/credits', App\Http\Controllers\Hr\CreditController::class);
        Route::resource('/calendar', App\Http\Controllers\Hr\CalendarController::class);
        Route::resource('/payroll', App\Http\Controllers\Hr\PayrollController::class);
        Route::get('/payroll/{type}/{code}', [App\Http\Controllers\Hr\PayrollController::class, 'view']);
    });
    Route::resource('/surveys', App\Http\Controllers\Hr\SurveyController::class);
    Route::resource('/requests', App\Http\Controllers\Portal\RequestController::class);
    Route::resource('/approvals', App\Http\Controllers\Portal\ApprovalController::class);
    Route::post('/comment', [App\Http\Controllers\Portal\CommentController::class, 'store']);

    Route::prefix('faims')->group(function () {
        Route::resource('/procurement-codes', App\Http\Controllers\FAIMS\Procurement\ProcurementCodeController::class);
        Route::resource('/procurements', App\Http\Controllers\FAIMS\Procurement\ProcurementController::class)->names([
            'index' => 'procurement.index',
        ]);
        Route::get('/procurements/create', [App\Http\Controllers\FAIMS\Procurement\ProcurementController::class, 'create_index']);
        Route::resource('/quotations', App\Http\Controllers\FAIMS\Procurement\QuotationController::class);
        Route::resource('/offers', App\Http\Controllers\FAIMS\Procurement\OfferController::class);
        Route::resource('/bac-resolutions', App\Http\Controllers\FAIMS\Procurement\BACResolutionController::class);
        Route::resource('/notice-of-awards', App\Http\Controllers\FAIMS\Procurement\NOAController::class);
        Route::resource('/purchase-orders', App\Http\Controllers\FAIMS\Procurement\POController::class);
        Route::resource('/suppliers', App\Http\Controllers\FAIMS\Procurement\SupplierController::class);

    });

     Route::prefix('crms')->group(function () {
        Route::resource('/services', App\Http\Controllers\CRMS\ServiceController::class);

    });
});

require __DIR__.'/auth.php';
