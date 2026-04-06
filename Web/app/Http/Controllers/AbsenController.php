<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\User;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        // Tanggal default hari ini
        $tanggal = $request->input('tanggal', now()->toDateString());
        $kelas = $request->input('kelas', '');
        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        // Query untuk presensi
        $query = Presensi::with('user')
            ->whereDate('waktu', $tanggal);

        // Filter berdasarkan kelas jika dipilih
        if (!empty($kelas)) {
            $query->whereHas('user', function($q) use ($kelas) {
                $q->where('kelas', $kelas);
            });
        }

        // Pencarian berdasarkan nama
        if (!empty($search)) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Ambil daftar kelas unik
        $daftarKelas = User::whereNotNull('kelas')->distinct('kelas')->pluck('kelas');

        // Pagination
        $presensis = $query->orderBy('waktu', 'desc')->paginate($perPage);

        // Tambahkan parameter query string
        $presensis->appends([
            'tanggal' => $tanggal,
            'kelas' => $kelas,
            'search' => $search,
            'per_page' => $perPage
        ]);

        return view('admin.absen.index', compact('presensis', 'tanggal', 'kelas', 'daftarKelas'));
    }

    // Metode untuk menampilkan detail presensi
    public function show($id)
    {
        try {
            $presensi = Presensi::with('user')->findOrFail($id);
            
            // Tambahkan URL bukti jika ada
            $presensi->bukti_url = $presensi->bukti_foto 
                ? url('storage/' . $presensi->bukti_foto) 
                : null;
            
            // Pastikan view yang benar
            return view('admin.absen.detail', compact('presensi'));
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error in AbsenController show method: ' . $e->getMessage());
            
            // Redirect dengan pesan error
            return redirect()->route('admin.absen')
                ->with('error', 'Tidak dapat menemukan detail presensi');
        }
    }

    // Metode untuk menampilkan form edit presensi
    public function edit($id)
    {
        try {
            $presensi = Presensi::with('user')->findOrFail($id);
            return view('admin.absen.edit', compact('presensi'));
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Error in AbsenController edit method: ' . $e->getMessage());
            
            // Redirect dengan pesan error
            return redirect()->route('admin.absen')
                ->with('error', 'Tidak dapat menemukan data presensi untuk diedit');
        }
    }

    // Metode untuk memproses update presensi
    public function update(Request $request, $id)
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

        return redirect()->route('admin.absen')
            ->with('success', 'Presensi berhasil diperbarui');
    }

    // Metode untuk menghapus presensi
    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);

        // Hapus file bukti jika ada
        if ($presensi->bukti_foto) {
            \Storage::disk('public')->delete($presensi->bukti_foto);
        }

        $presensi->delete();

        return redirect()->route('admin.absen')
            ->with('success', 'Presensi berhasil dihapus');
    }
}