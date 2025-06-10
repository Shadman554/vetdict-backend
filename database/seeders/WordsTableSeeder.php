<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

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

        $files = File::files($path);

        foreach ($files as $file) {
            // Get table name from file name, e.g., 'words.json' -> 'words'
            $tableName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $this->command->info("Processing {$tableName}...");

            // Skip if table doesn't exist
            if (!Schema::hasTable($tableName)) {
                $this->command->error("Table {$tableName} does not exist!");
                continue;
            }

            // Read the JSON file content
            $json = File::get($file->getPathname());
            $data = json_decode($json, true);

            // Check if data is not null and is an array
            if (is_array($data) && !empty($data)) {
                // Get table columns
                $tableColumns = Schema::getColumnListing($tableName);
                
                // Filter data to only include columns that exist in the table
                $filteredData = array_map(function($item) use ($tableColumns) {
                    return array_intersect_key($item, array_flip($tableColumns));
                }, $data);

                // Truncate the table before seeding to avoid duplicates on re-seed
                DB::table($tableName)->truncate();
                
                // Insert data in chunks to avoid memory issues
                $chunks = array_chunk($filteredData, 100);
                foreach ($chunks as $chunk) {
                    try {
                        DB::table($tableName)->insert($chunk);
                        $this->command->info("Inserted " . count($chunk) . " records into {$tableName}");
                    } catch (\Exception $e) {
                        $this->command->error("Error inserting into {$tableName}: " . $e->getMessage());
                        continue;
                    }
                }
                
                $this->command->info("Seeded {$tableName} successfully.");
            } else {
                $this->command->warn("No data to seed for {$tableName} or invalid JSON format.");
            }
        }
        
        $this->command->info('All data imported successfully!');
    }
}
