<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends BaseController
{
    /**
     * Display a listing of the resource with optional filtering.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Notification::query();

        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('notification_timestamp', [$startDate, $endDate]);
        }

        // Order by most recent first
        $notifications = $query->orderBy('notification_timestamp', 'desc')
                             ->paginate(15);

        return $this->sendResponse(
            NotificationResource::collection($notifications),
            'Notifications retrieved successfully.'
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
            'body' => 'required|string',
            'notification_timestamp' => 'required|date',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Set notification timestamp to now if not provided
        if (empty($input['notification_timestamp'])) {
            $input['notification_timestamp'] = now();
        }

        $notification = Notification::create($input);

        // Here you might want to trigger actual push notifications
        // $this->sendPushNotification($notification);


        return $this->sendResponse(
            new NotificationResource($notification),
            'Notification created successfully.',
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
        $notification = Notification::find($id);

        if (is_null($notification)) {
            return $this->sendError('Notification not found.');
        }

        return $this->sendResponse(
            new NotificationResource($notification),
            'Notification retrieved successfully.'
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
        $notification = Notification::find($id);

        if (is_null($notification)) {
            return $this->sendError('Notification not found.');
        }

        $input = $request->all();

        $validator = Validator::make($input, [
            'title' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required|string',
            'notification_timestamp' => 'sometimes|required|date',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $notification->update($input);

        return $this->sendResponse(
            new NotificationResource($notification),
            'Notification updated successfully.'
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
        $notification = Notification::find($id);

        if (is_null($notification)) {
            return $this->sendError('Notification not found.');
        }

        $notification->delete();

        return $this->sendResponse([], 'Notification deleted successfully.');
    }

    /**
     * Mark a notification as read.
     *
     * @param string $id
     * @return JsonResponse
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = Notification::find($id);

        if (is_null($notification)) {
            return $this->sendError('Notification not found.');
        }

        $notification->update(['read_at' => now()]);

        return $this->sendResponse(
            new NotificationResource($notification),
            'Notification marked as read.'
        );
    }

    /**
     * Get unread notifications count.
     *
     * @return JsonResponse
     */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::whereNull('read_at')
                           ->where('notification_timestamp', '<=', now())
                           ->count();

        return $this->sendResponse(
            ['unread_count' => $count],
            'Unread notifications count retrieved successfully.'
        );
    }
}
