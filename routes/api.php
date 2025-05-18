<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'registerStudent']);
Route::post('/login', [AuthController::class, 'login']);

// Mobile-specific public routes
Route::prefix('mobile')->group(function () {
    Route::post('/register', [AuthController::class, 'mobileRegister']);
    Route::post('/login', [AuthController::class, 'mobileLogin']);
});

// CSRF cookie route
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->noContent();
});

// Protected routes (requires Sanctum authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // Authentication routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Students routes
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::post('/', [StudentController::class, 'store']);
        Route::get('/{id}', [StudentController::class, 'show']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
    });

    // Users management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('/{user}', [UserController::class, 'update']);
        Route::delete('/{user}', [UserController::class, 'destroy']);
    });

    // Medical records routes
    Route::prefix('medical-records')->group(function () {
        Route::get('/', [MedicalRecordController::class, 'index']);
        Route::post('/', [MedicalRecordController::class, 'store']);
        Route::get('/user/{userId}', [MedicalRecordController::class, 'getByUser']);
        Route::get('/{id}', [MedicalRecordController::class, 'show']);
        Route::put('/{medicalRecord}', [MedicalRecordController::class, 'update']);
        Route::delete('/{medicalRecord}', [MedicalRecordController::class, 'destroy']);
    });

    // Role-specific routes
    Route::middleware(['nurse'])->group(function () {
        // Add nurse-specific routes here
    });
});

// Remove the debug route in production
if (config('app.env') !== 'production') {
    Route::post('/auth-check', function (Request $request) {
        \Illuminate\Support\Facades\Log::info('Auth check request received', [
            'headers' => $request->headers->all(),
            'email' => $request->email,
            'password_provided' => !empty($request->password)
        ]);

        $user = \App\Models\User::where('email', strtolower($request->email))->first();

        if (!$user) {
            \Illuminate\Support\Facades\Log::warning('Auth check: User not found', ['email' => $request->email]);
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'email_provided' => $request->email
            ], 404);
        }

        $passwordMatches = \Illuminate\Support\Facades\Hash::check($request->password, $user->password);
        \Illuminate\Support\Facades\Log::info('Auth check password verification', [
            'user_id' => $user->id,
            'password_matches' => $passwordMatches
        ]);

        return response()->json([
            'status' => 'debug',
            'user_exists' => true,
            'password_matches' => $passwordMatches,
            'user_details' => [
                'email' => $user->email,
                'name' => $user->name,
                'role' => $user->role
            ]
        ]);
    });
}