<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class JsonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Starting to seed data from JSON files...');

        // Path to the data directory
        $path = database_path('data');
        
        if (!File::exists($path)) {
            $this->command->error('Data directory not found!');
            return;
        }

        $files = File::files($path);

        foreach ($files as $file) {
            // Get table name from file name, e.g., 'words.json' -> 'words'
            $tableName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $this->command->info("Processing {$tableName}...");

            // Read the JSON file content
            $json = File::get($file->getPathname());
            $data = json_decode($json, true);

            // Check if data is not null and is an array
            if (is_array($data) && !empty($data)) {
                // Filter out any unwanted keys (like _exportedAt) from each record
                $filteredData = array_map(function($record) {
                    return array_filter($record, function($value, $key) {
                        return $key !== '_exportedAt';
                    }, ARRAY_FILTER_USE_BOTH);
                }, $data);

                // Get all unique column names from the data
                $allColumns = [];
                foreach ($filteredData as $record) {
                    $allColumns = array_merge($allColumns, array_keys($record));
                }
                $allColumns = array_unique($allColumns);

                // Use a transaction for safety
                DB::transaction(function () use ($tableName, $filteredData, $allColumns) {
                    // Ensure the table exists and has all required columns
                    if (!Schema::hasTable($tableName)) {
                        $this->command->error("Table {$tableName} does not exist!");
                        return;
                    }

                    // Get existing columns
                    $existingColumns = Schema::getColumnListing($tableName);
                    
                    // Add any missing columns
                    $newColumns = array_diff($allColumns, $existingColumns);
                    if (!empty($newColumns)) {
                        $this->command->info("Adding missing columns to {$tableName}: " . implode(', ', $newColumns));
                        
                        foreach ($newColumns as $column) {
                            try {
                                // Try to determine the column type based on sample data
                                $sampleValue = null;
                                foreach ($filteredData as $record) {
                                    if (isset($record[$column])) {
                                        $sampleValue = $record[$column];
                                        break;
                                    }
                                }
                                
                                $columnType = 'string';
                                if (is_numeric($sampleValue)) {
                                    $columnType = strpos($sampleValue, '.') !== false ? 'float' : 'integer';
                                } elseif (is_bool($sampleValue)) {
                                    $columnType = 'boolean';
                                }
                                
                                // Add the column with appropriate type
                                $this->command->info("Adding column {$column} as type {$columnType}");
                                
                                Schema::table($tableName, function($table) use ($column, $columnType) {
                                    $columnDef = $table->{$columnType}($column)->nullable();
                                    if ($column === 'id') {
                                        $columnDef->primary();
                                    }
                                });
                                
                            } catch (\Exception $e) {
                                $this->command->error("Failed to add column {$column} to {$tableName}: " . $e->getMessage());
                            }
                        }
                    }

                    // Truncate the table before seeding to avoid duplicates on re-seed
                    DB::table($tableName)->truncate();
                    
                    // Insert data into the table in chunks to avoid memory issues
                    $chunks = array_chunk($filteredData, 100);
                    foreach ($chunks as $chunk) {
                        try {
                            DB::table($tableName)->insert($chunk);
                        } catch (QueryException $e) {
                            $this->command->error("Error inserting into {$tableName}: " . $e->getMessage());
                            // Try to continue with the next chunk
                            continue;
                        }
                    }
                });
                
                $this->command->info("Seeded {$tableName} successfully.");
            } else {
                $this->command->warn("No data to seed for {$tableName} or invalid JSON format.");
            }
        }

        $this->command->info('Finished seeding data.');
    }
}
