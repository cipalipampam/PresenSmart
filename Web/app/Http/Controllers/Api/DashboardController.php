<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Announcement;
use App\Services\SettingCache;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Get Schedule (single cached read)
        $settings      = SettingCache::all();
        $checkInEnd    = $settings->get('check_in_end', '07:15');
        $checkOutStart = $settings->get('check_out_start', '15:00');

        // 2. Get today's attendance status
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('recorded_at', today())
            ->first();

        $status = match(true) {
            $attendance === null                                       => 'Belum Absen',
            $attendance->status === 'sick'                            => 'Sakit',
            $attendance->status === 'permission'                      => 'Izin',
            $attendance->status === 'present' && $attendance->check_out_time !== null => 'Selesai',
            $attendance->status === 'present'                         => 'Hadir',
            default                                                   => 'Belum Absen',
        };

        // 3. Get Active Announcements — return as structured objects, not flat strings
        $announcements = Announcement::where('is_active', true)
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'content', 'created_at'])
            ->map(fn($item) => [
                'id'         => $item->id,
                'title'      => $item->title,
                'content'    => $item->content,
                'created_at' => $item->created_at?->toDateTimeString(),
            ]);

        // 4. Calculate monthly stats
        $month = today()->month;
        $year = today()->year;
        
        $hadir = Attendance::where('user_id', $user->id)
                    ->whereMonth('recorded_at', $month)
                    ->whereYear('recorded_at', $year)
                    ->where('status', 'present')
                    ->count();
                    
        $izin = Attendance::where('user_id', $user->id)
                    ->whereMonth('recorded_at', $month)
                    ->whereYear('recorded_at', $year)
                    ->whereIn('status', ['permission', 'sick'])
                    ->count();
                    
        $alfa = Attendance::where('user_id', $user->id)
                    ->whereMonth('recorded_at', $month)
                    ->whereYear('recorded_at', $year)
                    ->where('status', 'alpha')
                    ->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'schedule' => [
                    'masuk'  => substr($checkInEnd, 0, 5),
                    'pulang' => substr($checkOutStart, 0, 5),
                    'status' => $status,
                ],
                'stats' => [
                    'hadir' => $hadir,
                    'izin'  => $izin,
                    'alfa'  => $alfa,
                ],
                'announcements' => $announcements,
            ]
        ]);
    }
}
