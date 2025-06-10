<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\WordResource;
use App\Models\Word;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WordController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    /**
     * Display a paginated listing of words.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            // Log the request parameters
            \Log::info('API Request:', [
                'url' => request()->fullUrl(),
                'params' => request()->all()
            ]);

            // Get pagination parameters
            $perPage = min($request->input('per_page', 200), 200); // Default 200 items per page, max 200
            $page = max(1, (int) $request->input('page', 1));
            
            // Build the query
            $query = Word::query();
            
            // Add search if provided
            if ($search = $request->input('search')) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('arabic', 'like', "%{$search}%")
                      ->orWhere('kurdish', 'like', "%{$search}%");
                });
            }
            
            // Get the results
            $words = $query->orderBy('name')->get();
            
            // Transform the items
            $transformedItems = $words->map(function($word) {
                return [
                    'id' => $word->id,
                    'name' => $word->name,
                    'kurdish' => $word->kurdish,
                    'arabic' => $word->arabic,
                    'description' => $word->description,
                    'barcode' => $word->barcode,
                    'is_saved' => (bool) $word->is_saved,
                    'is_favorite' => (bool) $word->is_favorite,
                    'exported_at' => $word->exported_at,
                    'created_at' => $word->created_at,
                    'updated_at' => $word->updated_at,
                ];
            });
            
            return $this->sendResponse([
                'data' => $transformedItems,
                'meta' => [
                    'total' => $words->count()
                ]
            ], 'Words retrieved successfully');
            
        } catch (\Exception $e) {
            \Log::error('Error in WordController@index: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return $this->sendError('Error retrieving words', [], 500);
        }
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
            'arabic' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255',
            'is_saved' => 'boolean',
            'is_favorite' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $word = Word::create($input);

        return $this->sendResponse(new WordResource($word), 'Word created successfully.', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $word = Word::find($id);

        if (is_null($word)) {
            return $this->sendError('Word not found.');
        }

        return $this->sendResponse(new WordResource($word), 'Word retrieved successfully.');
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
        $word = Word::find($id);

        if (is_null($word)) {
            return $this->sendError('Word not found.');
        }


        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'sometimes|required|string|max:255',
            'kurdish' => 'nullable|string|max:255',
            'arabic' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'barcode' => 'nullable|string|max:255',
            'is_saved' => 'sometimes|boolean',
            'is_favorite' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $word->update($input);

        return $this->sendResponse(new WordResource($word), 'Word updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $word = Word::find($id);

        if (is_null($word)) {
            return $this->sendError('Word not found.');
        }


        $word->delete();

        return $this->sendResponse([], 'Word deleted successfully.');
    }
}
