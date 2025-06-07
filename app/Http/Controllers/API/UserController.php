<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends BaseController
{
    /**
     * Register a new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $user = User::create($input);

        $token = $user->createToken('authToken')->plainTextToken;

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token
        ], 'User registered successfully.', 201);
    }

    /**
     * Login user and create token
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user = User::where('username', $request->username)->first();

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return $this->sendResponse([
            'user' => new UserResource($user),
            'token' => $token
        ], 'User logged in successfully.');
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->sendResponse([], 'Successfully logged out');
    }

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function show(Request $request): JsonResponse
    {
        return $this->sendResponse(new UserResource($request->user()), 'User retrieved successfully.');
    }

    /**
     * Update the authenticated User
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'today_points' => 'sometimes|integer|min:0',
            'total_points' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user->update($request->all());

        return $this->sendResponse(new UserResource($user), 'User updated successfully.');
    }

    /**
     * Delete the authenticated User
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();

        return $this->sendResponse([], 'User deleted successfully.');
    }
}
