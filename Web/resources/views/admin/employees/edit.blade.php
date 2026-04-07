@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Employee Profile</h5>
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-sm btn-light">
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

                    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="text-center mb-4">
                            @if(isset($employee->employee->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->employee->profile_picture))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($employee->employee->profile_picture) }}" alt="Profile Photo" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 3rem;">
                                    <i class="bi bi-person"></i>
                                </div>
                            @endif
                        </div>

                        <h6 class="text-warning text-darken-3 mb-3"><i class="bi bi-person-badge"></i> Account Data</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password (Leave blank if unchanged)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Access Level</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select Level</option>
                                    <option value="guru" {{ (old('role') ?? ($employee->hasRole('guru') ? 'guru' : '')) == 'guru' ? 'selected' : '' }}>Teacher</option>
                                    <option value="staff" {{ (old('role') ?? ($employee->hasRole('staff') ? 'staff' : '')) == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-warning text-darken-3 mb-3"><i class="bi bi-briefcase"></i> Employee Profile Data</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nip" class="form-label">NIP (National ID)</label>
                                <input type="text" class="form-control" id="nip" name="nip" value="{{ old('nip', $employee->employee->nip ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position / Academic Subject</label>
                                <input type="text" class="form-control" id="position" name="position" value="{{ old('position', $employee->employee->position ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $employee->employee->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $employee->employee->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="place_of_birth" class="form-label">Place of Birth</label>
                                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth', $employee->employee->place_of_birth ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $employee->employee->date_of_birth ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="religion" class="form-label">Religion</label>
                                <select class="form-select" id="religion" name="religion">
                                    <option value="">Select Religion</option>
                                    <option value="islam" {{ old('religion', $employee->employee->religion ?? '') == 'islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="kristen" {{ old('religion', $employee->employee->religion ?? '') == 'kristen' ? 'selected' : '' }}>Christian</option>
                                    <option value="katholik" {{ old('religion', $employee->employee->religion ?? '') == 'katholik' ? 'selected' : '' }}>Catholic</option>
                                    <option value="hindu" {{ old('religion', $employee->employee->religion ?? '') == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="buddha" {{ old('religion', $employee->employee->religion ?? '') == 'buddha' ? 'selected' : '' }}>Buddhist</option>
                                    <option value="konghucu" {{ old('religion', $employee->employee->religion ?? '') == 'konghucu' ? 'selected' : '' }}>Confucian</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $employee->employee->phone_number ?? '') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Full Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $employee->employee->address ?? '') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="profile_picture" class="form-label">Change Profile Picture (Max 2MB)</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-warning btn-lg">Update Employee Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
