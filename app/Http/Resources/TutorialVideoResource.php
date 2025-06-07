<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorialVideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'video_id' => $this->video_id,
            'duration' => $this->duration,
            'thumbnail_url' => $this->thumbnail_url,
            'view_count' => (int) $this->view_count,
            'like_count' => (int) $this->like_count,
            'is_published' => (bool) $this->is_published,
            'published_at' => $this->published_at ? Carbon::parse($this->published_at)->toIso8601String() : null,
            'tags' => $this->tags ?? [],
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
