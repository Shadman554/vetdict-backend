<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

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
                // Use a transaction for safety
                DB::transaction(function () use ($tableName, $data) {
                    // Truncate the table before seeding to avoid duplicates on re-seed
                    DB::table($tableName)->truncate();
                    // Insert data into the table
                    DB::table($tableName)->insert($data);
                });
                $this->command->info("Seeded {$tableName} successfully.");
            } else {
                $this->command->warn("No data to seed for {$tableName} or invalid JSON format.");
            }
        }

        $this->command->info('Finished seeding data.');
    }
}
