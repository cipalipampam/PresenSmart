@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0">Update Personnel Profile</h2>
            <p class="text-white-50 small mb-0">Modify professional credentials or update contact nodes for <strong>{{ $employee->name }}</strong>.</p>
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
                <h6 class="mb-0 fw-bold">Update Conflict</h6>
            </div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                {{-- Account Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-white bg-opacity-5 border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-shield-lock me-2 text-amber"></i>Security & Access</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="name" class="form-label text-white-50 small fw-semibold">Legal Name</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="role" class="form-label text-white-50 small fw-semibold">Access Level</label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                    <option value="">Select organizational role</option>
                                    <option value="guru" {{ (old('role') ?? ($employee->hasRole('guru') ? 'guru' : '')) == 'guru' ? 'selected' : '' }}>Educator (Teacher)</option>
                                    <option value="staff" {{ (old('role') ?? ($employee->hasRole('staff') ? 'staff' : '')) == 'staff' ? 'selected' : '' }}>Administration (Staff)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label text-white-50 small fw-semibold">Institutional Email</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $employee->email) }}" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="password" class="form-label text-white-50 small fw-semibold">Reset Credentials <span class="text-white-25 fw-normal">(Leave blank if unchanged)</span></label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-key"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Profile Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-white bg-opacity-5 border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-briefcase me-2 text-amber"></i>Professional Dossier</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nip" class="form-label text-white-50 small fw-semibold">NIP</label>
                                <input type="text" class="form-control" id="nip" name="nip" value="{{ old('nip', $employee->employee->nip ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label text-white-50 small fw-semibold">Strategic Position</label>
                                <input type="text" class="form-control" id="position" name="position" value="{{ old('position', $employee->employee->position ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="gender" class="form-label text-white-50 small fw-semibold">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Select gender</option>
                                    <option value="male" {{ old('gender', $employee->employee->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $employee->employee->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="phone_number" class="form-label text-white-50 small fw-semibold">Contact Relay</label>
                                <div class="input-group">
                                    <span class="input-group-text border-0 bg-light-soft text-white-50">+62</span>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', $employee->employee->phone_number ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="place_of_birth" class="form-label text-white-50 small fw-semibold">Birth City</label>
                                <input type="text" class="form-control" id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth', $employee->employee->place_of_birth ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label text-white-50 small fw-semibold">Birth Date</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $employee->employee->date_of_birth ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="religion" class="form-label text-white-50 small fw-semibold">Religion</label>
                                <select class="form-select" id="religion" name="religion">
                                    <option value="">Select religion</option>
                                    <option value="islam" {{ old('religion', $employee->employee->religion ?? '') == 'islam' ? 'selected' : '' }}>Islam</option>
                                    <option value="kristen" {{ old('religion', $employee->employee->religion ?? '') == 'kristen' ? 'selected' : '' }}>Christian</option>
                                    <option value="katholik" {{ old('religion', $employee->employee->religion ?? '') == 'katholik' ? 'selected' : '' }}>Catholic</option>
                                    <option value="hindu" {{ old('religion', $employee->employee->religion ?? '') == 'hindu' ? 'selected' : '' }}>Hindu</option>
                                    <option value="buddha" {{ old('religion', $employee->employee->religion ?? '') == 'buddha' ? 'selected' : '' }}>Buddhist</option>
                                    <option value="konghucu" {{ old('religion', $employee->employee->religion ?? '') == 'konghucu' ? 'selected' : '' }}>Confucian</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="address" class="form-label text-white-50 small fw-semibold">Current Residence</label>
                                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $employee->employee->address ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Media Section --}}
                <div class="card glass border-0 shadow-lg mb-4">
                    <div class="card-header bg-white bg-opacity-5 border-0 py-3">
                        <h6 class="mb-0 text-white fw-bold"><i class="bi bi-camera me-2 text-amber"></i>Identity Snapshot</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="mb-4">
                            @if(isset($employee->employee->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->employee->profile_picture))
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($employee->employee->profile_picture) }}" 
                                     alt="Profile" class="rounded-4 shadow-lg border border-white border-opacity-10 mb-3" style="width: 150px; height: 180px; object-fit: cover;">
                            @else
                                <div class="avatar-preview mx-auto rounded-4 mb-3 d-flex align-items-center justify-content-center bg-white bg-opacity-5 border border-white border-opacity-10" style="width: 150px; height: 180px;">
                                    <i class="bi bi-person-workspace text-white-25 display-4"></i>
                                </div>
                            @endif
                            <p class="text-white-25 small mb-0">Active identity proxy</p>
                        </div>
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label text-white-50 small fw-semibold text-start w-100">Upload Replacement</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                            <div class="form-text text-white-25 small text-start mt-2">Leave blank to retain current proxy image.</div>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="card glass border-0 shadow-lg position-sticky" style="top: 2rem;">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-warning w-100 py-3 fw-bold shadow-lg border-0 mb-3 text-dark">
                            <i class="bi bi-arrow-repeat me-2"></i>Synchronize Dossier
                        </button>
                        <p class="text-white-50 small text-center mb-0 px-2">
                             Update will be verified and distributed across all terminal nodes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
