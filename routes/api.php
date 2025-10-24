<?php

use App\Http\Controllers\Api\V1\ImageGenerationController;
use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])
->group(function() {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('home');

    Route::prefix('v1')->group(function() {

        Route::apiResource('posts', PostController::class);

        Route::post('/generate-prompt', [ImageGenerationController::class, 'generatePrompt'])
            ->middleware('throttle:5,1'); // 5 requests per minute

    });
});

require __DIR__.'/auth.php';
