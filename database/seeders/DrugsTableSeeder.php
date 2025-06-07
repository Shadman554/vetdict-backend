<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DrugsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $filePath = 'C:/Users/NAMI/Desktop/Vet dict database/drugs.json';
        
        if (!File::exists($filePath)) {
            $this->command->error("File not found: $filePath");
            return;
        }
        
        // Read the file with explicit UTF-8 encoding
        $json = file_get_contents($filePath);
        $json = mb_convert_encoding($json, 'UTF-8', 'auto');
        
        $drugs = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Failed to decode JSON: ' . json_last_error_msg());
            return;
        }
        
        if (empty($drugs)) {
            $this->command->warn('No drugs data found in the JSON file.');
            return;
        }
        
        $count = 0;
        
        foreach ($drugs as $drug) {
            try {
                DB::table('drugs')->updateOrInsert(
                    ['id' => $drug['id'] ?? null],
                    [
                        'name' => $drug['name'] ?? 'Unknown Drug',
                        'usage' => $drug['usage'] ?? null,
                        'side_effect' => $drug['sideEffect'] ?? $drug['side_effect'] ?? null,
                        'class' => $drug['class'] ?? null,
                        'other_info' => $drug['otherInfo'] ?? $drug['other_info'] ?? null,
                        'created_at' => isset($drug['createdAt']['_seconds']) ? 
                            \Carbon\Carbon::createFromTimestamp($drug['createdAt']['_seconds']) : now(),
                        'updated_at' => now(),
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $this->command->error('Error importing drug ' . ($drug['id'] ?? 'unknown') . ': ' . $e->getMessage());
            }
        }
        
        $this->command->info("Successfully imported/updated $count drugs.");
    }
}
