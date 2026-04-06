@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h1 class="h2 fw-bold text-primary mb-2">Dashboard Admin</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 m-0">
                    <li class="breadcrumb-item"><a href="#" class="text-muted">Home</a></li>
                    <li class="breadcrumb-item active text-dark" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-inline-block bg-light rounded p-2">
                <i class="bi bi-calendar-check me-2 text-primary"></i>
                <span class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        {{-- Statistik Utama --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm hover-lift">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-primary-soft me-3">
                        <i class="bi bi-people-fill text-primary fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Total Pengguna</h6>
                        <div class="d-flex align-items-baseline">
                            <h2 class="h3 mb-0 me-2">{{ $userCount }}</h2>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> 5%
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm hover-lift">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-success-soft me-3">
                        <i class="bi bi-calendar-check-fill text-success fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Presensi Hari Ini</h6>
                        <div class="d-flex align-items-baseline">
                            <h2 class="h3 mb-0 me-2">{{ $todayPresensiCount }}</h2>
                            <small class="text-success">
                                <i class="bi bi-graph-up"></i> Aktif
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm hover-lift">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-warning-soft me-3">
                        <i class="bi bi-geo-alt-fill text-warning fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-uppercase text-muted mb-1">Lokasi Sekolah</h6>
                        <div class="d-flex align-items-baseline">
                            <h6 class="mb-0 text-dark">
                                {{ $setting['school_lat'] }}, {{ $setting['school_long'] }}
                            </h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="card-title text-primary mb-3">
                        <i class="bi bi-list-check me-2"></i>Detail Presensi Hari Ini
                    </h5>
                </div>
                <div class="card-body pt-2">
                    @if ($todayPresensi->isEmpty())
                        <div class="alert alert-info text-center" role="alert">
                            <i class="bi bi-info-circle me-2"></i>Belum ada presensi hari ini
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama Siswa</th>
                                        <th>Status</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($todayPresensi as $presensi)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-circle me-2 text-primary"></i>
                                                    {{ $presensi->user->name }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    @if($presensi->status == 'hadir') bg-success
                                                    @elseif($presensi->status == 'izin') bg-warning
                                                    @elseif($presensi->status == 'sakit') bg-info
                                                    @else bg-danger
                                                    @endif">
                                                    {{ ucfirst($presensi->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($presensi->waktu)->format('H:i') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                Menampilkan {{ $todayPresensi->firstItem() }} - {{ $todayPresensi->lastItem() }} 
                                dari {{ $todayPresensi->total() }} data
                            </div>
                            <div>
                                {{ $todayPresensi->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="card-title text-primary mb-3">
                        <i class="bi bi-bar-chart me-2"></i>Ringkasan Presensi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Hadir</span>
                        <strong>{{ $todayPresensi->where('status', 'hadir')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Izin</span>
                        <strong>{{ $todayPresensi->where('status', 'izin')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sakit</span>
                        <strong>{{ $todayPresensi->where('status', 'sakit')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Alpha</span>
                        <strong>{{ $todayPresensi->where('status', 'alpha')->count() }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
    }
    .bg-primary-soft { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-soft { background-color: rgba(25, 135, 84, 0.1); }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .hover-lift {
        transition: transform 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-10px);
    }
</style>
@endpush
@endsection
