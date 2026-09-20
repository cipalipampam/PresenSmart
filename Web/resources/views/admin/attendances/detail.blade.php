@extends('admin.layouts.app')

@section('content')
@php
    $isPdf = \Illuminate\Support\Str::endsWith(strtolower($attendance->proof_image ?? ''), '.pdf');
    $isSick = $attendance->status === 'sick';
    $isPermission = $attendance->status === 'permission';
@endphp
<div class="py-2">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-house-door me-1"></i>Dashboard
                </a>
                <span class="text-muted small">/</span>
                <a href="{{ route(match ($scope) {
                    'siswa' => 'admin.attendances.students',
                    'employee' => 'admin.attendances.employees',
                    default => 'admin.attendances.index',
                }) }}" class="text-decoration-none text-muted small">
                    Presensi
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Detail</span>
            </div>
            <h1 class="fw-bold text-dark mb-1" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                Detail Presensi Kehadiran
            </h1>
            <p class="text-muted small mb-0">Rincian log presensi lengkap untuk <strong class="text-dark">{{ $attendance->user->name }}</strong>.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route(match ($scope) {
                'siswa' => 'admin.attendances.students',
                'employee' => 'admin.attendances.employees',
                default => 'admin.attendances.index',
            }) }}" class="btn btn-outline-secondary py-2 px-3 me-2" style="font-size: 0.85rem;">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
            </a>
            <a href="{{ route('admin.attendances.edit', array_merge(['id' => $attendance->id], $scope ? ['scope' => $scope] : [])) }}"
               class="btn btn-primary py-2 px-3" style="font-size: 0.85rem;">
                <i class="bi bi-pencil me-1"></i>Edit Presensi
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4" role="alert" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #059669 !important;">
            <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        {{-- ===== KOLOM KIRI: DETAIL DATA & MAPS ===== --}}
        <div class="col-lg-7">

            {{-- Card 1: Informasi Kehadiran --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 text-dark fw-bold">
                        <i class="bi bi-person-badge text-primary me-2"></i>Informasi Data Kehadiran
                    </h6>
                    @if($attendance->status == 'present')
                        @if($attendance->is_late)
                            <span class="badge-status badge-late"><i class="bi bi-clock-history"></i>Terlambat</span>
                        @else
                            <span class="badge-status badge-present"><i class="bi bi-check2"></i>Hadir Tepat Waktu</span>
                        @endif
                    @elseif($attendance->status == 'permission')
                        <span class="badge-status badge-permission">
                            <i class="bi bi-file-text"></i>Izin —
                            {{ $attendance->is_approved === null ? 'Menunggu Review' : ($attendance->is_approved ? 'Disetujui' : 'Ditolak') }}
                        </span>
                    @elseif($attendance->status == 'sick')
                        <span class="badge-status badge-sick">
                            <i class="bi bi-bandaid"></i>Sakit —
                            {{ $attendance->is_approved === null ? 'Menunggu Review' : ($attendance->is_approved ? 'Disetujui' : 'Ditolak') }}
                        </span>
                    @else
                        <span class="badge-status badge-absent"><i class="bi bi-x-circle"></i>Alfa (Tidak Hadir)</span>
                    @endif
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Nama Pengguna</label>
                            <div class="p-3 rounded-3 bg-light border text-dark fw-medium">
                                {{ $attendance->user->name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Peran / Kelas</label>
                            <div class="p-3 rounded-3 bg-light border text-dark fw-medium">
                                @if($attendance->user->student)
                                    Siswa — Kelas {{ $attendance->user->student->class_name }}
                                @elseif($attendance->user->employee)
                                    Pegawai — {{ $attendance->user->employee->position ?? 'Staff' }}
                                @else
                                    Administrator
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Waktu Masuk (Check-In)</label>
                            <div class="p-3 rounded-3 bg-light border text-dark fw-medium">
                                <i class="bi bi-box-arrow-in-right text-success me-1"></i>
                                {{ \Carbon\Carbon::parse($attendance->recorded_at)->translatedFormat('l, d F Y - H:i') }} WIB
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Waktu Pulang (Check-Out)</label>
                            <div class="p-3 rounded-3 bg-light border text-dark fw-medium">
                                <i class="bi bi-box-arrow-left text-danger me-1"></i>
                                {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->translatedFormat('l, d F Y - H:i') . ' WIB' : 'Belum absen pulang' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Koordinat GPS Geofence</label>
                            <div class="p-3 rounded-3 bg-light border">
                                @if($attendance->latitude && $attendance->longitude)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $attendance->latitude }},{{ $attendance->longitude }}"
                                       target="_blank" class="text-decoration-none text-primary fw-medium">
                                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $attendance->latitude }}, {{ $attendance->longitude }}
                                        <i class="bi bi-box-arrow-up-right ms-1 small"></i>
                                    </a>
                                @else
                                    <span class="text-muted">Koordinat lokasi tidak terekam</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-semibold">Status Keterlambatan</label>
                            <div class="p-3 rounded-3 bg-light border text-dark fw-medium">
                                @if($attendance->is_late)
                                    <span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i>Tercatat Terlambat</span>
                                @else
                                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Tepat Waktu</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-muted small fw-semibold">Catatan / Alasan Kehadiran</label>
                            <div class="p-3 rounded-3 bg-light border text-dark">
                                {{ $attendance->notes ?? 'Tidak ada catatan khusus yang dilampirkan.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Google Maps Embed (Jika ada koordinat GPS) --}}
            @if($attendance->latitude && $attendance->longitude)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 text-dark fw-bold">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Titik Lokasi Presensi pada Peta
                    </h6>
                </div>
                <div class="card-body p-0 overflow-hidden" style="border-radius: 0 0 16px 16px;">
                    <iframe
                        src="https://www.google.com/maps?q={{ $attendance->latitude }},{{ $attendance->longitude }}&z=17&output=embed"
                        width="100%"
                        height="260"
                        style="border:0;display:block;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </div>
            @endif

        </div>

        {{-- ===== KOLOM KANAN: LAMPIRAN SURAT SAKIT & AKSI APPROVAL ===== --}}
        <div class="col-lg-5">

            {{-- Section 1: Kotak Aksi Approval jika Menunggu --}}
            @if(in_array($attendance->status, ['permission', 'sick']) && $attendance->is_approved === null)
            <div class="card border-0 shadow-sm mb-4" style="background: #fffbeb; border: 1.5px solid #fde68a !important;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-exclamation-circle-fill text-warning fs-5"></i>
                        <h6 class="fw-bold text-dark mb-0">Menunggu Tindakan Admin</h6>
                    </div>
                    <p class="text-secondary small mb-3">
                        Tinjau dokumen bukti / surat sakit di bawah sebelum memutuskan untuk menyetujui atau menolak permohonan ini.
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="grow">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="btn btn-success w-100 py-2 fw-semibold" onclick="return confirm('Setujui permohonan ini?')">
                                <i class="bi bi-check-lg me-1"></i>Setujui
                            </button>
                        </form>
                        <form action="{{ route('admin.attendances.approve', $attendance->id) }}" method="POST" class="grow">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-outline-danger w-100 py-2 fw-semibold" onclick="return confirm('Tolak permohonan ini? Status pengguna akan otomatis tercatat Alfa.')">
                                <i class="bi bi-x-lg me-1"></i>Tolak (Alfa)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Section 2: DOKUMEN SURAT SAKIT / BUKTI LAMPIRAN --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">
                            @if($isSick)
                                <i class="bi bi-file-earmark-medical-fill text-danger me-2"></i>Surat Keterangan Sakit / Dokter
                            @elseif($isPermission)
                                <i class="bi bi-file-earmark-text-fill text-primary me-2"></i>Dokumen Surat Izin
                            @else
                                <i class="bi bi-file-earmark-image-fill text-primary me-2"></i>Dokumen / Lampiran Bukti
                            @endif
                        </h6>
                    </div>
                    @if($attendance->proof_image)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-check-circle me-1"></i>Dokumen Terlampir
                        </span>
                    @else
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 0.72rem;">
                            <i class="bi bi-x-circle me-1"></i>Tidak Ada Berkas
                        </span>
                    @endif
                </div>

                <div class="card-body p-4 text-center">
                    @if($attendance->proof_image)
                        {{-- Jika ada bukti surat --}}
                        <div class="mb-3 text-start">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light border mb-3">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <i class="bi {{ $isPdf ? 'bi-file-earmark-pdf-fill text-danger' : 'bi-file-earmark-image-fill text-primary' }} fs-4"></i>
                                    <div class="overflow-hidden">
                                        <div class="fw-semibold text-dark text-truncate small">
                                            {{ $isSick ? 'Surat Dokter' : 'Surat Izin' }} — {{ $attendance->user->name }}
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            Format: {{ strtoupper(pathinfo($attendance->proof_image, PATHINFO_EXTENSION)) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    <a href="{{ $attendance->proof_url }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.78rem;">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Tampilan Preview Foto Surat Sakit / PDF --}}
                        <div class="position-relative p-2 rounded-3 bg-light border text-center">
                            @if($isPdf)
                                <div class="py-5">
                                    <i class="bi bi-file-earmark-pdf-fill text-danger display-3 mb-2 d-block"></i>
                                    <h6 class="fw-bold text-dark mb-1">Dokumen Surat Format PDF</h6>
                                    <p class="text-muted small mb-3">Klik tombol di bawah untuk membaca atau mencetak berkas surat.</p>
                                    <a href="{{ $attendance->proof_url }}" target="_blank" class="btn btn-primary px-4 py-2">
                                        <i class="bi bi-file-earmark-arrow-down me-1"></i>Buka Dokumen PDF
                                    </a>
                                </div>
                            @else
                                <a href="{{ $attendance->proof_url }}" target="_blank" title="Klik untuk memperbesar tampilan">
                                    <img src="{{ $attendance->proof_url }}"
                                         alt="Surat Sakit / Bukti Kehadiran"
                                         class="img-fluid rounded-2 shadow-sm border bg-white"
                                         style="max-height: 420px; width: 100%; object-fit: contain;">
                                </a>
                                <p class="text-muted small mt-2 mb-0" style="font-size: 0.75rem;">
                                    <i class="bi bi-zoom-in me-1"></i>Klik gambar di atas untuk melihat resolusi penuh
                                </p>
                            @endif
                        </div>

                    @else
                        {{-- Jika TIDAK ADA bukti surat --}}
                        <div class="py-4 text-center">
                            <div class="rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: #fff1f2; color: #e11d48;">
                                <i class="bi bi-file-earmark-x fs-2"></i>
                            </div>
                            @if($isSick)
                                <h6 class="fw-bold text-danger mb-1">Surat Sakit Belum Dilampirkan!</h6>
                                <p class="text-muted small mb-3 px-3">
                                    Pengguna berstatus Sakit ini belum melampirkan foto surat dokter atau instansi kesehatan yang sah.
                                </p>
                            @else
                                <h6 class="fw-bold text-dark mb-1">Tidak Ada Lampiran Berkas</h6>
                                <p class="text-muted small mb-3 px-3">
                                    Presensi ini tidak disertai dokumen bukti atau surat izin pendukung.
                                </p>
                            @endif
                            <a href="{{ route('admin.attendances.edit', array_merge(['id' => $attendance->id], $scope ? ['scope' => $scope] : [])) }}"
                               class="btn btn-sm btn-outline-primary px-3">
                                <i class="bi bi-cloud-upload me-1"></i>Unggah Surat Sekarang
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Section 3: Informasi Audit --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between text-muted small" style="font-size: 0.78rem;">
                        <span>Dibuat pada: {{ \Carbon\Carbon::parse($attendance->created_at)->format('d/m/Y H:i') }}</span>
                        <span>ID Rekaman: #{{ $attendance->id }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
