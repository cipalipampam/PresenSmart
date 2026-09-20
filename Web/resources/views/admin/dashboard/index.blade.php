@extends('admin.layouts.app')

@section('content')
<div class="py-2">

    {{-- ===== FLASH MESSAGES ===== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-4" role="alert" style="background-color: #ecfdf5; color: #065f46; border-left: 4px solid #059669 !important;">
            <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
            <div class="grow">
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center mb-4" role="alert" style="background-color: #fff1f2; color: #9f1239; border-left: 4px solid #e11d48 !important;">
            <i class="bi bi-exclamation-octagon-fill fs-5 me-2 text-danger"></i>
            <div class="grow">
                <strong>Perhatian:</strong> {{ session('error') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row mb-4 align-items-center">
        <div class="col-lg-7">
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge px-2 py-1 text-primary fw-semibold" style="background-color: #eff6ff; border: 1px solid #bfdbfe; font-size: 0.75rem;">
                    <i class="bi bi-building me-1"></i>{{ $setting['school_name'] ?? 'PresenSmart School' }}
                </span>
                <span class="text-muted small">•</span>
                <span class="text-muted small">Tahun Ajaran {{ date('Y') }}/{{ date('Y') + 1 }}</span>
            </div>
            <h1 class="fw-bold text-dark mb-1" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                Dashboard Overview
            </h1>
            <p class="text-muted small mb-0">
                Selamat datang kembali, <strong class="text-dark">{{ Auth::user()->name ?? 'Administrator' }}</strong>. Pantau presensi, geofence, dan persetujuan secara real-time.
            </p>
        </div>
        <div class="col-lg-5 text-lg-end mt-3 mt-lg-0">
            <div class="d-inline-flex flex-wrap align-items-center gap-2">
                {{-- Date & Live Clock --}}
                <div class="d-inline-flex align-items-center bg-white px-3 py-2 rounded-3 border shadow-sm">
                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                    <span class="text-secondary fw-medium small">{{ now()->translatedFormat('l, d F Y') }}</span>
                    <span class="text-muted mx-2">|</span>
                    <i class="bi bi-clock me-1 text-muted"></i>
                    <span id="dashboard-clock" class="text-dark fw-bold small">{{ now()->format('H:i') }}</span>
                    <span class="text-muted small ms-1">WIB</span>
                </div>
                {{-- WebSocket Live Indicator --}}
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-white border shadow-sm">
                    <span id="ws-live-dot" class="pulse-dot"></span>
                    <span class="small fw-semibold text-success">Live Sync</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== 4 KPI METRIC CARDS ===== --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Hadir Hari Ini --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Hadir Hari Ini</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-person-check-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 id="ws-present-count" class="fw-bold text-dark mb-0" style="font-size: 2rem;">{{ $stats['total_present'] ?? $todayPresensiCount }}</h2>
                        @php
                            $rate = $totalStudents > 0 ? round((($stats['student_present'] ?? $stats['total_present'] ?? 0) / $totalStudents) * 100) : 0;
                        @endphp
                        <span class="badge-status badge-present ms-auto" title="Tingkat kehadiran siswa">
                            <i class="bi bi-arrow-up-short"></i>{{ $rate }}% Siswa
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between text-muted small mt-2 pt-2 border-top" style="font-size: 0.78rem;">
                        <span>Siswa: <strong class="text-dark">{{ $stats['student_present'] ?? 0 }}</strong> / {{ $totalStudents }}</span>
                        <span>Pegawai: <strong class="text-dark">{{ $stats['employee_present'] ?? 0 }}</strong> / {{ $totalEmployees }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Terlambat --}}
        <div class="col-xl-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Terlambat</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #fffbeb; color: #d97706;">
                            <i class="bi bi-clock-history fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 id="ws-late-count" class="fw-bold text-dark mb-0" style="font-size: 2rem;">{{ $stats['total_late'] ?? 0 }}</h2>
                        <span class="badge-status badge-late ms-auto">
                            Toleransi {{ $setting['late_tolerance_minutes'] ?? 15 }}m
                        </span>
                    </div>
                    <div class="text-muted small mt-2 pt-2 border-top" style="font-size: 0.78rem;">
                        Batas masuk: <strong class="text-dark">{{ $setting['check_in_end'] ?? '07:00' }} WIB</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Izin / Sakit Menunggu Persetujuan --}}
        <div class="col-xl-3 col-sm-6">
            <a href="{{ route('admin.attendances.index', ['approval' => 'pending', 'date' => null]) }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body p-3 p-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Izin / Sakit Pending</span>
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #f0f9ff; color: #0284c7;">
                                <i class="bi bi-file-earmark-medical-fill fs-5"></i>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline gap-2">
                            <h2 id="ws-pending-approval-count" class="fw-bold text-dark mb-0" style="font-size: 2rem;">{{ $pendingApprovals }}</h2>
                            @if($pendingApprovals > 0)
                                <span class="badge-status badge-sick ms-auto">
                                    <i class="bi bi-exclamation-circle me-1"></i>Tinjau
                                </span>
                            @else
                                <span class="badge-status badge-present ms-auto">
                                    <i class="bi bi-check2"></i>Clear
                                </span>
                            @endif
                        </div>
                        <div class="text-muted small mt-2 pt-2 border-top" style="font-size: 0.78rem;">
                            {{ $pendingApprovals > 0 ? 'Perlu tindakan verifikasi admin' : 'Semua pengajuan telah diproses' }}
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Card 4: Belum Hadir / Alfa --}}
        <div class="col-xl-3 col-sm-6">
            @php
                $absentEst = max(0, ($totalStudents ?? 0) - ($stats['student_present'] ?? 0) - ($stats['total_permission'] ?? 0));
            @endphp
            <div class="card h-100 border-0 shadow-sm hover-lift">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-muted text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Belum Hadir (Siswa)</span>
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #fff1f2; color: #e11d48;">
                            <i class="bi bi-person-x-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 class="fw-bold text-dark mb-0" style="font-size: 2rem;">{{ $absentEst }}</h2>
                        <span class="badge-status badge-absent ms-auto">
                            Auto-Alfa 15:00
                        </span>
                    </div>
                    <div class="text-muted small mt-2 pt-2 border-top" style="font-size: 0.78rem;">
                        Belum presensi hingga jam sekarang
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== TREN & QUICK APPROVAL INBOX ===== --}}
    <div class="row g-4 mb-4">
        {{-- Grafik Tren 7 Hari --}}
        <div class="col-xl-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div>
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Tren Kehadiran 7 Hari Terakhir
                        </h6>
                        <span class="text-muted small" style="font-size: 0.78rem;">Perbandingan Hadir Tepat Waktu vs Terlambat</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-none d-sm-flex align-items-center gap-2 small text-muted">
                            <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background: #2563eb;"></span> Tepat Waktu
                            <span class="d-inline-block rounded-circle ms-2" style="width: 10px; height: 10px; background: #f59e0b;"></span> Terlambat
                        </div>
                        <a href="{{ route('admin.attendances.students') }}" class="btn btn-sm btn-outline-primary py-1 px-3" style="font-size: 0.78rem;">
                            Lihat Semua →
                        </a>
                    </div>
                </div>
                <div class="card-body p-4" style="height: 290px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Quick Action Approval Inbox --}}
        <div class="col-xl-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div>
                        <h6 class="fw-bold text-dark m-0">
                            <i class="bi bi-inbox-fill text-primary me-2"></i>Inbox Pengajuan Izin / Sakit
                        </h6>
                        <span class="text-muted small" style="font-size: 0.78rem;">Tindakan persetujuan langsung admin</span>
                    </div>
                    <span class="badge-status badge-sick">{{ count($pendingRequests) }} Menunggu</span>
                </div>
                <div class="card-body p-0">
                    @if($pendingRequests->isEmpty())
                        <div class="text-center py-5">
                            <div class="mb-2 text-success"><i class="bi bi-check-circle-fill fs-1"></i></div>
                            <h6 class="fw-semibold text-dark mb-1">Semua Pengajuan Selesai</h6>
                            <p class="text-muted small mb-0 px-4">Tidak ada pengajuan izin atau surat sakit yang menunggu verifikasi saat ini.</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($pendingRequests as $req)
                                <div class="list-group-item p-3 border-bottom d-flex align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3 overflow-hidden">
                                        <div class="avatar-placeholder bg-light text-primary">
                                            {{ strtoupper(substr($req->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="overflow-hidden">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-semibold text-dark text-truncate" style="font-size: 0.88rem;">{{ $req->user->name }}</span>
                                                @if($req->user->student)
                                                    <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.68rem;">{{ $req->user->student->class_name }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center gap-2 mt-1">
                                                @if($req->status === 'sick')
                                                    <span class="badge-status badge-sick" style="font-size: 0.68rem; padding: 2px 6px;">
                                                        <i class="bi bi-bandaid"></i>Sakit
                                                    </span>
                                                @else
                                                    <span class="badge-status badge-permission" style="font-size: 0.68rem; padding: 2px 6px;">
                                                        <i class="bi bi-file-text"></i>Izin
                                                    </span>
                                                @endif
                                                <span class="text-muted small text-truncate" style="font-size: 0.78rem;" title="{{ $req->notes ?? '-' }}">
                                                    {{ Str::limit($req->notes ?? 'Tidak ada keterangan', 32) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 shrink-0">
                                        {{-- Tombol Lihat Bukti --}}
                                        @if($req->proof_url)
                                            <button type="button" class="btn-action btn-action-view" title="Lihat Foto Bukti" onclick="showProofModal('{{ $req->proof_url }}', '{{ addslashes($req->user->name) }}')">
                                                <i class="bi bi-image"></i>
                                            </button>
                                        @endif

                                        {{-- Tombol Setujui --}}
                                        <form action="{{ route('admin.attendances.approve', $req->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn-action btn-action-approve" title="Setujui Pengajuan" onclick="return confirm('Setujui pengajuan izin/sakit dari {{ addslashes($req->user->name) }}?')">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>

                                        {{-- Tombol Tolak --}}
                                        <form action="{{ route('admin.attendances.approve', $req->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn-action btn-action-delete" title="Tolak Pengajuan (Tercatat Alfa)" onclick="return confirm('Tolak permohonan ini? Status pengguna akan otomatis tercatat Alfa.')">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </form>

                                        {{-- Link Detail Lengkap --}}
                                        <a href="{{ route('admin.attendances.show', $req->id) }}" class="btn-action btn-action-view" title="Lihat Detail Halaman">
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== LOG PRESENSI HARI INI ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex align-items-center justify-content-between py-3 border-bottom">
            <div>
                <h6 class="fw-bold text-dark m-0">
                    <i class="bi bi-clock-history text-primary me-2"></i>Presensi Terkini Hari Ini
                </h6>
                <span class="text-muted small" style="font-size: 0.78rem;">Aktivitas check-in dan check-out terbaru secara langsung</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-outline-primary py-1 px-3" style="font-size: 0.82rem;">
                    Kelola Semua Presensi →
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if ($todayPresensi->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3 text-muted"><i class="bi bi-calendar-x fs-1"></i></div>
                    <h6 class="text-dark fw-medium">Belum Ada Presensi Hari Ini</h6>
                    <p class="text-muted small mb-0">Catatan presensi masuk akan otomatis tampil di sini secara real-time.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">Nama Pengguna</th>
                                <th>Peran / Kelas</th>
                                <th>Status Kehadiran</th>
                                <th>Waktu Masuk</th>
                                <th>Waktu Pulang</th>
                                <th>Lokasi Presensi</th>
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ws-attendance-tbody">
                            @foreach ($todayPresensi as $presensi)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-placeholder">
                                                {{ strtoupper(substr($presensi->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark" style="font-size: 0.88rem;">{{ $presensi->user->name ?? 'Unknown' }}</div>
                                                <div class="text-muted small" style="font-size: 0.75rem;">{{ $presensi->user->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($presensi->user->student)
                                            <span class="badge bg-light text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-person-badge me-1"></i>{{ $presensi->user->student->class_name }}
                                            </span>
                                        @elseif($presensi->user->employee)
                                            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.72rem;">
                                                <i class="bi bi-briefcase me-1"></i>{{ $presensi->user->employee->position ?? 'Pegawai' }}
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">Administrator</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($presensi->status == 'present')
                                            @if($presensi->is_late)
                                                <span class="badge-status badge-late"><i class="bi bi-clock-history"></i>Terlambat</span>
                                            @else
                                                <span class="badge-status badge-present"><i class="bi bi-check2"></i>Hadir</span>
                                            @endif
                                        @elseif($presensi->status == 'permission')
                                            <span class="badge-status badge-permission">
                                                <i class="bi bi-file-text"></i>Izin
                                                @if($presensi->is_approved === true)
                                                    <i class="bi bi-check-all ms-1 text-success"></i>
                                                @elseif($presensi->is_approved === false)
                                                    <i class="bi bi-x ms-1 text-danger"></i>
                                                @else
                                                    <span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">Review</span>
                                                @endif
                                            </span>
                                        @elseif($presensi->status == 'sick')
                                            <span class="badge-status badge-sick">
                                                <i class="bi bi-bandaid"></i>Sakit
                                                @if($presensi->is_approved === true)
                                                    <i class="bi bi-check-all ms-1 text-success"></i>
                                                @elseif($presensi->is_approved === false)
                                                    <i class="bi bi-x ms-1 text-danger"></i>
                                                @else
                                                    <span class="badge bg-warning text-dark ms-1" style="font-size:0.6rem;">Review</span>
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge-status badge-absent"><i class="bi bi-x-circle"></i>Alfa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-medium text-dark" style="font-size: 0.85rem;">
                                            {{ \Carbon\Carbon::parse($presensi->recorded_at)->format('H:i') }} WIB
                                        </span>
                                    </td>
                                    <td>
                                        @if($presensi->check_out_time)
                                            <span class="fw-medium text-dark" style="font-size: 0.85rem;">
                                                {{ \Carbon\Carbon::parse($presensi->check_out_time)->format('H:i') }} WIB
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($presensi->latitude && $presensi->longitude)
                                            <a href="https://www.google.com/maps?q={{ $presensi->latitude }},{{ $presensi->longitude }}" target="_blank" class="text-decoration-none small d-inline-flex align-items-center text-primary" style="font-size: 0.78rem;">
                                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>GPS Terkunci
                                            </a>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @if($presensi->proof_url)
                                                <button type="button" class="btn-action btn-action-view" title="Lihat Foto Bukti" onclick="showProofModal('{{ $presensi->proof_url }}', '{{ addslashes($presensi->user->name) }}')">
                                                    <i class="bi bi-image"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.attendances.show', $presensi->id) }}" class="btn-action btn-action-view" title="Detail Presensi Lengkap">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($todayPresensi instanceof \Illuminate\Pagination\LengthAwarePaginator && $todayPresensi->hasPages())
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Menampilkan <strong class="text-dark">{{ $todayPresensi->firstItem() }}-{{ $todayPresensi->lastItem() }}</strong> dari <strong class="text-dark">{{ $todayPresensi->total() }}</strong> catatan
                    </div>
                    <div class="pagination-modern">
                        {{ $todayPresensi->links('pagination::bootstrap-5') }}
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>

    {{-- ===== STATUS SISTEM & PENGUMUMAN WIDGET ===== --}}
    <div class="row g-4">
        {{-- Geofence & Parameter Operasional --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark m-0">
                        <i class="bi bi-sliders text-primary me-2"></i>Parameter Operasional Presensi
                    </h6>
                    <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 text-primary" style="font-size: 0.8rem;">
                        Ubah Pengaturan →
                    </a>
                </div>
                <div class="card-body p-3 p-md-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <span class="text-muted small d-block mb-1">Jam Masuk (Check-In)</span>
                                <strong class="text-dark fs-6">{{ $setting['check_in_start'] ?? '06:00' }} - {{ $setting['check_in_end'] ?? '07:00' }} WIB</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <span class="text-muted small d-block mb-1">Jam Pulang (Check-Out)</span>
                                <strong class="text-dark fs-6">{{ $setting['check_out_start'] ?? '15:00' }} - {{ $setting['check_out_end'] ?? '18:00' }} WIB</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <span class="text-muted small d-block mb-1">Radius Geofence GPS</span>
                                <strong class="text-dark fs-6">{{ $setting['office_radius'] ?? '50' }} Meter</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded-3 bg-light border">
                                <span class="text-muted small d-block mb-1">Toleransi Keterlambatan</span>
                                <strong class="text-dark fs-6">{{ $setting['late_tolerance_minutes'] ?? '15' }} Menit</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pengumuman Terkini --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark m-0">
                        <i class="bi bi-megaphone text-primary me-2"></i>Pengumuman Terkini di Mobile
                    </h6>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-link text-decoration-none p-0 text-primary" style="font-size: 0.8rem;">
                        Kelola Pengumuman →
                    </a>
                </div>
                <div class="card-body p-3">
                    @if($recentAnnouncements->isEmpty())
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-chat-left-dots fs-3 d-block mb-2"></i>
                            Belum ada pengumuman aktif untuk aplikasi mobile siswa & pegawai.
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentAnnouncements as $ann)
                                <div class="list-group-item px-2 py-2 border-0 border-bottom">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <strong class="text-dark small text-truncate" style="max-width: 70%;">{{ $ann->title }}</strong>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.65rem;">Aktif</span>
                                    </div>
                                    <p class="text-muted small mb-0 text-truncate" style="font-size: 0.78rem;">{{ $ann->content }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ===== MODAL QUICK PREVIEW BUKTI ===== --}}
<div class="modal fade" id="proofModal" tabindex="-1" aria-labelledby="proofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold text-dark" id="proofModalLabel">
                    <i class="bi bi-file-earmark-image text-primary me-2"></i>Bukti Dokumen / Surat
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center bg-light">
                <div id="proofLoading" class="py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <img id="proofModalImage" src="" alt="Bukti Surat" class="img-fluid rounded-3 border shadow-sm d-none" style="max-height: 480px; object-fit: contain;">
                <p id="proofModalCaption" class="text-muted small mt-2 mb-0"></p>
            </div>
            <div class="modal-footer border-top py-2">
                <a id="proofDownloadBtn" href="" target="_blank" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-box-arrow-up-right me-1"></i>Buka Tab Baru
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ─── Digital Clock ───────────────────────────────────────────────────────────
function updateDashboardClock() {
    const clockEl = document.getElementById('dashboard-clock');
    if (!clockEl) return;
    const now = new Date();
    const h = String(now.getHours()).padStart(2, '0');
    const m = String(now.getMinutes()).padStart(2, '0');
    clockEl.textContent = `${h}:${m}`;
}
setInterval(updateDashboardClock, 30000);

// ─── Modal Proof Image Function ──────────────────────────────────────────────
function showProofModal(url, userName) {
    const modalEl = document.getElementById('proofModal');
    const imgEl = document.getElementById('proofModalImage');
    const loadingEl = document.getElementById('proofLoading');
    const captionEl = document.getElementById('proofModalCaption');
    const downloadBtn = document.getElementById('proofDownloadBtn');

    loadingEl.classList.remove('d-none');
    imgEl.classList.add('d-none');
    captionEl.textContent = `Surat/Bukti dari: ${userName}`;
    downloadBtn.href = url;

    imgEl.onload = function() {
        loadingEl.classList.add('d-none');
        imgEl.classList.remove('d-none');
    };
    imgEl.onerror = function() {
        loadingEl.classList.add('d-none');
        captionEl.textContent = 'Gagal memuat gambar bukti.';
    };
    imgEl.src = url;

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

// ─── Weekly Trend Chart (Chart.js) ──────────────────────────────────────────
(function() {
    @php
        $chartLabels = [];
        $ontimeValues = [];
        $lateValues = [];
        $permissionValues = [];
        if (!empty($weeklyData)) {
            foreach ($weeklyData as $dateKey => $data) {
                $chartLabels[] = $data['label'];
                $ontimeValues[] = $data['ontime'];
                $lateValues[] = $data['late'];
                $permissionValues[] = $data['permission'];
            }
        }
    @endphp

    const labels = @json($chartLabels);
    const ontimeData = @json($ontimeValues);
    const lateData = @json($lateValues);

    const ctx = document.getElementById('weeklyChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Tepat Waktu',
                    data: ontimeData,
                    backgroundColor: '#2563eb',
                    hoverBackgroundColor: '#1d4ed8',
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7,
                },
                {
                    label: 'Terlambat',
                    data: lateData,
                    backgroundColor: '#f59e0b',
                    hoverBackgroundColor: '#d97706',
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.7,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    titleColor: '#ffffff',
                    bodyColor: '#cbd5e1',
                    padding: 10,
                    cornerRadius: 8,
                }
            },
            scales: {
                x: {
                    stacked: false,
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 11 } }
                },
                y: {
                    stacked: false,
                    grid: { color: '#f1f5f9' },
                    ticks: { color: '#64748b', font: { size: 11 }, stepSize: 1, precision: 0 },
                    beginAtZero: true
                }
            }
        }
    });
})();

// ─── WebSocket Listeners ─────────────────────────────────────────────────────
function registerDashboardWebSocket() {
    if (typeof window.Echo === 'undefined') return;

    // Listener 1: New attendance logged
    window.Echo.private('admin.attendance')
        .listen('.AttendanceLogged', (data) => {
            if (data.action === 'check_out') return;

            const tbody = document.getElementById('ws-attendance-tbody');
            if (tbody) {
                const statusMap = {
                    present:    { label: 'Hadir',  badgeClass: 'badge-present', icon: 'bi-check2' },
                    permission: { label: 'Izin',   badgeClass: 'badge-permission', icon: 'bi-file-text' },
                    sick:       { label: 'Sakit',  badgeClass: 'badge-sick', icon: 'bi-bandaid' },
                    absent:     { label: 'Alfa',   badgeClass: 'badge-absent', icon: 'bi-x-circle' },
                };
                const s = statusMap[data.status] ?? statusMap.absent;
                const initial = (data.user_name ?? '?').charAt(0).toUpperCase();

                const row = document.createElement('tr');
                row.className = 'ws-new-row';
                row.innerHTML = `
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-placeholder">${initial}</div>
                            <div>
                                <div class="fw-semibold text-dark" style="font-size: 0.88rem;">${data.user_name ?? 'Unknown'}</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Baru Saja</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.72rem;">Siswa</span></td>
                    <td>
                        <span class="badge-status ${s.badgeClass}">
                            <i class="bi ${s.icon}"></i>${s.label}
                        </span>
                    </td>
                    <td><span class="fw-medium text-dark" style="font-size: 0.85rem;">${data.time ?? ''} WIB</span></td>
                    <td><span class="text-muted small">—</span></td>
                    <td><span class="text-primary small" style="font-size:0.75rem;"><i class="bi bi-geo-alt-fill text-danger me-1"></i>Live GPS</span></td>
                    <td class="pe-4 text-end">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:0.7rem;">Baru Masuk</span>
                    </td>
                `;
                tbody.prepend(row);
            }

            // Live dot pulse
            const dot = document.getElementById('ws-live-dot');
            if (dot) {
                dot.style.transform = 'scale(1.6)';
                setTimeout(() => dot.style.transform = 'scale(1)', 600);
            }
        });

    // Listener 2: Dashboard stats updated
    window.Echo.private('admin.dashboard')
        .listen('.DashboardStatsUpdated', (data) => {
            if (data.total_present !== undefined) {
                const el = document.getElementById('ws-present-count');
                if (el) el.textContent = data.total_present;
            }
            if (data.total_late !== undefined) {
                const el = document.getElementById('ws-late-count');
                if (el) el.textContent = data.total_late;
            }
            if (data.pending_approvals !== undefined) {
                const el = document.getElementById('ws-pending-approval-count');
                if (el) el.textContent = data.pending_approvals;
            }
        });
}

if (typeof window.Echo !== 'undefined') {
    registerDashboardWebSocket();
} else {
    window.addEventListener('echo:ready', registerDashboardWebSocket, { once: true });
}
</script>

<style>
.pulse-dot {
    width: 8px;
    height: 8px;
    background: #059669;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.7);
    animation: pulseGlow 2s infinite;
}
@keyframes pulseGlow {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(5, 150, 105, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(5, 150, 105, 0); }
}
@keyframes wsSlideDown {
    from { opacity: 0; transform: translateY(-8px); background-color: #eff6ff; }
    to { opacity: 1; transform: translateY(0); background-color: transparent; }
}
.ws-new-row {
    animation: wsSlideDown 0.6s ease;
}
</style>
@endpush
