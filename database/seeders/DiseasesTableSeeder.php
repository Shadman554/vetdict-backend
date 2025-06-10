<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class DiseasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if table doesn't exist
        if (!Schema::hasTable('diseases')) {
            $this->command->error("Diseases table does not exist!");
            return;
        }

        // Path to your JSON file
        $jsonPath = database_path('data/diseases.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("Diseases JSON file not found at: {$jsonPath}");
            return;
        }

        try {
            $json = File::get($jsonPath);
            $diseases = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON: ' . json_last_error_msg());
            }

            $tableColumns = Schema::getColumnListing('diseases');
            $chunks = array_chunk($diseases, 100);
            $count = 0;

            foreach ($chunks as $chunk) {
                $records = [];
                foreach ($chunk as $disease) {
                    // Only include columns that exist in the database
                    $record = array_intersect_key($disease, array_flip($tableColumns));
                    // Ensure required fields are set
                    $record['id'] = $disease['id'] ?? uniqid();
                    $record['created_at'] = $record['created_at'] ?? now();
                    $record['updated_at'] = $record['updated_at'] ?? now();
                    $records[] = $record;
                }
                
                // Use insertOrIgnore to skip duplicates
                DB::table('diseases')->insertOrIgnore($records);
                $count += count($records);
                $this->command->info("Inserted {$count} diseases");
            }

            $this->command->info("Successfully seeded {$count} diseases");
            
        } catch (\Exception $e) {
            $this->command->error("Error seeding diseases: " . $e->getMessage());
            $this->command->error("File: " . $e->getFile() . " Line: " . $e->getLine());
        }
    }
}
