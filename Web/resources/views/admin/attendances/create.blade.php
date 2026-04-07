@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Manual Attendance Input</h5>
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

                    <form action="{{ route('admin.attendances.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select Employee / Student</label>
                            <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                <option value="">--- Select User ---</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} - {{ $user->hasRole('siswa') ? 'Student' : ($user->hasRole('guru') ? 'Teacher' : 'Staff') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Attendance Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">--- Select Status ---</option>
                                <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Present</option>
                                <option value="permission" {{ old('status') == 'permission' ? 'selected' : '' }}>Permission</option>
                                <option value="sick" {{ old('status') == 'sick' ? 'selected' : '' }}>Sick</option>
                                <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Absent (No Info)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="recorded_at" class="form-label">Date & Time</label>
                            <input type="datetime-local" class="form-control" id="recorded_at" name="recorded_at" value="{{ old('recorded_at', now()->format('Y-m-d\TH:i')) }}">
                            <small class="text-muted">Optional. Leave as current time if realtime input.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes / Information</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="proof_image" class="form-label">Photo Proof / Document (Optional, Max 5MB)</label>
                            <input type="file" class="form-control border @error('proof_image') is-invalid @enderror" id="proof_image" name="proof_image" accept="image/*,.pdf">
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg">Save Manual Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
