<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\User;
use App\Services\Web\Dashboard\AdminDashboardStatsService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AdminDashboardStatsService $statsService,
    ) {}

    public function index()
    {
        $stats = $this->statsService->current();
        $totalStudents = $stats['total_students'];
        $totalEmployees = $stats['total_employees'];
        $userCount = $totalStudents;

        // Ambil pengaturan sistem
        $setting = Setting::pluck('value', 'key')->all();

        // Presensi hari ini dengan pagination
        $todayPresensi = Attendance::with(['user.student', 'user.employee'])
            ->whereDate('recorded_at', now()->toDateString())
            ->orderBy('recorded_at', 'desc')
            ->paginate(10);

        // Attach proof_url to today presensi
        $todayPresensi->getCollection()->transform(function ($item) {
            $item->proof_url = $item->proof_image
                ? route('admin.attendances.proof', $item->id)
                : null;
            return $item;
        });

        $todayPresensiCount = $todayPresensi->total();
        $pendingApprovals = $stats['pending_approvals'];

        // Quick action: pengajuan izin/sakit yang menunggu review (maks 5)
        $pendingRequests = Attendance::with(['user.student', 'user.employee'])
            ->whereIn('status', ['permission', 'sick'])
            ->whereNull('is_approved')
            ->latest('recorded_at')
            ->take(5)
            ->get();

        $pendingRequests->transform(function ($item) {
            $item->proof_url = $item->proof_image
                ? route('admin.attendances.proof', $item->id)
                : null;
            return $item;
        });

        // 7 days trend: On-time vs Late vs Permission
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $label = now()->subDays($i)->translatedFormat('D, d M');

            $presentOnTime = Attendance::whereDate('recorded_at', $date)
                ->where('status', 'present')
                ->where('is_late', false)
                ->count();

            $presentLate = Attendance::whereDate('recorded_at', $date)
                ->where('status', 'present')
                ->where('is_late', true)
                ->count();

            $permissionCount = Attendance::whereDate('recorded_at', $date)
                ->whereIn('status', ['permission', 'sick'])
                ->count();

            $weeklyData[$date] = [
                'label' => $label,
                'ontime' => $presentOnTime,
                'late' => $presentLate,
                'permission' => $permissionCount,
                'total_present' => $presentOnTime + $presentLate,
            ];
        }

        // Pengumuman aktif terbaru
        $recentAnnouncements = Announcement::where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        return view('admin.dashboard.index', [
            'stats' => $stats,
            'totalStudents' => $totalStudents,
            'totalEmployees' => $totalEmployees,
            'userCount' => $userCount,
            'todayPresensi' => $todayPresensi,
            'todayPresensiCount' => $todayPresensiCount,
            'pendingApprovals' => $pendingApprovals,
            'pendingRequests' => $pendingRequests,
            'weeklyData' => $weeklyData,
            'recentAnnouncements' => $recentAnnouncements,
            'setting' => $setting,
        ]);
    }
}
