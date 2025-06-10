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

// Public routes that don't require authentication
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
    
    // Protected resource routes with all methods except index and show
    Route::post('words', [\App\Http\Controllers\API\WordController::class, 'store'])->name('words.store');
    Route::put('words/{word}', [\App\Http\Controllers\API\WordController::class, 'update'])->name('words.update');
    Route::delete('words/{word}', [\App\Http\Controllers\API\WordController::class, 'destroy'])->name('words.destroy');
    
    // Repeat for other resources
    Route::post('diseases', [\App\Http\Controllers\API\DiseaseController::class, 'store'])->name('diseases.store');
    Route::put('diseases/{disease}', [\App\Http\Controllers\API\DiseaseController::class, 'update'])->name('diseases.update');
    Route::delete('diseases/{disease}', [\App\Http\Controllers\API\DiseaseController::class, 'destroy'])->name('diseases.destroy');
    
    Route::post('drugs', [\App\Http\Controllers\API\DrugController::class, 'store'])->name('drugs.store');
    Route::put('drugs/{drug}', [\App\Http\Controllers\API\DrugController::class, 'update'])->name('drugs.update');
    Route::delete('drugs/{drug}', [\App\Http\Controllers\API\DrugController::class, 'destroy'])->name('drugs.destroy');
    
    Route::post('books', [\App\Http\Controllers\API\BookController::class, 'store'])->name('books.store');
    Route::put('books/{book}', [\App\Http\Controllers\API\BookController::class, 'update'])->name('books.update');
    Route::delete('books/{book}', [\App\Http\Controllers\API\BookController::class, 'destroy'])->name('books.destroy');
    
    Route::post('normal-ranges', [\App\Http\Controllers\API\NormalRangeController::class, 'store'])->name('normal-ranges.store');
    Route::put('normal-ranges/{normal_range}', [\App\Http\Controllers\API\NormalRangeController::class, 'update'])->name('normal-ranges.update');
    Route::delete('normal-ranges/{normal_range}', [\App\Http\Controllers\API\NormalRangeController::class, 'destroy'])->name('normal-ranges.destroy');
    
    Route::post('staff', [\App\Http\Controllers\API\StaffController::class, 'store'])->name('staff.store');
    Route::put('staff/{staff}', [\App\Http\Controllers\API\StaffController::class, 'update'])->name('staff.update');
    Route::delete('staff/{staff}', [\App\Http\Controllers\API\StaffController::class, 'destroy'])->name('staff.destroy');
    
    Route::post('tutorial-videos', [\App\Http\Controllers\API\TutorialVideoController::class, 'store'])->name('tutorial-videos.store');
    Route::put('tutorial-videos/{tutorial_video}', [\App\Http\Controllers\API\TutorialVideoController::class, 'update'])->name('tutorial-videos.update');
    Route::delete('tutorial-videos/{tutorial_video}', [\App\Http\Controllers\API\TutorialVideoController::class, 'destroy'])->name('tutorial-videos.destroy');
});
