<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public test endpoint
Route::get('/test-cors', function () {
    return response()->json([
        'message' => 'CORS is working!',
        'timestamp' => now(),
    ]);
});

// Test database connection and word count
Route::get('/test-db', function () {
    try {
        $count = \App\Models\Word::count();
        $firstWord = \App\Models\Word::first();
        
        return response()->json([
            'success' => true,
            'word_count' => $count,
            'first_word' => $firstWord,
            'database' => config('database.default'),
            'connection_ok' => true,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'database' => config('database.default'),
            'connection_ok' => false,
        ], 500);
    }
});

// Public routes
Route::apiResource('words', \App\Http\Controllers\API\WordController::class)->only(['index', 'show']);
Route::apiResource('diseases', \App\Http\Controllers\API\DiseaseController::class)->only(['index', 'show']);
Route::apiResource('drugs', \App\Http\Controllers\API\DrugController::class)->only(['index', 'show']);
Route::apiResource('books', \App\Http\Controllers\API\BookController::class)->only(['index', 'show']);
Route::apiResource('normal-ranges', \App\Http\Controllers\API\NormalRangeController::class)->only(['index', 'show']);
Route::apiResource('staff', \App\Http\Controllers\API\StaffController::class)->only(['index', 'show']);
Route::apiResource('tutorial-videos', \App\Http\Controllers\API\TutorialVideoController::class)->only(['index', 'show']);

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // User
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // Protected resource routes with all methods
    Route::apiResource('words', \App\Http\Controllers\API\WordController::class)->except(['index', 'show']);
    Route::apiResource('diseases', \App\Http\Controllers\API\DiseaseController::class)->except(['index', 'show']);
    Route::apiResource('drugs', \App\Http\Controllers\API\DrugController::class)->except(['index', 'show']);
    Route::apiResource('books', \App\Http\Controllers\API\BookController::class)->except(['index', 'show']);
    Route::apiResource('normal-ranges', \App\Http\Controllers\API\NormalRangeController::class)->except(['index', 'show']);
    Route::apiResource('staff', \App\Http\Controllers\API\StaffController::class)->except(['index', 'show']);
    Route::apiResource('tutorial-videos', \App\Http\Controllers\API\TutorialVideoController::class)->except(['index', 'show']);
});
