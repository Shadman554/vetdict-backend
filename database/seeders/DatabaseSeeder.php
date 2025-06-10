<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\WordsTableSeeder;
use Database\Seeders\DiseasesTableSeeder;
use Database\Seeders\BooksTableSeeder;
use Database\Seeders\DrugsTableSeeder;
use Database\Seeders\NormalRangesTableSeeder;
use Database\Seeders\StaffTableSeeder;
use Database\Seeders\TutorialVideosTableSeeder;
use Database\Seeders\JsonSeeder;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user if it doesn't exist
        User::firstOrCreate(
            ['id' => 'test_user_123'],
            [
                'username' => 'testuser',
                'today_points' => 0,
                'total_points' => 0,
                'last_updated' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Run all seeders
        $this->call([
            WordsTableSeeder::class,
            DiseasesTableSeeder::class,
            BooksTableSeeder::class,
            DrugsTableSeeder::class,
            NormalRangesTableSeeder::class,
            StaffTableSeeder::class,
            TutorialVideosTableSeeder::class,
        ]);
    }
}
