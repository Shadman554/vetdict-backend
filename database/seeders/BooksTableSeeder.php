<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $json = File::get('C:/Users/NAMI/Desktop/Vet dict database/books.json');
        $books = json_decode($json, true);

        foreach ($books as $book) {
            DB::table('books')->updateOrInsert(
                ['id' => $book['id']],
                [
                    'title' => $book['title'] ?? 'Untitled',
                    'description' => $book['description'] ?? null,
                    'cover_url' => $book['coverUrl'] ?? null,
                    'download_url' => $book['downloadUrl'] ?? null,
                    'category' => $book['category'] ?? 'General',
                    'added_at' => isset($book['addedAt']['_seconds']) ? 
                        \Carbon\Carbon::createFromTimestamp($book['addedAt']['_seconds']) : now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
