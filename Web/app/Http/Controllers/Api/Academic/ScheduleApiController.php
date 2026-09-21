<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Academic\ScheduleIndexRequest;
use App\Services\Api\Academic\ScheduleService;
use Illuminate\Http\JsonResponse;

class ScheduleApiController extends Controller
{
    public function __construct(
        private readonly ScheduleService $scheduleService,
    ) {}

    /**
     * Return the active weekly schedule that belongs to the authenticated student or teacher.
     */
    public function index(ScheduleIndexRequest $request): JsonResponse
    {
        $data = $this->scheduleService->forUser(
            $request->user(),
            $request->validated('day_of_week'),
        );

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum terhubung ke jadwal akademik aktif.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil diambil.',
            'data' => $data,
        ]);
    }
}
