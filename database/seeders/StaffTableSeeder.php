<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class StaffTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $filePath = 'C:/Users/NAMI/Desktop/Vet dict database/staff.json';
        
        if (!File::exists($filePath)) {
            $this->command->error("File not found: $filePath");
            return;
        }
        
        // Read the file with explicit UTF-8 encoding
        $json = file_get_contents($filePath);
        $json = mb_convert_encoding($json, 'UTF-8', 'auto');
        
        $staffMembers = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Failed to decode JSON: ' . json_last_error_msg());
            return;
        }
        
        if (empty($staffMembers)) {
            $this->command->warn('No staff data found in the JSON file.');
            return;
        }
        
        $count = 0;
        
        foreach ($staffMembers as $staff) {
            try {
                DB::table('staff')->updateOrInsert(
                    ['id' => $staff['id'] ?? null],
                    [
                        'name' => $staff['name'] ?? 'Unknown',
                        'job' => $staff['job'] ?? 'Staff',
                        'description' => $staff['description'] ?? null,
                        'photo' => $staff['photo'] ?? '',
                        'facebook' => $staff['facebook'] ?? null,
                        'twitter' => $staff['twitter'] ?? null,
                        'instagram' => $staff['instagram'] ?? null,
                        'snapchat' => $staff['snapchat'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $this->command->error('Error importing staff member ' . ($staff['id'] ?? 'unknown') . ': ' . $e->getMessage());
            }
        }
        
        $this->command->info("Successfully imported/updated $count staff members.");
    }
}
