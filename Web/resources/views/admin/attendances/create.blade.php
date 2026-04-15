@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0">Record Manual Presence</h2>
            <p class="text-white-50 small mb-0">Override or insert attendance records for personnel and students manually.</p>
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
                <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
                <h6 class="mb-0 fw-bold">Input Rejected</h6>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.attendances.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                {{-- Entry Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-light-soft border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-info-circle me-2 text-success"></i>Entry Details</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="user_id" class="form-label text-white-50 small fw-semibold">Subject (Employee or Student)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-person-search"></i></span>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">--- Search Subject ---</option>
                                        @foreach(\App\Models\User::all() as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->hasRole('siswa') ? 'Student' : ($user->hasRole('guru') ? 'Teacher' : 'Staff') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label text-white-50 small fw-semibold">Attendance Status</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">--- Select Status ---</option>
                                    <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="permission" {{ old('status') == 'permission' ? 'selected' : '' }}>Permission</option>
                                    <option value="sick" {{ old('status') == 'sick' ? 'selected' : '' }}>Sick</option>
                                    <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent (No Info)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="recorded_at" class="form-label text-white-50 small fw-semibold">Timestamp Assignment</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-calendar-event"></i></span>
                                    <input type="datetime-local" class="form-control" id="recorded_at" name="recorded_at" value="{{ old('recorded_at', now()->format('Y-m-d\TH:i')) }}">
                                </div>
                                <div class="form-text text-white-25 small mt-2">Leave default for real-time synchronization.</div>
                            </div>
                            <div class="col-12">
                                <label for="notes" class="form-label text-white-50 small fw-semibold">Observation / Notes</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Additional context regarding this entry">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- attachment Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-light-soft border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-paperclip me-2 text-success"></i>Verification Source</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="mb-4">
                            <div class="avatar-preview mx-auto rounded-4 mb-3 d-flex align-items-center justify-content-center bg-light-soft border border-white border-opacity-10" style="width: 150px; height: 120px;">
                                <i class="bi bi-file-earmark-medical text-white-25 display-4"></i>
                            </div>
                            <p class="text-white-25 small mb-0">Evidence, certificates, or documents</p>
                        </div>
                        <div class="mb-3">
                            <input type="file" class="form-control border @error('proof_image') is-invalid @enderror" id="proof_image" name="proof_image" accept="image/*,.pdf">
                            <div class="form-text text-white-25 small text-start mt-2">Max 5MB. PDF or Image accepted.</div>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="card glass border-0 shadow-lg position-sticky" style="top: 2rem;">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-lg border-0 mb-3">
                            <i class="bi bi-cloud-upload-fill me-2"></i>Commit Record
                        </button>
                        <p class="text-white-50 small text-center mb-0 px-2">
                             Manual entries are logged and attributed to the current administrator.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
