<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\DiseaseResource;
use App\Models\Disease;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiseaseController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $diseases = Disease::paginate(10);
        return $this->sendResponse(
            DiseaseResource::collection($diseases),
            'Diseases retrieved successfully.'
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
            'name' => 'required|string|max:255',
            'kurdish' => 'nullable|string|max:255',
            'symptoms' => 'nullable|string',
            'cause' => 'nullable|string',
            'control' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prevention' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $disease = Disease::create($input);

        return $this->sendResponse(
            new DiseaseResource($disease),
            'Disease created successfully.',
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
        $disease = Disease::find($id);

        if (is_null($disease)) {
            return $this->sendError('Disease not found.');
        }

        return $this->sendResponse(
            new DiseaseResource($disease),
            'Disease retrieved successfully.'
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
        $disease = Disease::find($id);

        if (is_null($disease)) {
            return $this->sendError('Disease not found.');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'sometimes|required|string|max:255',
            'kurdish' => 'nullable|string|max:255',
            'symptoms' => 'nullable|string',
            'cause' => 'nullable|string',
            'control' => 'nullable|string',
            'treatment' => 'nullable|string',
            'prevention' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $disease->update($input);

        return $this->sendResponse(
            new DiseaseResource($disease),
            'Disease updated successfully.'
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
        $disease = Disease::find($id);

        if (is_null($disease)) {
            return $this->sendError('Disease not found.');
        }

        $disease->delete();

        return $this->sendResponse([], 'Disease deleted successfully.');
    }
}
