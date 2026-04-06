<?php

namespace App\Http\Controllers\Web\User;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Enums\UserRole;

class AdminUserController extends Controller
{

    public function index(Request $request)
    {
        // Query dasar untuk user
        $query = User::query();

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas') && $request->input('kelas') !== '') {
            $query->where('kelas', $request->input('kelas'));
        }

        // Filter berdasarkan tanggal (jika diperlukan)
        if ($request->has('tanggal')) {
            $tanggal = $request->input('tanggal');
            $query->whereHas('presensis', function($q) use ($tanggal) {
                $q->whereDate('waktu', $tanggal);
            });
        }

        // Urutkan berdasarkan tanggal dibuat secara descending (terbaru di atas)
        $query->orderBy('created_at', 'desc');

        // Tentukan jumlah baris per halaman
        $perPage = $request->input('per_page', 10);

        // Ambil data dengan pagination
        $users = $query->paginate($perPage);

        // Debug daftar kelas yang tersedia
        $availableClasses = User::where('role', 'siswa')
            ->distinct()
            ->pluck('kelas')
            ->toArray();

        \Log::info('Available Classes Debug', [
            'all_classes' => $availableClasses,
            'selected_class' => $request->input('kelas'),
            'filter_applied' => $request->has('kelas') && $request->input('kelas') !== ''
        ]);

        // Kembalikan view dengan data
        return view('admin.users', [
            'users' => $users,
            'tanggal' => $request->input('tanggal', now()->toDateString()),
            'kelas' => $request->input('kelas', '')
        ]);
    }

    public function create()
    {
        return view('admin.create_user');
    }

    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'nisn' => 'required|unique:users,nisn',
            'role' => [
                'required', 
                'in:' . implode(',', UserRole::values())
            ],
            
            // Field baru
            'nis' => 'nullable|integer|unique:users,nis',
            'kelas' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:laki_laki,perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|in:islam,kristen,katholik,hindu,buddha,konghucu',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|numeric',
            'pas_foto' => 'nullable|image|max:2048', // Maks 2MB
        ]);

        // Proses upload foto
        $fotoPath = null;
        if ($request->hasFile('pas_foto')) {
            $foto = $request->file('pas_foto');
            $fotoPath = $foto->store('pas_foto', 'public');
            
            // Logging informasi foto
            \Log::info('Foto Uploaded: ', [
                'original_name' => $foto->getClientOriginalName(),
                'mime_type' => $foto->getMimeType(),
                'size' => $foto->getSize(),
                'stored_path' => $fotoPath
            ]);
        }

        // Buat user baru
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'nisn' => $validatedData['nisn'],
            'role' => $validatedData['role'],
            
            // Field baru
            'nis' => $validatedData['nis'] ?? null,
            'kelas' => $validatedData['kelas'] ?? null,
            'jenis_kelamin' => $validatedData['jenis_kelamin'] ?? null,
            'tempat_lahir' => $validatedData['tempat_lahir'] ?? null,
            'tanggal_lahir' => $validatedData['tanggal_lahir'] ?? null,
            'agama' => $validatedData['agama'] ?? null,
            'alamat' => $validatedData['alamat'] ?? null,
            'no_telp' => $validatedData['no_telp'] ?? null,
            'pas_foto' => $fotoPath,
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit_user', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 
                'email', 
                Rule::unique('users')->ignore($user->id)
            ],
            'password' => 'nullable|min:6',
            'nisn' => [
                'required', 
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => [
                'required', 
                'in:' . implode(',', UserRole::values())
            ],
            
            // Field baru
            'nis' => [
                'nullable', 
                'integer', 
                Rule::unique('users')->ignore($user->id)
            ],
            'kelas' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:laki_laki,perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|in:islam,kristen,katholik,hindu,buddha,konghucu',
            'alamat' => 'nullable|string',
            'no_telp' => 'nullable|numeric',
            'pas_foto' => 'nullable|image|max:2048', // Maks 2MB
        ]);

        // Proses upload foto
        if ($request->hasFile('pas_foto')) {
            // Hapus foto lama jika ada
            if ($user->pas_foto) {
                Storage::disk('public')->delete($user->pas_foto);
            }
            
            // Simpan foto baru
            $foto = $request->file('pas_foto');
            $fotoPath = $foto->store('pas_foto', 'public');
            
            // Logging informasi foto
            \Log::info('Foto Uploaded: ', [
                'original_name' => $foto->getClientOriginalName(),
                'mime_type' => $foto->getMimeType(),
                'size' => $foto->getSize(),
                'stored_path' => $fotoPath
            ]);
            $validatedData['pas_foto'] = $fotoPath;
        }

        // Update password hanya jika diisi
        if (!empty($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            unset($validatedData['password']);
        }

        // Update user
        $user->update($validatedData);

        return redirect()->route('admin.users')->with('success', 'User berhasil diupdate');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Hapus foto profil jika ada
        if ($user->pas_foto) {
            Storage::disk('public')->delete($user->pas_foto);
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus');
    }

    public function debugFoto($user)
    {
        // Informasi debugging lengkap
        \Log::info('Foto Debug untuk User: ' . $user->name, [
            'pas_foto_path' => $user->pas_foto ?? 'Tidak ada foto',
            'storage_path' => $user->pas_foto ? storage_path('app/public/' . $user->pas_foto) : 'N/A',
            'public_path' => $user->pas_foto ? public_path('storage/' . $user->pas_foto) : 'N/A',
            'storage_exists' => $user->pas_foto ? \Storage::disk('public')->exists($user->pas_foto) : false,
            'file_exists' => $user->pas_foto ? file_exists(storage_path('app/public/' . $user->pas_foto)) : false,
            'storage_url' => $user->pas_foto ? \Storage::url($user->pas_foto) : 'N/A',
            'asset_url' => $user->pas_foto ? asset('storage/' . $user->pas_foto) : 'N/A',
        ]);
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        
        // Tambahkan debug foto
        $this->debugFoto($user);
        
        return view('admin.users.detail', compact('user'));
    }
}
