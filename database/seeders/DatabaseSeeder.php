<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a test user if it doesn't exist
        if (Schema::hasTable('users')) {
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
        }

        // Run the seeders
        $this->call([
            // WordsTableSeeder::class, // Disabled the old seeder
            SeedWordsTable::class,    // Using the new seeder
        ]);
    }
}
