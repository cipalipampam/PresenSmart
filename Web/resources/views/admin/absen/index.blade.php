@extends('admin.layout')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Absen</h2>
        <div>
            <a href="{{ route('admin.absen.print') }}" class="btn btn-primary" target="_blank">
                <i class="bi bi-printer me-1"></i>Cetak Laporan
            </a>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <form method="get" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Cari nama siswa" 
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
        </div>
        <div class="col-md-6 text-end">
            <form method="get" class="d-flex justify-content-end">
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control me-2" style="width: 180px;">
                <select name="kelas" class="form-select me-2" style="width: 180px;">
                    <option value="">Semua Kelas</option>
                    @foreach($daftarKelas as $k)
                        <option value="{{ $k }}" {{ $kelas == $k ? 'selected' : '' }}>{{ $k }}</option>
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>NISN</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($presensis as $index => $p)
                    <tr>
                        <td>{{ ($presensis->currentPage() - 1) * $presensis->perPage() + $loop->iteration }}</td>
                        <td>{{ $p->user->name }}</td>
                        <td>{{ $p->user->nisn }}</td>
                        <td>{{ $p->user->kelas ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $statusClass = [
                                    'hadir' => 'success',
                                    'izin' => 'warning',
                                    'sakit' => 'info',
                                    'alpha' => 'danger'
                                ];
                            @endphp
                            <span class="badge text-white bg-{{ $statusClass[$p->status] ?? 'secondary' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-muted">
                                {{ \Carbon\Carbon::parse($p->waktu)->format('H:i:s') }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.absen.show', $p->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.absen.edit', $p->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.absen.destroy', $p->id) }}" method="POST" class="d-inline" 
                                      onsubmit="return confirm('Yakin ingin menghapus data presensi?');">
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
                Menampilkan {{ $presensis->firstItem() }} - {{ $presensis->lastItem() }} dari {{ $presensis->total() }} data
            </div>
            <div>
                {{ $presensis->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
