<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\DrugResource;
use App\Models\Drug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DrugController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $drugs = Drug::paginate(10);
        return $this->sendResponse(
            DrugResource::collection($drugs),
            'Drugs retrieved successfully.'
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
            'usage' => 'nullable|string',
            'side_effect' => 'nullable|string',
            'class' => 'nullable|string|max:255',
            'other_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $drug = Drug::create($input);

        return $this->sendResponse(
            new DrugResource($drug),
            'Drug created successfully.',
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
        $drug = Drug::find($id);

        if (is_null($drug)) {
            return $this->sendError('Drug not found.');
        }

        return $this->sendResponse(
            new DrugResource($drug),
            'Drug retrieved successfully.'
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
        $drug = Drug::find($id);

        if (is_null($drug)) {
            return $this->sendError('Drug not found.');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'sometimes|required|string|max:255',
            'usage' => 'nullable|string',
            'side_effect' => 'nullable|string',
            'class' => 'nullable|string|max:255',
            'other_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $drug->update($input);

        return $this->sendResponse(
            new DrugResource($drug),
            'Drug updated successfully.'
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
        $drug = Drug::find($id);

        if (is_null($drug)) {
            return $this->sendError('Drug not found.');
        }

        $drug->delete();

        return $this->sendResponse([], 'Drug deleted successfully.');
    }
}
