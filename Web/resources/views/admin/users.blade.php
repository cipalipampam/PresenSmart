@extends('admin.layout')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Pengguna</h2>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Tambah User Baru
        </a>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <form action="{{ route('admin.users') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari nama atau NISN" 
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <form action="{{ route('admin.users') }}" method="GET" class="d-flex justify-content-end">
                <input type="date" name="tanggal" value="{{ request('tanggal', now()->toDateString()) }}" class="form-control me-2" style="width: 180px;">
                <select name="kelas" class="form-select me-2" style="width: 180px;">
                    <option value="">Semua Kelas</option>
                    @php
                        $daftarKelas = \App\Models\User::where('role', 'siswa')
                            ->distinct()
                            ->orderBy('kelas')
                            ->pluck('kelas')
                            ->filter() // Hapus null/empty values
                            ->values()
                            ->toArray();
                    @endphp
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                    @endforeach
                </select>
                <select name="per_page" class="form-select w-auto me-2" onchange="this.form.submit()">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 Baris</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 Baris</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 Baris</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 Baris</option>
                </select>
                <button class="btn btn-primary">Filter</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Pas Foto</th>
                    <th>Nama</th>
                    <th>NISN</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Jenis Kelamin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $index => $user)
                    <tr>
                        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td>
                            @if($user->pas_foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->pas_foto))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($user->pas_foto) }}" 
                                     alt="Pas Foto" 
                                     style="max-width: 50px; max-height: 50px; border-radius: 50%;">
                            @else
                                <span class="text-muted">Tidak ada foto</span>
                            @endif
                        </td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->nisn }}</td>
                        <td>{{ $user->nis ?? '-' }}</td>
                        <td>{{ $user->kelas ?? '-' }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">
                            <span class="badge {{ $user->role == 'admin' ? 'bg-success' : 'bg-primary' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->jenis_kelamin == 'laki_laki')
                                <span class="badge bg-primary">Laki-laki</span>
                            @elseif($user->jenis_kelamin == 'perempuan')
                                <span class="badge bg-pink">Perempuan</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-between align-items-center">
            <div>
                Menampilkan {{ $users->firstItem() }} - {{ $users->lastItem() }} dari {{ $users->total() }} pengguna
            </div>
            <div>
                {{ $users->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
