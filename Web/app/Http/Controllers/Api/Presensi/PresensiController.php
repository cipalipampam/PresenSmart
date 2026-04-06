<?php

namespace App\Http\Controllers\Api\Presensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PresensiController extends Controller
{
    protected $schoolLat;
    protected $schoolLong;
    protected $radius;

    public function __construct()
    {
        $settings = Setting::pluck('value', 'key')->all();

        $this->schoolLat  = isset($settings['school_lat'])   ? (float) $settings['school_lat']   : null;
        $this->schoolLong = isset($settings['school_long'])  ? (float) $settings['school_long']  : null;
        $this->radius     = isset($settings['school_radius'])? (int)   $settings['school_radius'] : null;
    }

    public function store(Request $request)
    {
        $tipePresensi = $request->input('tipe_presensi', 'hadir');

        if ($tipePresensi == 'hadir') {
            $request->validate([
                'lat'  => 'required|numeric',
                'long' => 'required|numeric',
            ]);

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
                'keterangan' => 'required|min:10|max:500',
                'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);
        } else {
            return response()->json(['error' => 'Tipe presensi tidak valid'], 400);
        }

        $user = $request->user();
        $today = now()->toDateString();
        $alreadyPresent = $user->presensis()->whereDate('waktu', $today)->exists();

        if ($alreadyPresent) {
            return response()->json(['error' => 'Anda sudah melakukan presensi hari ini'], 403);
        }

        $buktiPath = null;
        if ($request->hasFile('bukti') && in_array($tipePresensi, ['izin', 'sakit'])) {
            $file = $request->file('bukti');
            $fileName = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('bukti_presensi', $fileName, 'public');
        }

        $presensi = Presensi::create([
            'user_id' => $user->id,
            'waktu'   => now(),
            'lat'     => $tipePresensi == 'hadir' ? $request->lat : null,
            'long'    => $tipePresensi == 'hadir' ? $request->long : null,
            'status'  => $tipePresensi,
            'keterangan' => in_array($tipePresensi, ['izin', 'sakit']) ? $request->keterangan : null,
            'bukti_foto' => $buktiPath,
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

        if ($presensi->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Tidak diizinkan'], 403);
        }

        $presensi->bukti_url = $presensi->bukti_foto 
            ? url('storage/' . $presensi->bukti_foto) 
            : null;

        return response()->json($presensi);
    }

    public function riwayatPresensi(Request $request)
    {
        $user = $request->user();
        
        $riwayat = Presensi::where('user_id', $user->id)
            ->orderBy('waktu', 'desc')
            ->get()
            ->map(function($presensi) {
                $presensi->bukti_url = $presensi->bukti_foto 
                    ? url('storage/' . $presensi->bukti_foto) 
                    : null;
                return $presensi;
            });

        return response()->json($riwayat);
    }

    private function haversine($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2)**2;
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }
}
