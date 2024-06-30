<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::apiResource('blogs', BlogController::class)->middleware('auth:sanctum')->middleware(['throttle:user']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
