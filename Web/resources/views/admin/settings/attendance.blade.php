@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="row align-items-center mb-5">
        <div class="col-md-7">
            <h2 class="fw-bold text-white mb-0">Attendance Protocol</h2>
            <p class="text-white-50 small mb-0">Configure global schedules, grace periods, and presence validation logic.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center bg-light-soft rounded-pill px-4 py-2 border border-white border-opacity-10 shadow-sm">
                <i class="bi bi-calendar-event me-3 text-cyan"></i>
                <span class="text-white-50 fw-medium small">Operational Rules</span>
            </div>
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success d-flex align-items-center mb-4 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.update_attendance_settings') }}" method="POST">
        @csrf

        <div class="row g-4">
            {{-- Check-In Card --}}
            <div class="col-md-4">
                <div class="card glass border-0 shadow-lg h-100">
                    <div class="card-body p-4 text-center">
                        <div class="icon-circle bg-success-soft text-emerald mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 18px; display: flex; align-items: center; justify-content: center; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-box-arrow-in-right fs-3"></i>
                        </div>
                        <h6 class="text-white fw-bold mb-1">Check-In Deadline</h6>
                        <p class="text-white-50 small mb-4">Final entry threshold</p>
                        
                        <input type="time" name="check_in_end" value="{{ $checkInEnd }}" 
                               class="form-control form-control-lg bg-light-soft border-white border-opacity-10 text-white text-center fs-2 fw-bold py-3 mb-3 h-auto" required>
                        
                        <p class="text-white-25 small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Late after this timestamp excluding tolerance.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tolerance Card --}}
            <div class="col-md-4">
                <div class="card glass border-0 shadow-lg h-100">
                    <div class="card-body p-4 text-center">
                        <div class="icon-circle bg-warning-soft text-amber mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 18px; display: flex; align-items: center; justify-content: center; background: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-hourglass-split fs-3"></i>
                        </div>
                        <h6 class="text-white fw-bold mb-1">Grace Period</h6>
                        <p class="text-white-50 small mb-4">Minutes post-deadline</p>
                        
                        <div class="input-group input-group-lg mb-3">
                            <input type="number" name="late_tolerance" value="{{ $lateTolerance }}" min="0" max="60" 
                                   class="form-control bg-light-soft border-white border-opacity-10 text-white text-end fs-2 fw-bold py-3 h-auto border-end-0" required>
                            <span class="input-group-text bg-light-soft border-white border-opacity-10 text-white-50 border-start-0 fs-5 px-3">min</span>
                        </div>
                        
                        <p class="text-white-25 small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Presence within this window is marked as 'Late'.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Check-Out Card --}}
            <div class="col-md-4">
                <div class="card glass border-0 shadow-lg h-100">
                    <div class="card-body p-4 text-center">
                        <div class="icon-circle bg-primary-soft text-cyan mx-auto mb-3" style="width: 60px; height: 60px; border-radius: 18px; display: flex; align-items: center; justify-content: center; background: rgba(6, 182, 212, 0.1);">
                            <i class="bi bi-box-arrow-right fs-3"></i>
                        </div>
                        <h6 class="text-white fw-bold mb-1">Check-Out Start</h6>
                        <p class="text-white-50 small mb-4">Earliest departure time</p>
                        
                        <input type="time" name="check_out_start" value="{{ $checkOutStart }}" 
                               class="form-control form-control-lg bg-light-soft border-white border-opacity-10 text-white text-center fs-2 fw-bold py-3 mb-3 h-auto" required>
                        
                        <p class="text-white-25 small mb-0">
                            <i class="bi bi-info-circle me-1"></i>
                            Departure UI activates after this time.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Simulation Row --}}
        <div class="card glass border-0 shadow-lg mt-5 overflow-hidden">
            <div class="card-header bg-light-soft border-0 py-3 px-4">
                <h6 class="fw-bold mb-0 text-white"><i class="bi bi-magic me-2 text-cyan"></i>Live Logic Simulation</h6>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-stretch g-3">
                    <div class="col-md">
                        <div class="h-100 p-4 rounded-4 bg-light-soft border border-white border-opacity-10 d-flex flex-column align-items-center text-center">
                            <div class="fs-1 text-emerald mb-2">●</div>
                            <small class="text-white-50 mb-1">Early Access</small>
                            <div class="fw-bold text-white mb-2">Before {{ $checkInEnd }}</div>
                            <div class="badge bg-success bg-opacity-10 text-emerald border border-success border-opacity-20 px-3 py-2">ON-TIME</div>
                        </div>
                    </div>
                    <div class="col-md-auto d-none d-md-flex align-items-center"><i class="bi bi-chevron-right text-white-25 fs-4"></i></div>
                    <div class="col-md">
                        @php
                            $parts = explode(':', $checkInEnd);
                            $lateH = (int)$parts[0];
                            $lateM = (int)$parts[1] + (int)$lateTolerance;
                            if ($lateM >= 60) { $lateH++; $lateM -= 60; }
                            $lateStr = sprintf('%02d:%02d', $lateH, $lateM);
                        @endphp
                        <div class="h-100 p-4 rounded-4 bg-light-soft border border-white border-opacity-10 d-flex flex-column align-items-center text-center">
                            <div class="fs-1 text-amber mb-2">●</div>
                            <small class="text-white-50 mb-1">Grace Window</small>
                            <div class="fw-bold text-white mb-2">{{ $checkInEnd }} - {{ $lateStr }}</div>
                            <div class="badge bg-warning bg-opacity-10 text-amber border border-warning border-opacity-20 px-3 py-2">LATE STATUS</div>
                        </div>
                    </div>
                    <div class="col-md-auto d-none d-md-flex align-items-center"><i class="bi bi-chevron-right text-white-25 fs-4"></i></div>
                    <div class="col-md">
                        <div class="h-100 p-4 rounded-4 bg-light-soft border border-white border-opacity-10 d-flex flex-column align-items-center text-center">
                            <div class="fs-1 text-danger mb-2">●</div>
                            <small class="text-white-50 mb-1">Hard Cut-off</small>
                            <div class="fw-bold text-white mb-2">After {{ $lateStr }}</div>
                            <div class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 px-3 py-2">REJECTED / ALFA</div>
                        </div>
                    </div>
                    <div class="col-md-auto d-none d-md-flex align-items-center"><i class="bi bi-chevron-right text-white-25 fs-4"></i></div>
                    <div class="col-md">
                        <div class="h-100 p-4 rounded-4 bg-light-soft border border-white border-opacity-10 d-flex flex-column align-items-center text-center">
                            <div class="fs-1 text-cyan mb-2">●</div>
                            <small class="text-white-50 mb-1">Exit Portal</small>
                            <div class="fw-bold text-white mb-2">Opens at {{ $checkOutStart }}</div>
                            <div class="badge bg-info bg-opacity-10 text-cyan border border-info border-opacity-20 px-3 py-2">CHECK-OUT READY</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="d-flex justify-content-end mt-5 pb-5">
            <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg border-0 fw-bold">
                <i class="bi bi-shield-lock-fill me-2"></i> Update Attendance Protocol
            </button>
        </div>

    </form>
</div>
@endsection
