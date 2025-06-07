<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\NormalRangeResource;
use App\Models\NormalRange;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NormalRangeController extends BaseController
{
    /**
     * Display a listing of the resource with optional filtering.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = NormalRange::query();

        // Filter by species if provided
        if ($request->has('species')) {
            $query->where('species', $request->species);
        }

        // Filter by category if provided
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $normalRanges = $query->paginate(10);
        
        return $this->sendResponse(
            NormalRangeResource::collection($normalRanges),
            'Normal ranges retrieved successfully.'
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
            'unit' => 'required|string|max:50',
            'min_value' => 'required|numeric',
            'max_value' => 'required|numeric|gte:min_value',
            'species' => 'required|string|max:100',
            'category' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $normalRange = NormalRange::create($input);

        return $this->sendResponse(
            new NormalRangeResource($normalRange),
            'Normal range created successfully.',
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
        $normalRange = NormalRange::find($id);

        if (is_null($normalRange)) {
            return $this->sendError('Normal range not found.');
        }

        return $this->sendResponse(
            new NormalRangeResource($normalRange),
            'Normal range retrieved successfully.'
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
        $normalRange = NormalRange::find($id);

        if (is_null($normalRange)) {
            return $this->sendError('Normal range not found.');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'sometimes|required|string|max:255',
            'unit' => 'sometimes|required|string|max:50',
            'min_value' => 'sometimes|required|numeric',
            'max_value' => 'sometimes|required|numeric|gte:min_value',
            'species' => 'sometimes|required|string|max:100',
            'category' => 'sometimes|required|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $normalRange->update($input);

        return $this->sendResponse(
            new NormalRangeResource($normalRange),
            'Normal range updated successfully.'
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
        $normalRange = NormalRange::find($id);

        if (is_null($normalRange)) {
            return $this->sendError('Normal range not found.');
        }

        $normalRange->delete();

        return $this->sendResponse([], 'Normal range deleted successfully.');
    }
}
