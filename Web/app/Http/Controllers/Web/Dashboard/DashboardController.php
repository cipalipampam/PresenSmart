<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;
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
        // Hitung jumlah user (students only for standard context)
        $userCount = User::role('siswa')->count();

        // Ambil pengaturan
        $setting = Setting::pluck('value', 'key')->all();

        // Presensi hari ini dengan pagination
        $todayPresensi = Attendance::with('user')
            ->whereDate('recorded_at', now()->toDateString())
            ->orderBy('recorded_at', 'desc')
            ->paginate(10); // Tambahkan pagination, 10 item per halaman

        // Hitung total presensi hari ini
        $todayPresensiCount = $todayPresensi->total();
        $pendingApprovals = $this->statsService->current()['pending_approvals'];

        return view('admin.dashboard.index', [
            'userCount' => $userCount,
            'todayPresensi' => $todayPresensi,
            'todayPresensiCount' => $todayPresensiCount,
            'pendingApprovals' => $pendingApprovals,
            'setting' => $setting,
        ]);
    }
}
