<?php

use App\Http\Controllers\Api\AdController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\SupportController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/self', [AuthController::class, 'self']);
    });
});

Route::prefix('ads')->group(function () {
    Route::get('/', [AdController::class, 'index']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/', [AdController::class, 'store']);
        Route::put('/{ad}', [AdController::class, 'update']);
        Route::delete('/{ad}', [AdController::class, 'delete']);
        Route::post('/{ad}', [AdController::class, 'complete']);
    });

    Route::middleware('auth:api')->prefix('{ad}/responses')->group(function () {
        Route::get('/', [ResponseController::class, 'index']);
        Route::post('/', [ResponseController::class, 'store']);
        Route::delete('/{response}', [ResponseController::class, 'delete']);
        Route::post('/{response}/accept', [ResponseController::class, 'accept']);
        Route::post('/{response}/reject', [ResponseController::class, 'reject']);
    });

    Route::middleware('auth:api')->prefix('{ad}/reviews')->group(function () {
        Route::post('/', [ReviewController::class, 'store']);
        Route::delete('/{review}', [ReviewController::class, 'delete']);
    });
});

Route::middleware('auth:api')->group(function () {
    Route::prefix('profile')->group(function () {
        Route::get('/ads', [ProfileController::class, 'selfAds']);
        Route::get('/responses', [ProfileController::class, 'selfResponses']);
        Route::get('/reviews', [ProfileController::class, 'selfReviews']);
    });

    Route::prefix('supports')->group(function () {
        Route::get('/', [SupportController::class, 'index']);
        Route::post('/', [SupportController::class, 'store']);
    });
});
