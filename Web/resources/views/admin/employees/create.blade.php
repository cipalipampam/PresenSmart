@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0">Recruit Personnel</h2>
            <p class="text-white-50 small mb-0">Onboard new educators or administrative staff members into the digital ecosystem.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary border-0 bg-light-soft text-white px-4">
                <i class="bi bi-arrow-left me-2"></i>Directory
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger mb-4 shadow-sm" role="alert">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <h6 class="mb-0 fw-bold">Registration Halted</h6>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.employees.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                {{-- Account Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-white bg-opacity-5 border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-shield-lock me-2 text-cyan"></i>Access Control</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label text-white-50 small fw-semibold">Legal Name</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Full name with titles" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label text-white-50 small fw-semibold">Role Designation</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select organizational role</option>
                                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Educator (Teacher)</option>
                                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Administration (Staff)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-white-50 small fw-semibold">Digital Identity (Email)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="id@school.edu" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="password" class="form-label text-white-50 small fw-semibold">Access Password</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="••••••••" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Profile Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-white bg-opacity-5 border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-briefcase me-2 text-cyan"></i>Professional Profile</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nip" class="form-label text-white-50 small fw-semibold">NIP (Registration Number)</label>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror" id="nip" name="nip" value="{{ old('nip') }}" placeholder="18-digit number">
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label text-white-50 small fw-semibold">Strategic Position</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" value="{{ old('position') }}" placeholder="e.g. Mathematics Lead">
                            </div>
                            <div class="col-md-6">
                                <label for="gender" class="form-label text-white-50 small fw-semibold">Gender</label>
                                <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                    <option value="">Select gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label text-white-50 small fw-semibold">Communication Link (WA)</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50">+62</span>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" placeholder="812xxxxx">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="place_of_birth" class="form-label text-white-50 small fw-semibold">Place of Birth</label>
                                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth') }}" placeholder="City">
                            </div>
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label text-white-50 small fw-semibold">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="religion" class="form-label text-white-50 small fw-semibold">Religion</label>
                                <select class="form-select" id="religion" name="religion">
                                    <option value="">Select religion</option>
                                    <option value="islam" {{ old('religion') == 'islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="kristen" {{ old('religion') == 'kristen' ? 'selected' : '' }}>Christian</option>
                                    <option value="katholik" {{ old('religion') == 'katholik' ? 'selected' : '' }}>Catholic</option>
                                    <option value="hindu" {{ old('religion') == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="buddha" {{ old('religion') == 'buddha' ? 'selected' : '' }}>Buddhist</option>
                                    <option value="konghucu" {{ old('religion') == 'konghucu' ? 'selected' : '' }}>Confucian</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label text-white-50 small fw-semibold">Home Residence</label>
                                <textarea class="form-control" id="address" name="address" rows="3" placeholder="Full residential details">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Media Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-white bg-opacity-5 border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-camera me-2 text-cyan"></i>Biometric Proxy</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="mb-4">
                            <div class="avatar-preview mx-auto rounded-4 mb-3 d-flex align-items-center justify-content-center bg-white bg-opacity-5 border border-white border-opacity-10" style="width: 150px; height: 180px;">
                                <i class="bi bi-person-workspace text-white-25 display-4"></i>
                            </div>
                            <p class="text-white-25 small mb-0">Official identification photo</p>
                        </div>
                        <div class="mb-3">
                            <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" id="profile_picture" name="profile_picture" accept="image/*">
                            <div class="form-text text-white-25 small text-start mt-2">Max 2MB. Dynamic resizing applied.</div>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="card glass border-0 shadow-lg position-sticky" style="top: 2rem;">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-lg border-0 mb-3">
                            <i class="bi bi-person-plus-fill me-2"></i>Execute Recruitment
                        </button>
                        <p class="text-white-50 small text-center mb-0 px-2">
                             System will automatically generate node keys for the new personnel.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
