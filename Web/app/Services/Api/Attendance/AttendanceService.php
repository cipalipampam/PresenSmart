<?php

namespace App\Services\Api\Attendance;

use App\Models\Attendance;
use App\Models\User;
use App\Services\SettingCache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class AttendanceService
{
    /**
     * Submit check in with geolocation boundaries.
     */
    public function checkIn(array $data, User $user)
    {
        // 1. Validate if user already checked in today
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('recorded_at', Carbon::today())
            ->first();

        if ($todayAttendance) {
            throw ValidationException::withMessages([
                'attendance' => ['Anda sudah melakukan presensi hari ini.']
            ]);
        }

        // 2. Fetch school coordinates and radius (single cached read)
        $settings     = SettingCache::all();
        $schoolLat    = $settings->get('school_lat');
        $schoolLong   = $settings->get('school_long');
        $schoolRadius = $settings->get('school_radius');

        if (!$schoolLat || !$schoolLong || !$schoolRadius) {
            throw ValidationException::withMessages([
                'attendance' => ['Konfigurasi lokasi sekolah belum diatur oleh admin.']
            ]);
        }

        // 3. Calculate Haversine distance
        $distance = $this->calculateDistance(
            (float) $schoolLat,
            (float) $schoolLong,
            (float) $data['latitude'],
            (float) $data['longitude']
        );

        if ($distance > (int) $schoolRadius) {
            throw ValidationException::withMessages([
                'attendance' => [sprintf('Anda berada di luar jangkauan presensi sekolah. (Jarak anda: %dm, maksimal: %dm)', round($distance), $schoolRadius)]
            ]);
        }

        // 4. Handle time restrictions
        $checkInEndStr    = $settings->get('check_in_end', '07:00');
        $toleranceMinutes = (int) $settings->get('late_tolerance_minutes', 10);

        $cutoffOnTime = Carbon::createFromTimeString($checkInEndStr);
        $cutoffLate   = $cutoffOnTime->copy()->addMinutes($toleranceMinutes);
        $currentTime  = Carbon::now();

        if ($currentTime->greaterThan($cutoffLate)) {
            throw ValidationException::withMessages([
                'attendance' => ['Batas toleransi terlambat habis (Maks. ' . $cutoffLate->format('H:i') . '). Pintu absensi masuk telah ditutup. Anda tercatat Alfa.']
            ]);
        }
        $isLate = $currentTime->greaterThan($cutoffOnTime);

        // 5. Handle proof image if any
        $proofPath = null;
        if (isset($data['proof_image'])) {
            $proofPath = $data['proof_image']->store('attendances', 'public');
        }

        $attendance = DB::transaction(function () use ($user, $data, $isLate, $proofPath) {
            $record = Attendance::create([
                'user_id'     => $user->id,
                'status'      => 'present',
                'is_late'     => $isLate,
                'is_approved' => true,
                'latitude'    => $data['latitude'],
                'longitude'   => $data['longitude'],
                'notes'       => $data['notes'] ?? null,
                'proof_image' => $proofPath,
                'recorded_at' => Carbon::now(),
            ]);

            event(new \App\Events\AttendanceLogged($record));
            event(new \App\Events\DashboardStatsUpdated());

            return $record;
        });

        return $attendance;
    }

    /**
     * Submit leave or sickness without geolocation.
     */
    public function submitPermission(array $data, User $user)
    {
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('recorded_at', Carbon::today())
            ->first();

        if ($todayAttendance) {
            throw ValidationException::withMessages([
                'attendance' => ['Anda sudah mengirim data untuk hari ini.']
            ]);
        }

        $proofPath = null;
        if (isset($data['proof_image'])) {
            $proofPath = $data['proof_image']->store('attendances', 'public');
        }

        $attendance = DB::transaction(function () use ($user, $data, $proofPath) {
            $record = Attendance::create([
                'user_id'     => $user->id,
                'status'      => $data['status'],
                'notes'       => $data['notes'] ?? null,
                'proof_image' => $proofPath,
                'recorded_at' => Carbon::now(),
            ]);

            event(new \App\Events\AttendanceLogged($record));
            event(new \App\Events\DashboardStatsUpdated());

            return $record;
        });

        return $attendance;
    }

    public function checkOut(array $data, User $user)
    {
        $today = Carbon::today();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('recorded_at', $today)
            ->where('status', 'present')
            ->first();

        if (!$attendance) {
            throw ValidationException::withMessages([
                'attendance' => ['Anda belum melakukan absensi masuk (Check-in) hari ini.']
            ]);
        }

        if ($attendance->check_out_time) {
            throw ValidationException::withMessages([
                'attendance' => ['Anda sudah melakukan absensi pulang hari ini.']
            ]);
        }

        $checkOutStart = Carbon::createFromTimeString(SettingCache::get('check_out_start', '15:00'));
        $currentTime   = Carbon::now();

        if ($currentTime->lessThan($checkOutStart)) {
            throw ValidationException::withMessages([
                'attendance' => ['Waktu absensi pulang belum dimulai (Minimal ' . $checkOutStart->format('H:i') . ').']
            ]);
        }

        DB::transaction(function () use ($attendance, $currentTime) {
            $attendance->update(['check_out_time' => $currentTime]);

            event(new \App\Events\AttendanceLogged($attendance));
            event(new \App\Events\DashboardStatsUpdated());
        });

        return $attendance;
    }

    /**
     * Paginated and filtered History.
     */
    public function history(User $user, $month = null, $year = null)
    {
        $query = Attendance::where('user_id', $user->id)->orderBy('recorded_at', 'desc');

        if ($month) {
            $query->whereMonth('recorded_at', $month);
        }

        if ($year) {
            $query->whereYear('recorded_at', $year);
        }

        return $query->paginate(15);
    }

    /**
     * Calculate Distance in meters using Haversine algorithm.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
