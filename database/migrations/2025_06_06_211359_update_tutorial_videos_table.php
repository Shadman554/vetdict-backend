<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tutorial_videos', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->string('video_id', 100)->change();
            $table->integer('duration')->unsigned()->nullable()->after('video_id');
            $table->string('thumbnail_url', 255)->nullable()->after('duration');
            $table->integer('view_count')->unsigned()->default(0)->after('thumbnail_url');
            $table->integer('like_count')->unsigned()->default(0)->after('view_count');
            $table->boolean('is_published')->default(false)->after('like_count');
            $table->timestamp('published_at')->nullable()->after('is_published');
            $table->json('tags')->nullable()->after('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tutorial_videos', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'duration',
                'thumbnail_url',
                'view_count',
                'like_count',
                'is_published',
                'published_at',
                'tags'
            ]);
            
            // Revert the video_id column change if needed
            $table->string('video_id')->change();
        });
    }
};
