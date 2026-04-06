<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresensiExport;
use Carbon\Carbon;

class PresensiController extends Controller
{
    protected $schoolLat;
    protected $schoolLong;
    protected $radius;

    public function __construct()
    {
        // Ambil semua setting dalam bentuk ['key' => 'value']
        $settings = Setting::pluck('value', 'key')->all();

        // Cast ke tipe yang benar
        $this->schoolLat  = isset($settings['school_lat'])   ? (float) $settings['school_lat']   : null;
        $this->schoolLong = isset($settings['school_long'])  ? (float) $settings['school_long']  : null;
        $this->radius     = isset($settings['school_radius'])? (int)   $settings['school_radius'] : null;
    }

    public function store(Request $request)
    {
        $tipePresensi = $request->input('tipe_presensi', 'hadir');

        // Validasi berbeda berdasarkan tipe presensi
        if ($tipePresensi == 'hadir') {
            $request->validate([
                'lat'  => 'required|numeric',
                'long' => 'required|numeric',
            ]);

            // Pastikan setting ada
            if (is_null($this->schoolLat) || is_null($this->schoolLong) || is_null($this->radius)) {
                return response()->json(['error' => 'Konfigurasi wilayah sekolah belum diatur'], 500);
            }

            $lat  = $request->lat;
            $long = $request->long;

            $distance = $this->haversine($lat, $long, $this->schoolLat, $this->schoolLong);

            if ($distance > $this->radius) {
                return response()->json(['error' => 'Diluar area sekolah'], 403);
            }
        } elseif (in_array($tipePresensi, ['izin', 'sakit'])) {
            $request->validate([
                'keterangan' => 'required|min:10|max:500', // Gunakan keterangan sebagai alasan
                'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120' // Maks 5MB
            ]);
        } else {
            return response()->json(['error' => 'Tipe presensi tidak valid'], 400);
        }

        $user = $request->user();

        // Cek apakah user sudah presensi hari ini
        $today = now()->toDateString();
        $alreadyPresent = $user->presensis()->whereDate('waktu', $today)->exists();

        if ($alreadyPresent) {
            return response()->json(['error' => 'Anda sudah melakukan presensi hari ini'], 403);
        }

        // Proses upload bukti untuk izin/sakit
        $buktiPath = null;
        if ($request->hasFile('bukti') && in_array($tipePresensi, ['izin', 'sakit'])) {
            $file = $request->file('bukti');
            $fileName = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('bukti_presensi', $fileName, 'public');
        }

        $presensi = Presensi::create([
            'user_id' => $user->id,
            'waktu'   => now(), // Sudah otomatis waktu Indonesia
            'lat'     => $tipePresensi == 'hadir' ? $request->lat : null,
            'long'    => $tipePresensi == 'hadir' ? $request->long : null,
            'status'  => $tipePresensi,
            'keterangan' => in_array($tipePresensi, ['izin', 'sakit']) ? $request->keterangan : null,
            'bukti_foto' => $buktiPath, // Tambahkan path bukti
        ]);

        return response()->json([
            'message' => 'Presensi berhasil',
            'data'    => $presensi
        ]);
    }


    public function index(Request $request)
    {
        $user    = $request->user();
        $riwayat = $user->presensis()->orderBy('waktu', 'desc')->get();

        return response()->json($riwayat);
    }

    public function show($id)
    {
        $user = auth()->user();
        $presensi = Presensi::findOrFail($id);

        // Pastikan yang mengakses adalah pemilik presensi atau admin
        if ($presensi->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Tidak diizinkan'], 403);
        }

        // Tambahkan URL bukti jika ada
        $presensi->bukti_url = $presensi->bukti_foto 
            ? url('storage/' . $presensi->bukti_foto) 
            : null;

        return response()->json($presensi);
    }

    public function riwayatPresensi(Request $request)
    {
        $user = $request->user();
        
        // Ambil riwayat presensi dengan informasi tambahan
        $riwayat = Presensi::where('user_id', $user->id)
            ->orderBy('waktu', 'desc')
            ->get()
            ->map(function($presensi) {
                // Tambahkan URL bukti jika ada
                $presensi->bukti_url = $presensi->bukti_foto 
                    ? url('storage/' . $presensi->bukti_foto) 
                    : null;
                return $presensi;
            });

        return response()->json($riwayat);
    }

    // Metode untuk menampilkan daftar presensi di admin
    public function adminIndex()
    {
        $presensis = Presensi::with('user')
            ->orderBy('waktu', 'desc')
            ->paginate(10);
        
        return view('admin.absen.index', compact('presensis'));
    }

    // Metode untuk menampilkan detail presensi
    public function adminShow($id)
    {
        $presensi = Presensi::with('user')->findOrFail($id);
        $presensi->bukti_url = $presensi->bukti_foto 
            ? url('storage/' . $presensi->bukti_foto) 
            : null;
        
        return view('admin.absen.detail', compact('presensi'));
    }

    // Metode untuk menampilkan form edit presensi
    public function adminEdit($id)
    {
        $presensi = Presensi::findOrFail($id);
        return view('admin.absen.edit', compact('presensi'));
    }

    // Metode untuk memproses update presensi
    public function adminUpdate(Request $request, $id)
    {
        $presensi = Presensi::findOrFail($id);

        $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alfa',
            'keterangan' => 'nullable|min:10|max:500',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        // Proses upload bukti baru jika ada
        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $fileName = time() . '_' . $presensi->user_id . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('bukti_presensi', $fileName, 'public');
            
            // Hapus file lama jika ada
            if ($presensi->bukti_foto) {
                \Storage::disk('public')->delete($presensi->bukti_foto);
            }

            $presensi->bukti_foto = $buktiPath;
        }

        $presensi->status = $request->status;
        $presensi->keterangan = $request->keterangan;
        $presensi->save();

        return redirect()->route('presensi.index')
            ->with('success', 'Presensi berhasil diperbarui');
    }

    // Metode untuk menghapus presensi
    public function adminDestroy($id)
    {
        $presensi = Presensi::findOrFail($id);

        // Hapus file bukti jika ada
        if ($presensi->bukti_foto) {
            \Storage::disk('public')->delete($presensi->bukti_foto);
        }

        $presensi->delete();

        return redirect()->route('presensi.index')
            ->with('success', 'Presensi berhasil dihapus');
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    // Metode untuk mengecek dan mencatat alpha
    public function checkAndRecordAlpha()
    {
        // Ambil pengaturan waktu presensi
        $settings = Setting::pluck('value', 'key')->all();
        $startTime = $settings['presensi_start_time'] ?? '07:00';
        $endTime = $settings['presensi_end_time'] ?? '09:00';

        // Tanggal hari ini
        $today = now()->toDateString();
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Waktu mulai dan akhir presensi
        $presensiStart = Carbon::createFromFormat('Y-m-d H:i', $today . ' ' . $startTime);
        $presensiEnd = Carbon::createFromFormat('Y-m-d H:i', $today . ' ' . $endTime);

        // Ambil semua siswa
        $users = User::where('role', 'siswa')->get();
        $alphaUsers = [];

        foreach ($users as $user) {
            // Cek apakah user sudah presensi hari ini
            $presensiHariIni = Presensi::where('user_id', $user->id)
                ->whereDate('waktu', $today)
                ->exists();

            // Jika belum presensi dan sudah melewati jam presensi, catat alpha
            if (!$presensiHariIni && now()->between($presensiStart, $presensiEnd)) {
                $presensi = Presensi::create([
                    'user_id' => $user->id,
                    'waktu' => now(),
                    'status' => 'alpha',
                    'keterangan' => 'Tidak melakukan presensi dalam waktu yang ditentukan'
                ]);

                $alphaUsers[] = [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ];
            }
        }

        return response()->json([
            'total_alpha_users' => count($alphaUsers),
            'alpha_users' => $alphaUsers,
            'presensi_start' => $presensiStart->format('H:i'),
            'presensi_end' => $presensiEnd->format('H:i')
        ]);
    }

    public function debugPresensiStatus()
    {
        $today = now()->toDateString();
        $users = User::where('role', 'siswa')->get();
        
        $userStatuses = [];
        
        foreach ($users as $user) {
            $presensiHariIni = Presensi::where('user_id', $user->id)
                ->whereDate('waktu', $today)
                ->first();
            
            $userStatuses[] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'presensi_hari_ini' => $presensiHariIni ? $presensiHariIni->status : 'Belum Presensi'
            ];
        }
        
        return response()->json($userStatuses);
    }

    public function print(Request $request)
    {
        // Ambil parameter filter
        $tanggal = $request->input('tanggal', now()->toDateString());
        $kelas = $request->input('kelas', null);

        // Query presensi
        $query = Presensi::whereDate('waktu', $tanggal)
            ->with('user');

        if ($kelas) {
            $query->whereHas('user', function($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        $presensis = $query->get();

        // Tampilkan view print
        return view('admin.absen.print', [
            'presensis' => $presensis,
            'tanggal' => $tanggal,
            'kelas' => $kelas
        ]);
    }

    public function getPresensiStats(Request $request)
    {
        // Tanggal hari ini
        $today = now()->toDateString();

        // Hitung statistik presensi
        $stats = [
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

        return response()->json([
            'status' => 'success',
            'data' => $stats,
            'tanggal' => $today
        ]);
    }
}
