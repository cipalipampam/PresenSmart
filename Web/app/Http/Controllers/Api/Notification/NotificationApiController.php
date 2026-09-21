<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Notification\NotificationIndexRequest;
use App\Services\Api\Notification\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    public function index(NotificationIndexRequest $request): JsonResponse
    {
        $data = $this->notificationService->forUser(
            $request->user(),
            $request->validated('filter') ?? 'all',
            $request->validated('per_page') ?? 15,
        );

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil diambil.',
            'data' => $data,
        ]);
    }

    public function markAsRead(Request $request, int $notification): JsonResponse
    {
        $appNotification = $this->notificationService->markAsRead($request->user(), $notification);

        if (! $appNotification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai telah dibaca.',
            'data' => $appNotification,
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $updated = $this->notificationService->markAllAsRead($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai dibaca.',
            'data' => ['updated_count' => $updated],
        ]);
    }
}
