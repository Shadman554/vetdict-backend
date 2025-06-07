<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Word;
use Illuminate\Support\Facades\DB;

class ImportWords extends Command
{
    protected $signature = 'import:words {file}';
    protected $description = 'Import words from JSON file to database';

    public function handle()
    {
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $json = file_get_contents($file);
        $words = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('Invalid JSON: ' . json_last_error_msg());
            return 1;
        }

        $this->info('Starting import of ' . count($words) . ' words...');
        
        $imported = 0;
        $skipped = 0;
        
        // Disable query logging to save memory
        DB::disableQueryLog();
        
        // Process in chunks to save memory
        foreach (array_chunk($words, 100) as $chunk) {
            foreach ($chunk as $wordData) {
                try {
                    // Skip if word with this ID already exists
                    if (Word::where('id', $wordData['id'])->exists()) {
                        $skipped++;
                        continue;
                    }
                    
                    // Map the JSON fields to database columns
                    $word = new Word();
                    $word->id = $wordData['id'];
                    $word->name = $wordData['name'] ?? null;
                    $word->kurdish = $wordData['kurdish'] ?? null;
                    $word->arabic = $wordData['arabic'] ?? null;
                    $word->description = $wordData['description'] ?? null;
                    $word->barcode = $wordData['barcode'] ?? null;
                    $word->is_saved = $wordData['isSaved'] ?? false;
                    $word->is_favorite = $wordData['isFavorite'] ?? false;
                    $word->exported_at = $wordData['_exportedAt'] ?? null;
                    $word->save();
                    
                    $imported++;
                    
                    // Show progress
                    if ($imported % 100 === 0) {
                        $this->info("Imported $imported words...");
                    }
                } catch (\Exception $e) {
                    $this->error("Error importing word {$wordData['id']}: " . $e->getMessage());
                    $skipped++;
                }
            }
        }
        
        $this->info("\nImport completed!");
        $this->info("Imported: $imported");
        $this->info("Skipped (already exists or error): $skipped");
        
        return 0;
    }
}
