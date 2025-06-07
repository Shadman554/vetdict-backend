<?php

$db = new PDO('sqlite:database/database.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    // Check if we can connect
    echo "Connected to SQLite database successfully.\n\n";
    
    // List all tables
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "No tables found in the database.\n";
    } else {
        echo "Tables in database:\n";
        foreach ($tables as $table) {
            echo "- $table\n";
            
            // Get row count for each table
            $count = $db->query("SELECT COUNT(*) FROM \"$table\"")->fetchColumn();
            echo "  Rows: $count\n";
            
            // Show columns for words table
            if ($table === 'words') {
                $columns = $db->query("PRAGMA table_info(words)")->fetchAll(PDO::FETCH_COLUMN, 1);
                echo "  Columns: " . implode(', ', $columns) . "\n";
                
                // Show first few rows
                $stmt = $db->query("SELECT * FROM words LIMIT 3");
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "  Sample data:\n";
                foreach ($rows as $row) {
                    echo "    " . json_encode($row) . "\n";
                }
            }
        }
    }
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
