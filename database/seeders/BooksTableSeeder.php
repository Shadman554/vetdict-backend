<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if table doesn't exist
        if (!Schema::hasTable('books')) {
            $this->command->error("Books table does not exist!");
            return;
        }

        // Path to your JSON file
        $jsonPath = database_path('data/books.json');
        
        if (!File::exists($jsonPath)) {
            $this->command->error("Books JSON file not found at: {$jsonPath}");
            return;
        }

        try {
            $json = File::get($jsonPath);
            $books = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON: ' . json_last_error_msg());
            }

            if (!is_array($books) || empty($books)) {
                $this->command->warn('No books data found in JSON file.');
                return;
            }

            $tableColumns = Schema::getColumnListing('books');
            $chunks = array_chunk($books, 50);
            $count = 0;

            foreach ($chunks as $chunk) {
                $records = [];
                
                foreach ($chunk as $book) {
                    if (!is_array($book)) {
                        $this->command->warn('Skipping invalid book data');
                        continue;
                    }
                    
                    // Only include columns that exist in the database
                    $record = array_intersect_key($book, array_flip($tableColumns));
                    
                    // Handle special fields
                    if (isset($book['coverUrl']) && !isset($record['cover_url'])) {
                        $record['cover_url'] = $book['coverUrl'];
                    }
                    
                    if (isset($book['downloadUrl']) && !isset($record['download_url'])) {
                        $record['download_url'] = $book['downloadUrl'];
                    }
                    
                    // Handle added_at timestamp
                    if (isset($book['addedAt']['_seconds'])) {
                        $record['added_at'] = Carbon::createFromTimestamp($book['addedAt']['_seconds']);
                    } elseif (!isset($record['added_at'])) {
                        $record['added_at'] = now();
                    }
                    
                    // Ensure required fields
                    $record['id'] = $book['id'] ?? (string) uniqid();
                    $record['title'] = $book['title'] ?? 'Untitled';
                    $record['created_at'] = $record['created_at'] ?? now();
                    $record['updated_at'] = $record['updated_at'] ?? now();
                    
                    $records[] = $record;
                }
                
                if (!empty($records)) {
                    // Use insertOrIgnore to skip duplicates
                    DB::table('books')->insertOrIgnore($records);
                    $count += count($records);
                    $this->command->info("Inserted {$count} books");
                }
            }

            $this->command->info("Successfully seeded {$count} books");
            
        } catch (\Exception $e) {
            $this->command->error("Error seeding books: " . $e->getMessage());
            $this->command->error("File: " . $e->getFile() . " Line: " . $e->getLine());
        }
    }
}
