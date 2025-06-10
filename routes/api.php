<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

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

// Test database connection and configuration
Route::get('/db-test', function () {
    try {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        
        // Test database connection
        DB::connection()->getPdo();
        
        // Check SQLite database file if using SQLite
        if ($connection === 'sqlite') {
            $database = $config['database'];
            $dbExists = file_exists($database);
            $dbWritable = is_writable($database) || is_writable(dirname($database));
            
            return response()->json([
                'status' => 'SQLite connection successful',
                'database_file' => $database,
                'file_exists' => $dbExists,
                'is_writable' => $dbWritable,
                'app_env' => config('app.env'),
                'db_connection' => $connection,
                'db_config' => $config,
                'storage_writable' => is_writable(storage_path()),
                'bootstrap_cache_writable' => is_writable(base_path('bootstrap/cache')),
            ]);
        }
        
        return response()->json([
            'status' => 'Database connection successful',
            'app_env' => config('app.env'),
            'db_connection' => $connection,
            'db_config' => $config,
        ]);
        
    } catch (\Exception $e) {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        
        return response()->json([
            'error' => $e->getMessage(),
            'app_env' => config('app.env'),
            'db_connection' => $connection,
            'db_config' => $config,
            'storage_writable' => is_writable(storage_path()),
            'bootstrap_cache_writable' => is_writable(base_path('bootstrap/cache')),
            'php_version' => PHP_VERSION,
            'extensions' => get_loaded_extensions(),
        ], 500);
    }
});

// Test database schema and data
Route::get('/db-schema', function () {
    try {
        // Test database connection first
        DB::connection()->getPdo();
        
        $tables = [
            'users', 'diseases', 'words', 'drugs', 'books', 
            'normal_ranges', 'staff', 'tutorial_videos'
        ];
        
        $result = [
            'status' => 'Database schema check',
            'app_env' => config('app.env'),
            'db_connection' => config('database.default'),
            'tables' => []
        ];
        
        foreach ($tables as $table) {
            $exists = Schema::hasTable($table);
            $result['tables'][$table] = [
                'exists' => $exists,
                'columns' => $exists ? Schema::getColumnListing($table) : []
            ];
            
            if ($exists) {
                try {
                    $result['tables'][$table]['count'] = DB::table($table)->count();
                } catch (\Exception $e) {
                    $result['tables'][$table]['count_error'] = $e->getMessage();
                }
            }
        }
        
        return response()->json($result);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
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
