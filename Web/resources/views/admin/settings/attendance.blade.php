@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">
                <i class="bi bi-stopwatch-fill text-success me-2"></i>
                Konfigurasi Waktu Presensi
            </h2>
            <p class="text-muted small mb-0">Atur jadwal check-in, check-out, dan toleransi keterlambatan secara global.</p>
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm d-flex align-items-center" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.update_attendance_settings') }}" method="POST">
        @csrf

        <div class="row g-4">

            {{-- Check-In Card --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                <i class="bi bi-box-arrow-in-right fs-4 text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Batas Jam Masuk</h6>
                                <small class="text-muted">Check-In Deadline</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <input
                                type="time"
                                name="check_in_end"
                                value="{{ $checkInEnd }}"
                                class="form-control form-control-lg fw-bold text-success fs-4 text-center border-success"
                                required
                            >
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Lewat dari waktu ini (+ toleransi), sistem menolak absensi masuk.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tolerance Card --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                                <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Toleransi Terlambat</h6>
                                <small class="text-muted">Dalam satuan menit</small>
                            </div>
                        </div>
                        <div class="input-group mb-2">
                            <input
                                type="number"
                                name="late_tolerance"
                                value="{{ $lateTolerance }}"
                                min="0"
                                max="60"
                                class="form-control form-control-lg fw-bold text-warning fs-4 text-center border-warning"
                                required
                            >
                            <span class="input-group-text fw-bold text-warning border-warning bg-warning bg-opacity-10">menit</span>
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Kehadiran dalam rentang ini dicatat sebagai <strong>Terlambat</strong>. Lewat dari itu = Alfa.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Check-Out Card --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                                <i class="bi bi-box-arrow-right fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">Jam Absen Pulang</h6>
                                <small class="text-muted">Check-Out Start</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            <input
                                type="time"
                                name="check_out_start"
                                value="{{ $checkOutStart }}"
                                class="form-control form-control-lg fw-bold text-primary fs-4 text-center border-primary"
                                required
                            >
                        </div>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Tombol <strong>Absen Pulang</strong> pada aplikasi mobile baru muncul setelah jam ini tercapai.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Info Summary Row --}}
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-diagram-3 me-2 text-secondary"></i>Simulasi Alur Berdasarkan Konfigurasi Saat Ini</h6>
                <div class="row text-center g-3">
                    <div class="col">
                        <div class="p-3 bg-success bg-opacity-10 rounded-3">
                            <i class="bi bi-clock-history fs-4 text-success d-block mb-1"></i>
                            <small class="text-muted">Sebelum <strong>{{ $checkInEnd }}</strong></small>
                            <p class="fw-bold text-success mb-0 small">✅ Tepat Waktu</p>
                        </div>
                    </div>
                    <div class="col-auto d-flex align-items-center text-muted">→</div>
                    <div class="col">
                        @php
                            $parts = explode(':', $checkInEnd);
                            $lateH = (int)$parts[0];
                            $lateM = (int)$parts[1] + (int)$lateTolerance;
                            if ($lateM >= 60) { $lateH++; $lateM -= 60; }
                            $lateStr = sprintf('%02d:%02d', $lateH, $lateM);
                        @endphp
                        <div class="p-3 bg-warning bg-opacity-10 rounded-3">
                            <i class="bi bi-clock fs-4 text-warning d-block mb-1"></i>
                            <small class="text-muted"><strong>{{ $checkInEnd }}</strong> s/d <strong>{{ $lateStr }}</strong></small>
                            <p class="fw-bold text-warning mb-0 small">⚠️ Terlambat</p>
                        </div>
                    </div>
                    <div class="col-auto d-flex align-items-center text-muted">→</div>
                    <div class="col">
                        <div class="p-3 bg-danger bg-opacity-10 rounded-3">
                            <i class="bi bi-x-circle fs-4 text-danger d-block mb-1"></i>
                            <small class="text-muted">Setelah <strong>{{ $lateStr }}</strong></small>
                            <p class="fw-bold text-danger mb-0 small">❌ Ditolak (Alfa)</p>
                        </div>
                    </div>
                    <div class="col-auto d-flex align-items-center text-muted">→</div>
                    <div class="col">
                        <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                            <i class="bi bi-door-open fs-4 text-primary d-block mb-1"></i>
                            <small class="text-muted">Buka Pulang <strong>{{ $checkOutStart }}</strong></small>
                            <p class="fw-bold text-primary mb-0 small">🚪 Absen Pulang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-success px-5 py-2 shadow-sm fw-bold">
                <i class="bi bi-floppy-fill me-2"></i> Simpan Konfigurasi
            </button>
        </div>

    </form>
</div>
@endsection
