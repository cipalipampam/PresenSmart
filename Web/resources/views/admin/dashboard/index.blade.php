@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-5 align-items-center">
        <div class="col-md-6">
            <h1 class="display-6 fw-bold text-white mb-2">System Overview</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-white-50 text-decoration-none">Core</a></li>
                    <li class="breadcrumb-item active text-cyan" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center bg-light-soft rounded-pill px-4 py-2 border border-white border-opacity-10 shadow-sm">
                <i class="bi bi-calendar3 me-3 text-cyan"></i>
                <span class="text-white-50 fw-medium small">{{ now()->format('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">
        {{-- Main Stats --}}
        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-lg hover-lift h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary-soft text-cyan me-3">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <h6 class="text-white-50 text-uppercase fw-bold m-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total Members</h6>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <h2 class="display-5 fw-bold text-white mb-0">{{ $userCount }}</h2>
                            <p class="text-emerald small mb-0 mt-1"><i class="bi bi-arrow-up-right me-1"></i>Active Students</p>
                        </div>
                        <div class="opacity-25 fs-1 text-white position-absolute end-0 bottom-0 mb-n2 me-n2">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="card border-0 shadow-lg hover-lift h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success-soft text-emerald me-3">
                            <i class="bi bi-shield-check fs-4"></i>
                        </div>
                        <h6 class="text-white-50 text-uppercase fw-bold m-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Today's Activity</h6>
                    </div>
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <h2 class="display-5 fw-bold text-white mb-0">{{ $todayPresensiCount }}</h2>
                            <p class="text-cyan small mb-0 mt-1"><i class="bi bi-check-all me-1"></i>Reported Presence</p>
                        </div>
                        <div class="opacity-25 fs-1 text-white position-absolute end-0 bottom-0 mb-n2 me-n2">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-12">
            <div class="card border-0 shadow-lg hover-lift h-100 overflow-hidden">
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-warning-soft text-amber me-3">
                            <i class="bi bi-geo-alt-fill fs-4"></i>
                        </div>
                        <h6 class="text-white-50 text-uppercase fw-bold m-0" style="font-size: 0.75rem; letter-spacing: 0.05em;">Node Identity</h6>
                    </div>
                    <div>
                        <h4 class="fw-bold text-white mb-1">HQ Campus</h4>
                        <p class="text-white-50 small mb-0">{{ $setting['school_lat'] }}, {{ $setting['school_long'] }}</p>
                        <div class="mt-2">
                            <span class="badge bg-amber bg-opacity-10 text-amber border border-amber border-opacity-25">Location Locked</span>
                        </div>
                    </div>
                    <div class="opacity-25 fs-1 text-white position-absolute end-0 bottom-0 mb-n2 me-n2">
                        <i class="bi bi-geo"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-lg mb-4">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between p-4">
                    <h5 class="fw-bold text-white m-0">Recent Logs <span class="text-white-50 fw-normal ms-2 small">Daily Stream</span></h5>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-link text-cyan text-decoration-none fw-bold">View Full Stream →</a>
                </div>
                <div class="card-body p-0">
                    @if ($todayPresensi->isEmpty())
                        <div class="text-center py-5 border-top border-white border-opacity-5">
                            <div class="mb-3 text-white-25"><i class="bi bi-chat-dots fs-1"></i></div>
                            <h6 class="text-white-50">No incoming logs for today</h6>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Personnel</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Log Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($todayPresensi as $presensi)
                                        <tr class="border-top border-white border-opacity-5">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-light-soft rounded text-cyan d-flex align-items-center justify-content-center fw-bold me-3" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                        {{ strtoupper(substr($presensi->user->name, 0, 1)) }}
                                                    </div>
                                                    <span class="fw-medium text-white">{{ $presensi->user->name }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                @if($presensi->status == 'present')
                                                    <span class="badge bg-emerald bg-opacity-10 text-emerald border border-success border-opacity-25 px-2 py-1" style="color: #10b981 !important;">Present</span>
                                                @elseif($presensi->status == 'permission')
                                                    <span class="badge bg-warning bg-opacity-10 text-amber border border-warning border-opacity-25 px-2 py-1" style="color: #f59e0b !important;">Permission</span>
                                                @elseif($presensi->status == 'sick')
                                                    <span class="badge bg-info bg-opacity-10 text-cyan border border-info border-opacity-25 px-2 py-1" style="color: #06b6d4 !important;">Sick</span>
                                                @else
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1">Absent</span>
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
                        
                        {{-- Pagination --}}
                        <div class="card-footer bg-transparent border-0 p-4 border-top border-white border-opacity-5 d-flex justify-content-between align-items-center">
                            <div class="text-white-50 small">
                                Frame <span class="text-white fw-bold">{{ $todayPresensi->firstItem() }}-{{ $todayPresensi->lastItem() }}</span> of {{ $todayPresensi->total() }}
                            </div>
                            <div class="pagination-modern">
                                {{ $todayPresensi->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-lg h-100">
                <div class="card-header bg-transparent p-4">
                    <h5 class="fw-bold text-white m-0">Metric Analysts</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="mb-5">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-white-50 small">Presence Reliability</span>
                            <span class="text-white fw-bold small">88%</span>
                        </div>
                        <div class="progress bg-white bg-opacity-10" style="height: 6px;">
                            <div class="progress-bar bg-cyan" role="progressbar" style="width: 88%"></div>
                        </div>
                    </div>

                    <div class="d-grid gap-3">
                        <div class="p-3 rounded bg-light-soft border border-white border-opacity-5 d-flex align-items-center justify-content-between hover-lift">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-emerald me-3" style="width: 10px; height: 10px;"></div>
                                <span class="text-white-50 small">Present Today</span>
                            </div>
                            <span class="text-white fw-bold">{{ $todayPresensi->where('status', 'present')->count() }}</span>
                        </div>
                        <div class="p-3 rounded bg-light-soft border border-white border-opacity-5 d-flex align-items-center justify-content-between hover-lift">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-amber me-3" style="width: 10px; height: 10px;"></div>
                                <span class="text-white-50 small">Off-site Duty</span>
                            </div>
                            <span class="text-white fw-bold">{{ $todayPresensi->where('status', 'permission')->count() }}</span>
                        </div>
                        <div class="p-3 rounded bg-light-soft border border-white border-opacity-5 d-flex align-items-center justify-content-between hover-lift">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-info me-3" style="width: 10px; height: 10px;"></div>
                                <span class="text-white-50 small">Medical Sick</span>
                            </div>
                            <span class="text-white fw-bold">{{ $todayPresensi->where('status', 'sick')->count() }}</span>
                        </div>
                        <div class="p-3 rounded bg-light-soft border border-white border-opacity-5 d-flex align-items-center justify-content-between hover-lift">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-danger me-3" style="width: 10px; height: 10px;"></div>
                                <span class="text-white-50 small">Total Absentees</span>
                            </div>
                            <span class="text-white fw-bold">{{ $todayPresensi->where('status', 'absent')->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
