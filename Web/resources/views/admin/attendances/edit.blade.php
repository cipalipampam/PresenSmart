@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Attendance Record</h5>
                    <a href="{{ route('admin.attendances.index') }}" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Employee / Student Name</label>
                            <input type="text" class="form-control bg-light" value="{{ $attendance->user->name }} - {{ $attendance->user->hasRole('siswa') ? 'Student' : ($attendance->user->hasRole('guru') ? 'Teacher' : 'Staff') }}" readonly disabled>
                            {{-- we don't allow changing the user of an attendance record --}}
                            <input type="hidden" name="user_id" value="{{ $attendance->user_id }}">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Attendance Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">--- Select Status ---</option>
                                <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="permission" {{ old('status', $attendance->status) == 'permission' ? 'selected' : '' }}>Permission</option>
                                <option value="sick" {{ old('status', $attendance->status) == 'sick' ? 'selected' : '' }}>Sick</option>
                                <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Absent</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="recorded_at" class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" id="recorded_at" name="recorded_at" value="{{ old('recorded_at', \Carbon\Carbon::parse($attendance->recorded_at)->format('Y-m-d\TH:i')) }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes / Information</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                        </div>

                        @if($attendance->proof_image)
                        <div class="mb-3">
                            <label class="form-label">Current Photo / Document Proof</label>
                            <div>
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($attendance->proof_image) }}" target="_blank" class="btn btn-sm btn-outline-primary mb-2">View Current File Document</a>
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="proof_image" class="form-label">Replace Proof Update (Optional, Max 5MB)</label>
                            <input type="file" class="form-control border @error('proof_image') is-invalid @enderror" id="proof_image" name="proof_image" accept="image/*,.pdf">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-warning btn-lg">Update Attendance Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
