<?php

namespace App\Http\Controllers\Web\Presensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\User;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        $tanggal = $request->input('tanggal', now()->toDateString());
        $kelas = $request->input('kelas', '');
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        $query = Presensi::with('user')
            ->whereDate('waktu', $tanggal);

        if (!empty($kelas)) {
            $query->whereHas('user', function($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        if (!empty($search)) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $daftarKelas = User::whereNotNull('kelas')->distinct('kelas')->pluck('kelas');

        $presensis = $query->orderBy('waktu', 'desc')->paginate($perPage);

        $presensis->appends([
            'tanggal' => $tanggal,
            'kelas' => $kelas,
            'search' => $search,
            'per_page' => $perPage
        ]);

        return view('admin.absen.index', compact('presensis', 'tanggal', 'kelas', 'daftarKelas'));
    }

    public function show($id)
    {
        try {
            $presensi = Presensi::with('user')->findOrFail($id);
            
            $presensi->bukti_url = $presensi->bukti_foto 
                ? url('storage/' . $presensi->bukti_foto) 
                : null;
            
            return view('admin.absen.detail', compact('presensi'));
        } catch (\Exception $e) {
            \Log::error('Error in AbsenController show method: ' . $e->getMessage());
            return redirect()->route('admin.absen')
                ->with('error', 'Tidak dapat menemukan detail presensi');
        }
    }

    public function edit($id)
    {
        try {
            $presensi = Presensi::with('user')->findOrFail($id);
            return view('admin.absen.edit', compact('presensi'));
        } catch (\Exception $e) {
            \Log::error('Error in AbsenController edit method: ' . $e->getMessage());
            return redirect()->route('admin.absen')
                ->with('error', 'Tidak dapat menemukan data presensi untuk diedit');
        }
    }

    public function update(Request $request, $id)
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

        return redirect()->route('admin.absen')
            ->with('success', 'Presensi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);

        if ($presensi->bukti_foto) {
            \Storage::disk('public')->delete($presensi->bukti_foto);
        }

        $presensi->delete();

        return redirect()->route('admin.absen')
            ->with('success', 'Presensi berhasil dihapus');
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

        if ($request->has('status') && !$request->has('user_id')) {
            $presensi->update([
                'status' => $request->status,
            ]);
            return back()->with('success', 'Status presensi berhasil diupdate.');
        }

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
