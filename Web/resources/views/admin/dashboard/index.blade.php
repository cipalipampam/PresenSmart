@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <h1 class="fw-bold text-white mb-1" style="font-size:1.75rem;">
                Welcome, {{ Auth::user()->name ?? 'Admin' }} 👋
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><i class="bi bi-house-fill text-white-50 me-1"></i><span class="text-white-50">Home</span></li>
                    <li class="breadcrumb-item active text-cyan" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center bg-light-soft rounded-pill px-4 py-2 border border-white border-opacity-10">
                <i class="bi bi-calendar3 me-2 text-cyan"></i>
                <span class="text-white-50 fw-medium small">{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    {{-- ===== STAT CARDS ===== --}}
    <div class="row g-4 mb-4">
        {{-- Total Anggota --}}
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-lg hover-lift h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary-soft text-cyan me-3">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                        <h6 class="text-white-50 text-uppercase fw-bold m-0" style="font-size:0.7rem;letter-spacing:0.06em;">Total Active Members</h6>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <h2 class="display-5 fw-bold text-white mb-1">{{ $userCount }}</h2>
                            <span class="trend-badge trend-neu">
                                <i class="bi bi-person-check me-1"></i>Registered Students
                            </span>
                        </div>
                    </div>
                    <div class="opacity-10 fs-1 text-cyan position-absolute end-0 bottom-0 mb-n1 me-n1">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Aktivitas Hari Ini --}}
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-lg hover-lift h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success-soft text-emerald me-3">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <h6 class="text-white-50 text-uppercase fw-bold m-0" style="font-size:0.7rem;letter-spacing:0.06em;">Present Today</h6>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <h2 class="display-5 fw-bold text-white mb-1">{{ $todayPresensiCount }}</h2>
                            <span class="trend-badge trend-up">
                                <i class="bi bi-check-all me-1"></i>Attendances Logged
                            </span>
                        </div>
                    </div>
                    <div class="opacity-10 fs-1 text-emerald position-absolute end-0 bottom-0 mb-n1 me-n1">
                        <i class="bi bi-shield-check"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lokasi Aktif --}}
        <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-lg hover-lift h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-warning-soft text-amber me-3">
                            <i class="bi bi-geo-alt-fill fs-5"></i>
                        </div>
                        <h6 class="text-white-50 text-uppercase fw-bold m-0" style="font-size:0.7rem;letter-spacing:0.06em;">Active School Location</h6>
                    </div>
                    <div>
                        <h5 class="fw-bold text-white mb-1">HQ School</h5>
                        <p class="text-white-50 small mb-2" style="font-size:0.78rem;">{{ $setting['school_lat'] }}, {{ $setting['school_long'] }}</p>
                        <span class="d-inline-flex align-items-center gap-2">
                            <span class="gps-dot"></span>
                            <span class="text-emerald" style="font-size:0.78rem;font-weight:600;">GPS Active</span>
                        </span>
                    </div>
                    <div class="opacity-10 fs-1 text-amber position-absolute end-0 bottom-0 mb-n1 me-n1">
                        <i class="bi bi-geo"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== CHART: Tren Kehadiran 7 Hari ===== --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-white m-0">
                        <i class="bi bi-graph-up-arrow me-2 text-cyan"></i>Attendance Trend
                        <span class="text-white-50 fw-normal ms-2 small">Last 7 Days</span>
                    </h6>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-link text-cyan text-decoration-none fw-semibold p-0">
                        View All →
                    </a>
                </div>
                <div class="card-body p-4" style="height:220px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== LOG & RINGKASAN ===== --}}
    <div class="row g-4">
        {{-- Log Presensi Hari Ini --}}
        <div class="col-xl-8">
            <div class="card border-0 shadow-lg" style="min-height:350px;">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-white m-0">
                        Today's Attendance Log
                        <span class="text-white-50 fw-normal ms-2 small">Latest Entries</span>
                    </h6>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-link text-cyan text-decoration-none fw-semibold p-0">
                        View All →
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($todayPresensi->isEmpty())
                        <div class="text-center py-5 border-top border-white border-opacity-5">
                            <div class="mb-3"><i class="bi bi-inbox fs-1 text-white-25"></i></div>
                            <h6 class="text-white-50 fw-medium">No attendances today</h6>
                            <p class="text-muted small mb-0">Attendance data will appear here once someone checks in.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Member Name</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($todayPresensi as $presensi)
                                        <tr class="border-top border-white border-opacity-5">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="bg-light-soft rounded-circle text-cyan d-flex align-items-center justify-content-center fw-bold"
                                                         style="width:34px;height:34px;font-size:0.8rem;flex-shrink:0;">
                                                        {{ strtoupper(substr($presensi->user->name, 0, 1)) }}
                                                    </div>
                                                    <span class="fw-medium text-white">{{ $presensi->user->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($presensi->status == 'present')
                                                    <span class="badge px-2 py-1" style="background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.25);">Present</span>
                                                @elseif($presensi->status == 'permission')
                                                    <span class="badge px-2 py-1" style="background:rgba(245,158,11,0.15);color:#f59e0b;border:1px solid rgba(245,158,11,0.25);">Permission</span>
                                                @elseif($presensi->status == 'sick')
                                                    <span class="badge px-2 py-1" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:1px solid rgba(6,182,212,0.25);">Sick</span>
                                                @else
                                                    <span class="badge px-2 py-1" style="background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.25);">Absent</span>
                                                @endif
                                            </td>
                                            <td class="pe-4 text-end">
                                                <span class="text-white-50 small fw-medium">{{ \Carbon\Carbon::parse($presensi->recorded_at)->format('H:i') }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($todayPresensi instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="card-footer bg-transparent border-0 p-4 border-top border-white border-opacity-5 d-flex justify-content-between align-items-center">
                            <div class="text-white-50 small">
                                Showing <span class="text-white fw-bold">{{ $todayPresensi->firstItem() }}-{{ $todayPresensi->lastItem() }}</span>
                                of <span class="text-white fw-bold">{{ $todayPresensi->total() }}</span> entries
                            </div>
                            <div class="pagination-modern">
                                {{ $todayPresensi->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Ringkasan Hari Ini --}}
        <div class="col-xl-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-transparent">
                    <h6 class="fw-bold text-white m-0">Today's Summary</h6>
                </div>
                <div class="card-body p-4 pt-3">
                    @php
                        $totalToday = $todayPresensi instanceof \Illuminate\Pagination\LengthAwarePaginator
                            ? $todayPresensi->total()
                            : (is_countable($todayPresensi) ? count($todayPresensi) : 0);

                        // For paginated, we need to get all items for counting — use the collection
                        $allItems = $todayPresensi instanceof \Illuminate\Pagination\LengthAwarePaginator
                            ? $todayPresensi->getCollection()
                            : $todayPresensi;

                        $presentCount    = $allItems->where('status', 'present')->count();
                        $permissionCount = $allItems->where('status', 'permission')->count();
                        $sickCount       = $allItems->where('status', 'sick')->count();
                        $absentCount     = $allItems->where('status', 'absent')->count();
                        $reliabilityPct  = $totalToday > 0 ? round(($presentCount / $totalToday) * 100) : 0;
                    @endphp

                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50 small">Attendance Rate</span>
                            <span class="text-white fw-bold small">{{ $reliabilityPct }}%</span>
                        </div>
                        <div class="progress bg-white bg-opacity-10" style="height:6px;border-radius:10px;">
                            <div class="progress-bar" role="progressbar"
                                 style="width:{{ $reliabilityPct }}%;background:linear-gradient(90deg,#06b6d4,#10b981);border-radius:10px;"
                                 aria-valuenow="{{ $reliabilityPct }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <div class="p-3 rounded-2 bg-light-soft d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle" style="width:9px;height:9px;background:#10b981;"></div>
                                <span class="text-white-50 small">Present</span>
                            </div>
                            <span class="text-white fw-bold">{{ $presentCount }}</span>
                        </div>
                        <div class="p-3 rounded-2 bg-light-soft d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle" style="width:9px;height:9px;background:#f59e0b;"></div>
                                <span class="text-white-50 small">Permission</span>
                            </div>
                            <span class="text-white fw-bold">{{ $permissionCount }}</span>
                        </div>
                        <div class="p-3 rounded-2 bg-light-soft d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle" style="width:9px;height:9px;background:#06b6d4;"></div>
                                <span class="text-white-50 small">Sick</span>
                            </div>
                            <span class="text-white fw-bold">{{ $sickCount }}</span>
                        </div>
                        <div class="p-3 rounded-2 bg-light-soft d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle" style="width:9px;height:9px;background:#ef4444;"></div>
                                <span class="text-white-50 small">Absent</span>
                            </div>
                            <span class="text-white fw-bold">{{ $absentCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    // Weekly attendance chart — use $weeklyData if passed by controller, else build 7-day labels
    @php
        // Build 7-day labels and empty dataset as fallback
        $weeklyLabels = [];
        $weeklyValues = [];
        for ($i = 6; $i >= 0; $i--) {
            $weeklyLabels[] = now()->subDays($i)->format('D, d M');
            $weeklyValues[] = isset($weeklyData) ? ($weeklyData[now()->subDays($i)->format('Y-m-d')] ?? 0) : 0;
        }
    @endphp

    const labels = @json($weeklyLabels);
    const values = @json($weeklyValues);

    const ctx = document.getElementById('weeklyChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Present',
                data: values,
                borderColor: '#06b6d4',
                backgroundColor: 'rgba(6,182,212,0.08)',
                borderWidth: 2.5,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#06b6d4',
                pointBorderColor: '#0f172a',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(30,41,59,0.95)',
                    borderColor: 'rgba(6,182,212,0.3)',
                    borderWidth: 1,
                    titleColor: '#f8fafc',
                    bodyColor: '#94a3b8',
                    padding: 10,
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} present`
                    }
                }
            },
            scales: {
                x: {
                    grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                    ticks: { color: '#64748b', font: { size: 11 } }
                },
                y: {
                    grid: { color: 'rgba(255,255,255,0.04)', drawBorder: false },
                    ticks: { color: '#64748b', font: { size: 11 }, stepSize: 1, precision: 0 },
                    beginAtZero: true
                }
            }
        }
    });
})();
</script>
@endpush
