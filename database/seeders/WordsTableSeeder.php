<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WordsTableSeeder extends Seeder
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

        // Define the order of tables to seed
        $tables = [
            'words', 'diseases', 'drugs', 'books', 'normal_ranges', 
            'staff', 'tutorial_videos', 'about_page', 'app_links', 'notifications'
        ];

        foreach ($tables as $tableName) {
            $filePath = "{$path}/{$tableName}.json";
            
            if (!File::exists($filePath)) {
                $this->command->warn("Skipping {$tableName}: JSON file not found");
                continue;
            }

            $this->command->info("Processing {$tableName}...");

            // Skip if table doesn't exist
            if (!Schema::hasTable($tableName)) {
                $this->command->error("Table {$tableName} does not exist!");
                continue;
            }

            try {
                // Read and decode JSON
                $json = File::get($filePath);
                $data = json_decode($json, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \Exception('Invalid JSON: ' . json_last_error_msg());
                }

                if (!is_array($data) || empty($data)) {
                    $this->command->warn("No data to seed for {$tableName} or invalid format");
                    continue;
                }

                // Get table columns and required fields
                $tableColumns = Schema::getColumnListing($tableName);
                $requiredColumns = ['id', 'created_at', 'updated_at'];
                
                // Process data in chunks
                $chunks = array_chunk($data, 50);
                $totalInserted = 0;
                
                foreach ($chunks as $chunkIndex => $chunk) {
                    $records = [];
                    
                    foreach ($chunk as $item) {
                        if (!is_array($item)) {
                            $this->command->warn("Skipping invalid item in {$tableName}");
                            continue;
                        }
                        
                        // Filter out non-existent columns
                        $record = array_intersect_key($item, array_flip($tableColumns));
                        
                        // Ensure required fields
                        $record['id'] = $record['id'] ?? (string) Str::uuid();
                        $record['created_at'] = $record['created_at'] ?? now();
                        $record['updated_at'] = $record['updated_at'] ?? now();
                        
                        $records[] = $record;
                    }
                    
                    if (empty($records)) {
                        continue;
                    }
                    
                    // Try to insert, ignore duplicates
                    try {
                        $inserted = DB::table($tableName)->insertOrIgnore($records);
                        $totalInserted += count($records);
                        $this->command->info("Chunk {$chunkIndex}: Inserted " . count($records) . " records into {$tableName}");
                    } catch (\Exception $e) {
                        $this->command->error("Error inserting into {$tableName}: " . $e->getMessage());
                        continue;
                    }
                }
                
                $this->command->info("Successfully seeded {$totalInserted} records into {$tableName}");
                
            } catch (\Exception $e) {
                $this->command->error("Error processing {$tableName}: " . $e->getMessage());
                $this->command->error("File: " . $e->getFile() . " Line: " . $e->getLine());
                continue;
            }
        }
        
        $this->command->info('All data import completed!');
    }
}
