@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0">Update Attendance Record</h2>
            <p class="text-white-50 small mb-0">Modify historical presence data for <strong>{{ $attendance->user->name }}</strong>.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.attendances.index') }}" class="btn btn-outline-secondary border-0 bg-light-soft text-white px-4">
                <i class="bi bi-arrow-left me-2"></i>Attendance Log
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <h6 class="mb-0 fw-bold">Update Conflict</h6>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                {{-- Data Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-white bg-opacity-5 border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-pencil-square me-2 text-amber"></i>Entry Modification</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-white-50 small fw-semibold">Subject Identity</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" value="{{ $attendance->user->name }} ({{ $attendance->user->hasRole('siswa') ? 'Student' : ($attendance->user->hasRole('guru') ? 'Teacher' : 'Staff') }})" readonly disabled>
                                </div>
                                <input type="hidden" name="user_id" value="{{ $attendance->user_id }}">
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label text-white-50 small fw-semibold">Updated Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">--- Select Status ---</option>
                                    <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="permission" {{ old('status', $attendance->status) == 'permission' ? 'selected' : '' }}>Permission</option>
                                    <option value="sick" {{ old('status', $attendance->status) == 'sick' ? 'selected' : '' }}>Sick</option>
                                    <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="recorded_at" class="form-label text-white-50 small fw-semibold">Log Timestamp</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-calendar-check"></i></span>
                                    <input type="datetime-local" class="form-control" id="recorded_at" name="recorded_at" value="{{ old('recorded_at', \Carbon\Carbon::parse($attendance->recorded_at)->format('Y-m-d\TH:i')) }}">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label text-white-50 small fw-semibold">Entry Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Verification Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-white bg-opacity-5 border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-file-earmark-text me-2 text-amber"></i>Verification Ledger</h6>
                    </div>
                    <div class="card-body p-4">
                        @if($attendance->proof_image)
                        <div class="mb-4">
                            <label class="form-label text-white-50 small fw-semibold d-block">Current Documentation</label>
                            <div class="p-3 rounded-3 bg-light-soft border border-white border-opacity-10">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-file-earmark-image text-info fs-4"></i>
                                        <span class="text-white-50 small">Documentation File</span>
                                    </div>
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($attendance->proof_image) }}" target="_blank" class="btn btn-sm btn-info text-white rounded-pill px-3">
                                        Review
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="proof_image" class="form-label text-white-50 small fw-semibold">Replace Verification File</label>
                            <input type="file" class="form-control border @error('proof_image') is-invalid @enderror" id="proof_image" name="proof_image" accept="image/*,.pdf">
                            <div class="form-text text-white-25 small mt-2">Maximum payload: 5MB.</div>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="card glass border-0 shadow-lg position-sticky" style="top: 2rem;">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-warning w-100 py-3 fw-bold shadow-lg border-0 mb-3 text-dark">
                            <i class="bi bi-arrow-repeat me-2"></i>Apply Changes
                        </button>
                        <p class="text-white-50 small text-center mb-0 px-2">
                             Modifications will be audited and reflected across all institution reports.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
