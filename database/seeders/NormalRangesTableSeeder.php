<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class NormalRangesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $filePath = 'C:/Users/NAMI/Desktop/Vet dict database/normal_ranges.json';
        
        if (!File::exists($filePath)) {
            $this->command->error("File not found: $filePath");
            return;
        }
        
        // Read the file with explicit UTF-8 encoding
        $json = file_get_contents($filePath);
        $json = mb_convert_encoding($json, 'UTF-8', 'auto');
        
        $ranges = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Failed to decode JSON: ' . json_last_error_msg());
            return;
        }
        
        if (empty($ranges)) {
            $this->command->warn('No normal ranges data found in the JSON file.');
            return;
        }
        
        $count = 0;
        
        foreach ($ranges as $range) {
            try {
                DB::table('normal_ranges')->updateOrInsert(
                    ['id' => $range['id'] ?? null],
                    [
                        'name' => $range['name'] ?? 'Unknown',
                        'unit' => $range['unit'] ?? '',
                        'min_value' => $range['minValue'] ?? $range['min_value'] ?? null,
                        'max_value' => $range['maxValue'] ?? $range['max_value'] ?? null,
                        'species' => $range['species'] ?? 'General',
                        'category' => $range['category'] ?? 'General',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $this->command->error('Error importing normal range ' . ($range['id'] ?? 'unknown') . ': ' . $e->getMessage());
            }
        }
        
        $this->command->info("Successfully imported/updated $count normal ranges.");
    }
}
