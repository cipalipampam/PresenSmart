<?php

namespace App\Http\Controllers\Api\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Academic\ClassAttendanceIndexRequest;
use App\Http\Requests\Api\Academic\StoreClassAttendanceRequest;
use App\Models\Schedule;
use App\Services\Api\Academic\ClassAttendanceService;
use Illuminate\Http\JsonResponse;

use Illuminate\Validation\ValidationException;

class ClassAttendanceApiController extends Controller
{
    public function __construct(
        private readonly ClassAttendanceService $classAttendanceService,
    ) {}

    /**
     * Show a class roster pre-filled from saved subject attendance and approved daily leave.
     */
    public function index(ClassAttendanceIndexRequest $request, Schedule $schedule): JsonResponse
    {
        if (! $this->classAttendanceService->isAssignedTeacher($request->user(), $schedule)) {
            return $this->unassignedTeacherResponse();
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar presensi kelas berhasil diambil.',
            'data' => $this->classAttendanceService->getRosterData(
                $schedule,
                $request->validated('attendance_date'),
            ),
        ]);
    }

    /**
     * Save one attendance state for every submitted student in the schedule's classroom.
     */
    public function store(StoreClassAttendanceRequest $request, Schedule $schedule): JsonResponse
    {
        if (! $this->classAttendanceService->isAssignedTeacher($request->user(), $schedule)) {
            return $this->unassignedTeacherResponse();
        }

        try {
            $data = $request->validated();
            $result = $this->classAttendanceService->store(
                $request->user(),
                $schedule,
                $data['attendance_date'] ?? null,
                $data['attendances'],
            );

            return response()->json([
                'success' => true,
                'message' => 'Presensi mata pelajaran berhasil disimpan.',
                'data' => $result,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        }
    }

    private function unassignedTeacherResponse(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Anda tidak ditugaskan untuk mengabsen jadwal ini.',
        ], 403);
    }
}
