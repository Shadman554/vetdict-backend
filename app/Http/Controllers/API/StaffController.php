<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StaffController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $staffMembers = Staff::orderBy('name')->paginate(10);
        return $this->sendResponse(
            StaffResource::collection($staffMembers),
            'Staff members retrieved successfully.'
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
            'job' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'snapchat' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'staff_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('public/staff', $filename);
            $input['photo'] = Storage::url($path);
        }

        $staff = Staff::create($input);

        return $this->sendResponse(
            new StaffResource($staff),
            'Staff member created successfully.',
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
        $staff = Staff::find($id);

        if (is_null($staff)) {
            return $this->sendError('Staff member not found.');
        }

        return $this->sendResponse(
            new StaffResource($staff),
            'Staff member retrieved successfully.'
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
        $staff = Staff::find($id);

        if (is_null($staff)) {
            return $this->sendError('Staff member not found.');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'sometimes|required|string|max:255',
            'job' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'snapchat' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Handle photo update
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($staff->photo) {
                $oldPhotoPath = str_replace('/storage', 'public', $staff->photo);
                if (Storage::exists($oldPhotoPath)) {
                    Storage::delete($oldPhotoPath);
                }
            }
            
            // Upload new photo
            $photo = $request->file('photo');
            $filename = 'staff_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('public/staff', $filename);
            $input['photo'] = Storage::url($path);
        }

        $staff->update($input);

        return $this->sendResponse(
            new StaffResource($staff),
            'Staff member updated successfully.'
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
        $staff = Staff::find($id);

        if (is_null($staff)) {
            return $this->sendError('Staff member not found.');
        }

        // Delete photo if exists
        if ($staff->photo) {
            $photoPath = str_replace('/storage', 'public', $staff->photo);
            if (Storage::exists($photoPath)) {
                Storage::delete($photoPath);
            }
        }

        $staff->delete();

        return $this->sendResponse([], 'Staff member deleted successfully.');
    }
}
