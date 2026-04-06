<?php

namespace App\Http\Controllers\Web\Presensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Presensi;
use App\Models\Setting;
use Carbon\Carbon;

class AdminPresensiController extends Controller
{
    public function adminIndex()
    {
        $presensis = Presensi::with('user')
            ->orderBy('waktu', 'desc')
            ->paginate(10);
        
        return view('admin.absen.index', compact('presensis'));
    }

    public function adminShow($id)
    {
        $presensi = Presensi::with('user')->findOrFail($id);
        $presensi->bukti_url = $presensi->bukti_foto 
            ? url('storage/' . $presensi->bukti_foto) 
            : null;
        
        return view('admin.absen.detail', compact('presensi'));
    }

    public function adminEdit($id)
    {
        $presensi = Presensi::findOrFail($id);
        return view('admin.absen.edit', compact('presensi'));
    }

    public function adminUpdate(Request $request, $id)
    {
        $presensi = Presensi::findOrFail($id);

        $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alfa',
            'keterangan' => 'nullable|min:10|max:500',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $fileName = time() . '_' . $presensi->user_id . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('bukti_presensi', $fileName, 'public');
            
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

    public function adminDestroy($id)
    {
        $presensi = Presensi::findOrFail($id);

        if ($presensi->bukti_foto) {
            \Storage::disk('public')->delete($presensi->bukti_foto);
        }

        $presensi->delete();

        return redirect()->route('presensi.index')
            ->with('success', 'Presensi berhasil dihapus');
    }

    public function checkAndRecordAlpha()
    {
        $settings = Setting::pluck('value', 'key')->all();
        $startTime = $settings['presensi_start_time'] ?? '07:00';
        $endTime = $settings['presensi_end_time'] ?? '09:00';

        $today = now()->toDateString();
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        $presensiStart = Carbon::createFromFormat('Y-m-d H:i', $today . ' ' . $startTime);
        $presensiEnd = Carbon::createFromFormat('Y-m-d H:i', $today . ' ' . $endTime);

        $users = User::where('role', 'siswa')->get();
        $alphaUsers = [];

        foreach ($users as $user) {
            $presensiHariIni = Presensi::where('user_id', $user->id)
                ->whereDate('waktu', $today)
                ->exists();

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
        $tanggal = $request->input('tanggal', now()->toDateString());
        $kelas = $request->input('kelas', null);

        $query = Presensi::whereDate('waktu', $tanggal)
            ->with('user');

        if ($kelas) {
            $query->whereHas('user', function($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        $presensis = $query->get();

        return view('admin.absen.print', [
            'presensis' => $presensis,
            'tanggal' => $tanggal,
            'kelas' => $kelas
        ]);
    }

    public function getPresensiStats(Request $request)
    {
        $today = now()->toDateString();

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
