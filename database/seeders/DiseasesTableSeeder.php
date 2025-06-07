<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DiseasesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $json = File::get('C:/Users/NAMI/Desktop/Vet dict database/diseases.json');
        $diseases = json_decode($json, true);

        foreach ($diseases as $disease) {
            DB::table('diseases')->updateOrInsert(
                ['id' => $disease['id']],
                [
                    'name' => $disease['name'] ?? null,
                    'kurdish' => $disease['kurdish'] ?? null,
                    'symptoms' => $disease['symptoms'] ?? null,
                    'cause' => $disease['cause'] ?? null,
                    'treatment' => $disease['treatment'] ?? null,
                    'prevention' => $disease['prevention'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
