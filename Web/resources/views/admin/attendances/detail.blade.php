@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1">Attendance Details</h2>
            <p class="text-white-50 small mb-0">Full attendance log for <strong>{{ $attendance->user->name }}</strong>.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.attendances.index') }}" class="btn border-0 bg-light-soft text-white px-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Log
            </a>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">

            {{-- ===== PRESENCE RECORD ===== --}}
            <div class="card glass border-0 shadow-lg mb-4">
                <div class="card-header border-0 py-3">
                    <h6 class="mb-0 text-white fw-bold">
                        <i class="bi bi-calendar-check me-2 text-emerald"></i>Presence Record
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Member Name</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ $attendance->user->name }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Attendance Status</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10">
                                @if($attendance->status == 'present')
                                    <span class="badge px-3 py-2" style="background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.2);">
                                        <i class="bi bi-check-circle me-1"></i>Present
                                    </span>
                                @elseif($attendance->status == 'permission')
                                    <span class="badge px-3 py-2" style="background:rgba(245,158,11,0.15);color:#f59e0b;border:1px solid rgba(245,158,11,0.2);">
                                        <i class="bi bi-clock me-1"></i>Permission —
                                        {{ $attendance->is_approved === null ? 'Pending' : ($attendance->is_approved ? 'Approved' : 'Rejected') }}
                                    </span>
                                @elseif($attendance->status == 'sick')
                                    <span class="badge px-3 py-2" style="background:rgba(6,182,212,0.15);color:#06b6d4;border:1px solid rgba(6,182,212,0.2);">
                                        <i class="bi bi-heart-pulse me-1"></i>Sick —
                                        {{ $attendance->is_approved === null ? 'Pending' : ($attendance->is_approved ? 'Approved' : 'Rejected') }}
                                    </span>
                                @else
                                    <span class="badge px-3 py-2" style="background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.2);">
                                        <i class="bi bi-x-circle me-1"></i>Absent
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Attendance Time</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ \Carbon\Carbon::parse($attendance->recorded_at)->format('d F Y, H:i:s') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">GPS Coordinates</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10">
                                @if($attendance->latitude && $attendance->longitude)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ $attendance->latitude }},{{ $attendance->longitude }}"
                                       target="_blank" class="text-decoration-none" style="color:#06b6d4;">
                                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $attendance->latitude }}, {{ $attendance->longitude }}
                                    </a>
                                @else
                                    <span class="text-white-50">Location not recorded</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white-50 small fw-semibold">Notes / Description</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ $attendance->notes ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== GOOGLE MAPS EMBED ===== --}}
            @if($attendance->latitude && $attendance->longitude)
            <div class="card glass border-0 shadow-lg mb-4">
                <div class="card-header border-0 py-3">
                    <h6 class="mb-0 text-white fw-bold">
                        <i class="bi bi-map me-2 text-cyan"></i>Presence Location on Map
                    </h6>
                </div>
                <div class="card-body p-0 overflow-hidden" style="border-radius:0 0 12px 12px;">
                    <iframe
                        src="https://www.google.com/maps?q={{ $attendance->latitude }},{{ $attendance->longitude }}&z=17&output=embed"
                        width="100%"
                        height="280"
                        style="border:0;display:block;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </div>
            @endif

            {{-- ===== SUPPORTING DOCUMENTATION ===== --}}
            @if(isset($attendance->proof_image) && \Illuminate\Support\Facades\Storage::disk('public')->exists($attendance->proof_image))
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold">
                            <i class="bi bi-file-earmark-image me-2 text-cyan"></i>Supporting Evidence
                        </h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($attendance->proof_image) }}"
                             alt="Bukti" class="img-fluid rounded-3 shadow border border-white border-opacity-10"
                             style="max-height:400px;">
                    </div>
                </div>
            @endif

            {{-- ===== ACTIONS ===== --}}
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.attendances.index') }}" class="btn border-0 bg-light-soft text-white px-4">
                    Back
                </a>
                <a href="{{ route('admin.attendances.edit', $attendance->id) }}"
                   class="btn btn-warning fw-semibold px-5 text-dark shadow-sm">
                    <i class="bi bi-pencil me-2"></i>Edit Attendance
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
