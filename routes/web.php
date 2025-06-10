<?php

use Illuminate\Support\Facades\Route;

// Simple health check endpoint
Route::get('/', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'VetDict API',
        'time' => now()->toDateTimeString()
    ]);
});



// Auth Routes (if needed)
require __DIR__.'/auth.php';

// Fallback route
Route::fallback(function() {
    return response()->json([
        'error' => 'Not Found',
        'message' => 'The requested resource was not found.',
        'documentation' => 'https://your-docs-url.com'
    ], 404);
});
