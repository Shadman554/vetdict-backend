<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Check database connection
try {
    DB::connection()->getPdo();
    echo "Connected to database successfully.\n";
    
    // Get all tables
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    
    echo "\nTables in database:\n";
    foreach ($tables as $table) {
        echo "- {$table->name}\n";
        
        // Get row count for each table
        $count = DB::table($table->name)->count();
        echo "  Rows: $count\n";
        
        // Show columns for words table
        if ($table->name === 'words') {
            $columns = DB::select("PRAGMA table_info(words)");
            echo "  Columns: " . implode(', ', array_column($columns, 'name')) . "\n";
        }
    }
    
} catch (\Exception $e) {
    die("Could not connect to the database. Error: " . $e->getMessage() . "\n");
}
