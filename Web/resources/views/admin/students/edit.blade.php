@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Student Profile</h5>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-light">
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

                    <form action="{{ route('admin.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="text-center mb-4">
                            @if(isset($student->student->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->student->profile_picture))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($student->student->profile_picture) }}" alt="Profile Photo" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
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
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $student->name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $student->email) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">New Password (Leave blank if unchanged)</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="text-warning text-darken-3 mb-3"><i class="bi bi-mortarboard"></i> Student Profile Data</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="national_student_number" class="form-label">NISN (National ID)</label>
                                <input type="text" class="form-control" id="national_student_number" name="national_student_number" value="{{ old('national_student_number', $student->student->national_student_number ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="student_number" class="form-label">Student ID (NIS)</label>
                                <input type="text" class="form-control" id="student_number" name="student_number" value="{{ old('student_number', $student->student->student_number ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="grade" class="form-label">Grade / Class</label>
                                <input type="text" class="form-control" id="grade" name="grade" value="{{ old('grade', $student->student->grade ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $student->student->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->student->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="place_of_birth" class="form-label">Place of Birth</label>
                                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth', $student->student->place_of_birth ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $student->student->date_of_birth ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="religion" class="form-label">Religion</label>
                                <select class="form-select" id="religion" name="religion">
                                    <option value="">Select Religion</option>
                                    <option value="islam" {{ old('religion', $student->student->religion ?? '') == 'islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="kristen" {{ old('religion', $student->student->religion ?? '') == 'kristen' ? 'selected' : '' }}>Christian</option>
                                    <option value="katholik" {{ old('religion', $student->student->religion ?? '') == 'katholik' ? 'selected' : '' }}>Catholic</option>
                                    <option value="hindu" {{ old('religion', $student->student->religion ?? '') == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="buddha" {{ old('religion', $student->student->religion ?? '') == 'buddha' ? 'selected' : '' }}>Buddhist</option>
                                    <option value="konghucu" {{ old('religion', $student->student->religion ?? '') == 'konghucu' ? 'selected' : '' }}>Confucian</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $student->student->phone_number ?? '') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Full Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $student->student->address ?? '') }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="profile_picture" class="form-label">Change Profile Picture (Max 2MB)</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            </div>
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-warning btn-lg">Update Student Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
