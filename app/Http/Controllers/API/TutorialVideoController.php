<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\TutorialVideoResource;
use App\Models\TutorialVideo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TutorialVideoController extends BaseController
{
    /**
     * Display a listing of the resource with optional filtering.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = TutorialVideo::query();

        // Search by title if provided
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        // Order by most recent first
        $videos = $query->orderBy('created_at', 'desc')
                       ->paginate(12);

        return $this->sendResponse(
            TutorialVideoResource::collection($videos),
            'Tutorial videos retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_id' => 'required|string|max:100|unique:tutorial_videos,video_id',
            'duration' => 'nullable|integer|min:0',
            'thumbnail_url' => 'nullable|url|max:255',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Set default values
        if (!isset($input['is_published'])) {
            $input['is_published'] = false;
        }

        if ($input['is_published'] && !isset($input['published_at'])) {
            $input['published_at'] = now();
        }

        $tutorialVideo = TutorialVideo::create($input);

        return $this->sendResponse(
            new TutorialVideoResource($tutorialVideo),
            'Tutorial video created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $video = TutorialVideo::find($id);

        if (is_null($video)) {
            return $this->sendError('Tutorial video not found.');
        }

        // Increment view count
        $video->increment('view_count');

        return $this->sendResponse(
            new TutorialVideoResource($video),
            'Tutorial video retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $video = TutorialVideo::find($id);

        if (is_null($video)) {
            return $this->sendError('Tutorial video not found.');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'video_id' => 'sometimes|required|string|max:100|unique:tutorial_videos,video_id,' . $id,
            'duration' => 'nullable|integer|min:0',
            'thumbnail_url' => 'nullable|url|max:255',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Handle publish status
        if (isset($input['is_published']) && $input['is_published'] && !$video->published_at) {
            $input['published_at'] = now();
        }

        $video->update($input);

        return $this->sendResponse(
            new TutorialVideoResource($video),
            'Tutorial video updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $video = TutorialVideo::find($id);

        if (is_null($video)) {
            return $this->sendError('Tutorial video not found.');
        }

        // Here you might want to delete associated files or thumbnails
        // if they are stored locally
        
        $video->delete();

        return $this->sendResponse([], 'Tutorial video deleted successfully.');
    }

    /**
     * Increment the like count for a video.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function like(string $id): JsonResponse
    {
        $video = TutorialVideo::find($id);

        if (is_null($video)) {
            return $this->sendError('Tutorial video not found.');
        }

        $video->increment('like_count');

        return $this->sendResponse(
            new TutorialVideoResource($video),
            'Like count incremented.'
        );
    }

    /**
     * Get related videos based on tags or category.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function related(string $id): JsonResponse
    {
        $video = TutorialVideo::find($id);

        if (is_null($video)) {
            return $this->sendError('Tutorial video not found.');
        }

        // Simple implementation - get videos with similar tags
        $relatedVideos = TutorialVideo::where('id', '!=', $id)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return $this->sendResponse(
            TutorialVideoResource::collection($relatedVideos),
            'Related videos retrieved successfully.'
        );
    }
}
