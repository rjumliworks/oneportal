<?php

use Aws\Rekognition\RekognitionClient;
use Illuminate\Support\Facades\Route;

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index'])->middleware('guest')->name('welcome');
Route::get('/verification', [App\Http\Controllers\WelcomeController::class, 'verification']);
Route::post('/verify', [App\Http\Controllers\WelcomeController::class, 'verify']);
Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index']);
Route::post('/attendance', [App\Http\Controllers\AttendanceController::class, 'store']);
Route::post('/improve', [App\Http\Controllers\AiController::class, 'improve']);
// Route::post('/face/register', [App\Http\Controllers\FaceController::class, 'register']);
// Route::post('/face/recognize', [App\Http\Controllers\FaceController::class, 'recognize']);
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

    Route::middleware(['role:Document Management Officer'])->group(function () {
        Route::resource('/events', App\Http\Controllers\Trace\EventController::class);
    });

    Route::resource('/surveys', App\Http\Controllers\Hr\SurveyController::class);
    Route::resource('/requests', App\Http\Controllers\Portal\RequestController::class);
    Route::resource('/approvals', App\Http\Controllers\Portal\ApprovalController::class);
    Route::resource('/dtr', App\Http\Controllers\Portal\DtrController::class);
    Route::post('/comment', [App\Http\Controllers\Portal\CommentController::class, 'store']);
});

Route::post('/recognize', [App\Http\Controllers\AttendanceController::class, 'recognize']);
Route::get('/rekognition-test', function () {
    $rekognition = new RekognitionClient([
        'version' => 'latest',
        'region'      => config('services.rekognition.region'),
            'credentials' => [
                'key'    => config('services.rekognition.key'),
                'secret' => config('services.rekognition.secret'),
            ],
    ]);

    

    // List collections
    try {
     $result = $rekognition->listCollections();
    
    // Convert to array
    $resultArray = $result->toArray();

    return response()->json($resultArray);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::get('/aws-account-check', function () {
    $sts = new \Aws\Sts\StsClient([
        'version' => 'latest',
        'region' => 'ap-southeast-1',
        'credentials' => [
            'key' => config('services.rekognition.key'),
            'secret' => config('services.rekognition.secret'),
        ],
    ]);

    $identity = $sts->getCallerIdentity();
    dd($identity);
});

Route::get('/rekognition-create', function () {
    try {
        $rekognition = new RekognitionClient([
            'version' => 'latest',
            'region' => 'ap-southeast-1',
            'credentials' => [
                'key' => config('services.rekognition.key'),
                'secret' => config('services.rekognition.secret'),
            ],
        ]);

        $result = $rekognition->createCollection([
            'CollectionId' => 'dost9-users',
        ]);

        return response()->json([
            'message' => 'Collection created successfully!',
            'result' => $result,
        ]);
    } catch (\Aws\Exception\AwsException $e) {
        return response()->json([
            'error' => $e->getAwsErrorMessage(),
            'type'  => $e->getAwsErrorCode(),
        ], 500);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

require __DIR__.'/auth.php';
