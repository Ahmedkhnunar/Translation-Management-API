<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\AuthController;

// Auth route
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('translations/export', [TranslationController::class, 'export']);
    Route::apiResource('translations', TranslationController::class);
});