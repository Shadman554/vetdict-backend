<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BookController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $books = Book::paginate(10);
        return $this->sendResponse(
            BookResource::collection($books),
            'Books retrieved successfully.'
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
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file_url' => 'required|url',
            'cover_image' => 'nullable|url',
            'pages' => 'nullable|integer|min:0',
            'language' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:255',
            'publication_date' => 'nullable|date',
            'isbn' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $book = Book::create($input);

        return $this->sendResponse(
            new BookResource($book),
            'Book created successfully.',
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
        $book = Book::find($id);

        if (is_null($book)) {
            return $this->sendError('Book not found.');
        }

        return $this->sendResponse(
            new BookResource($book),
            'Book retrieved successfully.'
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
        $book = Book::find($id);

        if (is_null($book)) {
            return $this->sendError('Book not found.');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'sometimes|required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file_url' => 'sometimes|required|url',
            'cover_image' => 'nullable|url',
            'pages' => 'nullable|integer|min:0',
            'language' => 'nullable|string|max:100',
            'publisher' => 'nullable|string|max:255',
            'publication_date' => 'nullable|date',
            'isbn' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $book->update($input);

        return $this->sendResponse(
            new BookResource($book),
            'Book updated successfully.'
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
        $book = Book::find($id);

        if (is_null($book)) {
            return $this->sendError('Book not found.');
        }

        // If the file is stored locally, you might want to delete it
        // Storage::disk('public')->delete($book->file_path);
        
        $book->delete();

        return $this->sendResponse([], 'Book deleted successfully.');
    }
}
