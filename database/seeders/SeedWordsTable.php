<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SeedWordsTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Seeding words table...');
        
        $file = database_path('data/words.json');
        
        if (!File::exists($file)) {
            $this->command->error("Words JSON file not found!");
            return;
        }
        
        $json = File::get($file);
        $words = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Failed to decode JSON: ' . json_last_error_msg());
            return;
        }
        
        $chunks = array_chunk($words, 50);
        
        foreach ($chunks as $chunk) {
            $insertData = [];
            
            foreach ($chunk as $word) {
                $insertData[] = [
                    'id' => $word['id'] ?? null,
                    'name' => $word['name'] ?? '',
                    'kurdish' => $word['kurdish'] ?? '',
                    'arabic' => $word['arabic'] ?? '',
                    'description' => $word['description'] ?? '',
                    'barcode' => $word['barcode'] ?? null,
                    'is_saved' => $word['isSaved'] ?? false,
                    'is_favorite' => $word['isFavorite'] ?? false,
                    'exported_at' => $word['_exportedAt'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            try {
                DB::table('words')->insertOrIgnore($insertData);
                $this->command->info('Inserted ' . count($chunk) . ' words');
            } catch (\Exception $e) {
                $this->command->error('Error inserting words: ' . $e->getMessage());
                // Try inserting one by one to identify the problematic record
                foreach ($insertData as $data) {
                    try {
                        DB::table('words')->insertOrIgnore($data);
                    } catch (\Exception $e) {
                        $this->command->error('Error inserting word: ' . json_encode($data));
                        $this->command->error($e->getMessage());
                    }
                }
            }
        }
        
        $this->command->info('Seeded ' . DB::table('words')->count() . ' words');
    }
}
