<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Setting;
use App\Models\Presensi;

class AdminController extends Controller
{
     public function dashboard()
    {
        // Total user
        $userCount = User::where('role', 'siswa')->count();

        // Tanggal hari ini
        $today = now()->toDateString();

        // Presensi hari ini
        $todayPresensi = Presensi::with('user')
            ->whereDate('waktu', $today)
            ->orderBy('waktu', 'desc')
            ->get();

        // Hitung total presensi hari ini
        $todayPresensiCount = $todayPresensi->count();

        // Hitung status presensi
        $presensiStats = [
            'hadir' => Presensi::whereDate('waktu', $today)
                ->where('status', 'hadir')
                ->count(),
            'izin' => Presensi::whereDate('waktu', $today)
                ->where('status', 'izin')
                ->count(),
            'sakit' => Presensi::whereDate('waktu', $today)
                ->where('status', 'sakit')
                ->count(),
            'alpha' => Presensi::whereDate('waktu', $today)
                ->where('status', 'alpha')
                ->count(),
        ];

        // Ambil setting koordinat dan radius
        $settings = Setting::pluck('value', 'key')->all();
        $setting = [
            'school_lat'    => $settings['school_lat']    ?? '-',
            'school_long'   => $settings['school_long']   ?? '-',
            'school_radius' => $settings['school_radius'] ?? '-',
        ];

        return view('admin.dashboard', [
            'userCount' => $userCount,
            'todayPresensi' => $todayPresensi,
            'todayPresensiCount' => $todayPresensiCount,
            'presensiStats' => $presensiStats,
            'setting' => $setting
        ]);
    }
    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'nisn' => 'required|unique:users,nisn,'.$id,
            'kelas' => 'required',
        ]);
        $user->update($request->only(['name', 'email', 'nisn', 'kelas']));
        return redirect()->route('admin.users')->with('success', 'User updated!');
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User deleted!');
    }

    public function lokasi()
    {
        $lat = Setting::where('key', 'school_lat')->first()?->value;
        $long = Setting::where('key', 'school_long')->first()?->value;
        $radius = Setting::where('key', 'school_radius')->first()?->value;
        return view('admin.lokasi', compact('lat', 'long', 'radius'));
    }

    public function updateLokasi(Request $request)
    {
        Setting::updateOrCreate(['key' => 'school_lat'], ['value' => $request->lat]);
        Setting::updateOrCreate(['key' => 'school_long'], ['value' => $request->long]);
        Setting::updateOrCreate(['key' => 'school_radius'], ['value' => $request->radius]);
        return redirect()->route('admin.lokasi')->with('success', 'Lokasi sekolah diperbarui!');
    }

    public function createUser()
    {
        return view('admin.create_user');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
            'nisn' => 'required|unique:users,nisn',
            'kelas' => 'required',
        ]);
        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'nisn' => $request->nisn,
            'kelas' => $request->kelas,
        ]);
        return redirect()->route('admin.users')->with('success', 'User berhasil ditambah!');
    }

    public function logout()
    {
        \Auth::logout();
        return redirect()->route('admin.login_form');
    }

    public function absen(Request $request)
    {
        $tanggal = $request->query('tanggal', now()->toDateString());
        $kelas = $request->query('kelas', '');
        $users = \App\Models\User::orderBy('name')->get();
        $daftarKelas = \App\Models\User::select('kelas')->distinct()->orderBy('kelas')->pluck('kelas');
        $presensis = \App\Models\Presensi::with('user')
            ->whereDate('waktu', $tanggal)
            ->when($kelas, function($q) use ($kelas) {
                $q->whereHas('user', function($qu) use ($kelas) {
                    $qu->where('kelas', $kelas);
                });
            })
            ->orderBy('waktu', 'asc')
            ->get();
        return view('admin.absen.index', compact('presensis', 'users', 'tanggal', 'kelas', 'daftarKelas'));
    }

    public function createAbsen(Request $request)
    {
        $users = \App\Models\User::orderBy('name')->get();
        $tanggal = $request->query('tanggal', now()->toDateString());
        return view('admin.absen.create', compact('users', 'tanggal'));
    }

    public function storeAbsen(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'waktu' => 'required|date',
            'status' => 'required|string',
        ]);
        \App\Models\Presensi::create([
            'user_id' => $request->user_id,
            'waktu' => $request->waktu,
            'lat' => 0,
            'long' => 0,
            'status' => $request->status,
        ]);
        return redirect()->route('admin.absen')->with('success', 'Presensi berhasil ditambahkan.');
    }

    public function editAbsen($id)
    {
        $presensi = \App\Models\Presensi::findOrFail($id);
        $users = \App\Models\User::orderBy('name')->get();
        return view('admin.absen.edit', compact('presensi', 'users'));
    }

    public function updateAbsen(Request $request, $id)
    {
        $presensi = \App\Models\Presensi::findOrFail($id);

        // Jika hanya update status (dari tabel)
        if ($request->has('status') && !$request->has('user_id')) {
            $presensi->update([
                'status' => $request->status,
            ]);
            return back()->with('success', 'Status presensi berhasil diupdate.');
        }

        // Jika update lengkap (dari form edit)
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'waktu' => 'required|date',
            'status' => 'required|string',
        ]);
        $presensi->update([
            'user_id' => $request->user_id,
            'waktu' => $request->waktu,
            'status' => $request->status,
        ]);
        return redirect()->route('admin.absen')->with('success', 'Presensi berhasil diupdate.');
    }

    public function deleteAbsen($id)
    {
        $presensi = \App\Models\Presensi::findOrFail($id);
        $presensi->delete();
        return redirect()->route('admin.absen')->with('success', 'Presensi berhasil dihapus.');
    }
}
