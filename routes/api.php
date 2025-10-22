<?php

use App\Http\Controllers\Api\V1\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])
->group(function() {

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('home');

    Route::prefix('v1')->group(function() {

        Route::apiResource('posts', PostController::class);

    });
});

require __DIR__.'/auth.php';
