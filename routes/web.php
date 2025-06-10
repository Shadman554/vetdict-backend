<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| API root and documentation routes
|
*/

// API Root
Route::get('/', function () {
    return response()->json([
        'app' => 'VetDict API',
        'version' => '1.0.0',
        'status' => 'online',
        'endpoints' => [
            'api' => url('/api'),
            'documentation' => 'https://your-docs-url.com', // Update with your docs URL
            'test_cors' => url('/api/test-cors'),
            'test_db' => url('/api/test-db')
        ]
    ]);
});

// Serve storage files
Route::get('/storage/{path}', function ($path) {
    $path = storage_path('app/public/' . $path);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
    return response()->file($path);
})->where('path', '.*');

require __DIR__.'/auth.php';
