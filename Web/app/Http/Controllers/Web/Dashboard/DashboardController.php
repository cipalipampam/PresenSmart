<?php

namespace App\Http\Controllers\Web\Dashboard;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Presensi;
use App\Models\Setting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitung jumlah user
        $userCount = User::where('role', 'siswa')->count();

        // Ambil pengaturan
        $setting = Setting::pluck('value', 'key')->all();

        // Presensi hari ini dengan pagination
        $todayPresensi = Presensi::with('user')
            ->whereDate('waktu', now()->toDateString())
            ->orderBy('waktu', 'desc')
            ->paginate(10); // Tambahkan pagination, 10 item per halaman

        // Hitung total presensi hari ini
        $todayPresensiCount = $todayPresensi->total();

        return view('admin.dashboard', [
            'userCount' => $userCount,
            'todayPresensi' => $todayPresensi,
            'todayPresensiCount' => $todayPresensiCount,
            'setting' => $setting
        ]);
    }
}
