<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\JoinRequestController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SafetyController;
use Illuminate\Support\Facades\Route;

Route::get('/health', fn () => response()->json([
    'success' => true,
    'message' => 'Barengin API is running.',
    'data' => [
        'app' => config('app.name'),
        'environment' => app()->environment(),
        'time' => now()->toIso8601String(),
    ],
]));

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/activities', [ActivityController::class, 'index']);
Route::get('/activities/{activity}', [ActivityController::class, 'show'])->whereNumber('activity');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/interests', [ProfileController::class, 'updateInterests']);

    Route::get('/my/activities', [ActivityController::class, 'mine']);
    Route::get('/my/join-requests', [JoinRequestController::class, 'mine']);
    Route::delete('/my/join-requests/{joinRequest}', [JoinRequestController::class, 'cancelPending'])->whereNumber('joinRequest');
    Route::get('/my/favorites', [FavoriteController::class, 'index']);

    Route::post('/activities', [ActivityController::class, 'store']);
    Route::patch('/activities/{activity}', [ActivityController::class, 'update'])->whereNumber('activity');
    Route::post('/activities/{activity}/publish', [ActivityController::class, 'publish'])->whereNumber('activity');
    Route::post('/activities/{activity}/cancel', [ActivityController::class, 'cancel'])->whereNumber('activity');
    Route::post('/activities/{activity}/leave', [ActivityController::class, 'leave'])->whereNumber('activity');
    Route::post('/activities/{activity}/start', [ActivityController::class, 'start'])->whereNumber('activity');
    Route::post('/activities/{activity}/complete', [ActivityController::class, 'complete'])->whereNumber('activity');
    Route::get('/activities/{activity}/participants', [ActivityController::class, 'participants'])->whereNumber('activity');
    Route::delete('/activities/{activity}/participants/{userId}', [ActivityController::class, 'removeParticipant'])->whereNumber('activity')->whereNumber('userId');

    Route::post('/activities/{activity}/join-requests', [JoinRequestController::class, 'store'])->whereNumber('activity');
    Route::get('/activities/{activity}/join-requests', [JoinRequestController::class, 'forActivity'])->whereNumber('activity');
    Route::patch('/join-requests/{joinRequest}/decision', [JoinRequestController::class, 'decide'])->whereNumber('joinRequest');

    Route::post('/activities/{activity}/favorite', [FavoriteController::class, 'store'])->whereNumber('activity');
    Route::delete('/activities/{activity}/favorite', [FavoriteController::class, 'destroy'])->whereNumber('activity');

    Route::post('/reports', [SafetyController::class, 'report']);
    Route::get('/my/blocked-users', [SafetyController::class, 'blockedUsers']);
    Route::post('/users/{user}/block', [SafetyController::class, 'block'])->whereNumber('user');
    Route::delete('/users/{user}/block', [SafetyController::class, 'unblock'])->whereNumber('user');
});
