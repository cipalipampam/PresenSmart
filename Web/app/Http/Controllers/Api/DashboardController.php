<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Attendance;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. Get Schedule
        $checkInEnd = Setting::where('key', 'check_in_end')->value('value') ?? '07:15';
        $checkOutStart = Setting::where('key', 'check_out_start')->value('value') ?? '15:00';

        // 2. Get User Status
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('recorded_at', today())
            ->first();

        $status = 'Belum Absen';
        if ($attendance) {
            if ($attendance->status === 'sick') {
                $status = 'Sakit';
            } elseif ($attendance->status === 'permission') {
                $status = 'Izin';
            } elseif ($attendance->status === 'present') {
                if ($attendance->check_out_time) {
                    $status = 'Selesai';
                } else {
                    $status = 'Hadir';
                }
            }
        }

        // 3. Get Active Announcements
        $announcements = Announcement::where('is_active', true)
            ->latest()
            ->take(5)
            ->get(['title', 'content']);
        
        $pengumumanList = $announcements->map(function ($item) {
            return $item->title . ($item->content ? ' - ' . $item->content : '');
        })->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'schedule' => [
                    'masuk' => substr($checkInEnd, 0, 5), // '07:15:00' -> '07:15'
                    'pulang' => substr($checkOutStart, 0, 5),
                    'status' => $status
                ],
                'announcements' => $pengumumanList
            ]
        ]);
    }
}
