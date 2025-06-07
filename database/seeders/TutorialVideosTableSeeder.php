<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TutorialVideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path to your JSON file
        $filePath = 'C:/Users/NAMI/Desktop/Vet dict database/tutorialVideos.json';
        
        if (!File::exists($filePath)) {
            $this->command->error("File not found: $filePath");
            return;
        }
        
        // Read the file with explicit UTF-8 encoding
        $json = file_get_contents($filePath);
        $json = mb_convert_encoding($json, 'UTF-8', 'auto');
        
        $videos = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error('Failed to decode JSON: ' . json_last_error_msg());
            return;
        }
        
        if (empty($videos)) {
            $this->command->warn('No tutorial videos data found in the JSON file.');
            return;
        }
        
        $count = 0;
        
        foreach ($videos as $video) {
            try {
                $publishedAt = null;
                
                // Handle different possible date formats
                if (isset($video['publishedAt'])) {
                    if (is_array($video['publishedAt']) && isset($video['publishedAt']['_seconds'])) {
                        $publishedAt = \Carbon\Carbon::createFromTimestamp($video['publishedAt']['_seconds']);
                    } else {
                        try {
                            $publishedAt = \Carbon\Carbon::parse($video['publishedAt']);
                        } catch (\Exception $e) {
                            $publishedAt = now();
                        }
                    }
                } else {
                    $publishedAt = now();
                }
                
                DB::table('tutorial_videos')->updateOrInsert(
                    ['id' => $video['id'] ?? null],
                    [
                        'title' => $video['Title'] ?? $video['title'] ?? 'Untitled Video',
                        'video_id' => $video['VideoID'] ?? $video['video_id'] ?? '',
                        'description' => $video['description'] ?? null,
                        'duration' => $video['duration'] ?? null,
                        'thumbnail_url' => $video['thumbnailUrl'] ?? $video['thumbnail_url'] ?? null,
                        'view_count' => $video['viewCount'] ?? $video['view_count'] ?? 0,
                        'like_count' => $video['likeCount'] ?? $video['like_count'] ?? 0,
                        'is_published' => $video['isPublished'] ?? $video['is_published'] ?? true,
                        'published_at' => $publishedAt,
                        'tags' => isset($video['tags']) ? 
                            (is_string($video['tags']) ? $video['tags'] : json_encode($video['tags'])) : null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                $this->command->error('Error importing video ' . ($video['id'] ?? 'unknown') . ': ' . $e->getMessage());
            }
        }
        
        $this->command->info("Successfully imported/updated $count tutorial videos.");
    }
}
