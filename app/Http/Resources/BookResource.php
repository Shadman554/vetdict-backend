<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'name' => $this->name,
            'author' => $this->author,
            'description' => $this->description,
            'file_url' => $this->file_url,
            'cover_image' => $this->cover_image,
            'pages' => $this->pages ? (int) $this->pages : null,
            'language' => $this->language,
            'publisher' => $this->publisher,
            'publication_date' => $this->publication_date,
            'isbn' => $this->isbn,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
