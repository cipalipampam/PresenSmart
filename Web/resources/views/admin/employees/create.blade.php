@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Add New Employee</h5>
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

                    <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <h6 class="text-primary mb-3"><i class="bi bi-person-badge"></i> Account Data</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="role" class="form-label">Access Level</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select Level</option>
                                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Teacher</option>
                                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-primary mb-3"><i class="bi bi-briefcase"></i> Employee Profile Data</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_number" class="form-label">NIP (National ID)</label>
                                <input type="text" class="form-control @error('employee_number') is-invalid @enderror" id="employee_number" name="employee_number" value="{{ old('employee_number') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position / Academic Subject</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="place_of_birth" class="form-label">Place of Birth</label>
                                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="religion" class="form-label">Religion</label>
                                <select class="form-select" id="religion" name="religion">
                                    <option value="">Select Religion</option>
                                    <option value="islam" {{ old('religion') == 'islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="kristen" {{ old('religion') == 'kristen' ? 'selected' : '' }}>Christian</option>
                                    <option value="katholik" {{ old('religion') == 'katholik' ? 'selected' : '' }}>Catholic</option>
                                    <option value="hindu" {{ old('religion') == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="buddha" {{ old('religion') == 'buddha' ? 'selected' : '' }}>Buddhist</option>
                                    <option value="konghucu" {{ old('religion') == 'konghucu' ? 'selected' : '' }}>Confucian</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Full Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="profile_picture" class="form-label">Profile Picture (Max 2MB)</label>
                                <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Save Employee Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
