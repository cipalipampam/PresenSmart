<?php

namespace App\Services\Api\Attendance;

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\User;
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

        // 2. Fetch school coordinates and radius
        $schoolLat = Setting::where('key', 'school_lat')->first()?->value;
        $schoolLong = Setting::where('key', 'school_long')->first()?->value;
        $schoolRadius = Setting::where('key', 'school_radius')->first()?->value;

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

        // 4. Handle time restrictions (07:00 base, 10 min tolerance)
        $checkInEndStr = Setting::where('key', 'check_in_end')->first()?->value ?? '07:00';
        $toleranceMinutes = (int)(Setting::where('key', 'late_tolerance_minutes')->first()?->value ?? 10);
        
        $cutoffOnTime = Carbon::createFromTimeString($checkInEndStr);
        $cutoffLate = $cutoffOnTime->copy()->addMinutes($toleranceMinutes);
        $currentTime = Carbon::now();

        if ($currentTime->greaterThan($cutoffLate)) {
            throw ValidationException::withMessages([
                'attendance' => ['Batas toleransi terlambat habis (Maks. '.$cutoffLate->format('H:i').'). Pintu absensi masuk telah ditutup. Anda tercatat Alfa.']
            ]);
        }
        $isLate = $currentTime->greaterThan($cutoffOnTime);

        // 5. Handle proof image if any
        $proofPath = null;
        if (isset($data['proof_image'])) {
            $proofPath = $data['proof_image']->store('attendances', 'public');
        }

        return Attendance::create([
            'user_id' => $user->id,
            'status' => 'present',
            'is_late' => $isLate,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'notes' => $data['notes'] ?? null,
            'proof_image' => $proofPath,
            'recorded_at' => Carbon::now(),
        ]);
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

        return Attendance::create([
            'user_id' => $user->id,
            'status' => $data['status'],
            'notes' => $data['notes'],
            'proof_image' => $proofPath,
            'recorded_at' => Carbon::now(),
        ]);
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
     * Calculate Distance in meters using Haversine algorithm
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius of Earth in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
