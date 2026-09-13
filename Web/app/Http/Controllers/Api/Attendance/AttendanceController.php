<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attendance\CheckInRequest;
use App\Http\Requests\Api\Attendance\PermissionRequest;
use App\Models\Attendance;
use App\Services\Api\Attendance\AttendanceService;
use App\Services\Shared\Storage\AttendanceProofStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

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
                'data' => $attendance,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (Throwable $exception) {
            report($exception);

            return $this->serverError();
        }
    }

    public function checkOut(Request $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->checkOut($request->all(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Berhasil absen pulang.',
                'data' => $attendance,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (Throwable $exception) {
            report($exception);

            return $this->serverError();
        }
    }

    public function permission(PermissionRequest $request): JsonResponse
    {
        try {
            $attendance = $this->attendanceService->submitPermission($request->validated(), $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Izin / Sakit berhasil dicatat.',
                'data' => $attendance,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (Throwable $exception) {
            report($exception);

            return $this->serverError();
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
            'data' => $attendances,
        ]);
    }

    public function proof(Attendance $attendance, AttendanceProofStorage $proofStorage)
    {
        return $proofStorage->response($attendance);
    }

    private function serverError(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.',
        ], 500);
    }
}
