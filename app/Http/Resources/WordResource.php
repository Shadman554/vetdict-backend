<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WordResource extends JsonResource
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
            'kurdish' => $this->kurdish,
            'arabic' => $this->arabic,
            'description' => $this->description,
            'barcode' => $this->barcode,
            'is_saved' => (bool) $this->is_saved,
            'is_favorite' => (bool) $this->is_favorite,
            'exported_at' => $this->exported_at ? $this->exported_at->toIso8601String() : null,
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        // Set appropriate headers for the response
        $response->header('Content-Type', 'application/json; charset=utf-8');
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Connection', 'keep-alive');
        $response->header('Keep-Alive', 'timeout=60');
        
        // Enable CORS
        $response->header('Access-Control-Allow-Origin', '*');
    }
}
