<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attendance\CheckInRequest;
use App\Http\Requests\Api\Attendance\PermissionRequest;
use App\Services\Api\Attendance\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private AttendanceService $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function checkIn(CheckInRequest $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->checkIn($request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan presensi.',
                'data' => $attendance
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function permission(PermissionRequest $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->submitPermission($request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Izin / Sakit berhasil dicatat.',
                'data' => $attendance
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request): JsonResponse
    {
        $month = $request->query('month');
        $year = $request->query('year');
        
        $attendances = $this->attendanceService->history($request->user(), $month, $year);

        return response()->json([
            'success' => true,
            'message' => 'Riwayat presensi berhasil diambil.',
            'data' => $attendances
        ]);
    }
}
