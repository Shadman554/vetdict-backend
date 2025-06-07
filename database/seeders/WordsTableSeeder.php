<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class WordsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = 'C:/Users/NAMI/Desktop/Vet dict database/words.json';
        
        if (!file_exists($filePath)) {
            $this->command->error("JSON file not found: {$filePath}");
            return;
        }
        
        $json = file_get_contents($filePath);
        $words = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Invalid JSON: ' . json_last_error_msg());
            return;
        }
        
        $this->command->info('Importing ' . count($words) . ' words...');
        
        foreach ($words as $wordData) {
            // Skip if word already exists
            if (\App\Models\Word::where('id', $wordData['id'])->exists()) {
                continue;
            }
            
            // Create new word
            \App\Models\Word::create([
                'id' => $wordData['id'],
                'name' => $wordData['name'],
                'arabic' => $wordData['arabic'] ?? null,
                'kurdish' => $wordData['kurdish'] ?? null,
                'description' => $wordData['description'] ?? null,
                'barcode' => $wordData['barcode'] ?? null,
                'is_saved' => $wordData['isSaved'] ?? false,
                'is_favorite' => $wordData['isFavorite'] ?? false,
                'exported_at' => $wordData['_exportedAt'] ?? now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        $this->command->info('Words imported successfully!');
    }
}
