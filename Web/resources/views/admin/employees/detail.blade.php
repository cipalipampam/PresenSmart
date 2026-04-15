@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1">Personnel Profile</h2>
            <p class="text-white-50 small mb-0">Complete dossier for <strong>{{ $employee->name }}</strong>.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.employees.index') }}" class="btn border-0 bg-light-soft text-white px-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Directory
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- ===== PHOTO PANEL ===== --}}
        <div class="col-lg-4">
            <div class="card glass border-0 shadow-lg">
                <div class="card-body p-4 text-center">
                    @if(isset($employee->employee->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->employee->profile_picture))
                        <div class="mx-auto rounded-4 mb-3 overflow-hidden border border-white border-opacity-10 shadow-lg"
                             style="width:170px;height:210px;flex-shrink:0;">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($employee->employee->profile_picture) }}"
                                 alt="Profile" style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                    @else
                        <div class="avatar-preview mx-auto rounded-4 mb-3 d-flex align-items-center justify-content-center bg-light-soft border border-white border-opacity-10"
                             style="width:170px;height:210px;">
                            <i class="bi bi-person-workspace text-white-25 display-3"></i>
                        </div>
                    @endif

                    <h5 class="fw-bold text-white mb-1">{{ $employee->name }}</h5>

                    <div class="d-flex align-items-center justify-content-center gap-2 mt-2 flex-wrap">
                        @if($employee->hasRole('guru'))
                            <span class="badge px-3 py-2" style="background:rgba(16,185,129,0.12);color:#10b981;border:1px solid rgba(16,185,129,0.25);">
                                <i class="bi bi-journal-bookmark me-1"></i>Teacher
                            </span>
                        @elseif($employee->hasRole('staff'))
                            <span class="badge px-3 py-2" style="background:rgba(148,163,184,0.12);color:#94a3b8;border:1px solid rgba(148,163,184,0.25);">
                                <i class="bi bi-person-gear me-1"></i>Staff
                            </span>
                        @endif
                        <span class="badge px-3 py-2" style="background:rgba(16,185,129,0.12);color:#10b981;border:1px solid rgba(16,185,129,0.25);">
                            <i class="bi bi-circle-fill me-1" style="font-size:0.45rem;"></i>Active
                        </span>
                    </div>

                    @if($employee->employee->position ?? false)
                        <p class="text-white-50 small mt-2 mb-0">
                            <i class="bi bi-briefcase me-1"></i>{{ $employee->employee->position }}
                        </p>
                    @endif
                </div>
                <div class="card-footer bg-light-soft border-0 p-3">
                    <a href="{{ route('admin.employees.edit', $employee->id) }}" class="btn btn-warning w-100 fw-semibold text-dark">
                        <i class="bi bi-pencil me-2"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== INFO PANEL ===== --}}
        <div class="col-lg-8">
            {{-- Account Info --}}
            <div class="card glass border-0 shadow-lg mb-4">
                <div class="card-header border-0 py-3">
                    <h6 class="mb-0 text-white fw-bold">
                        <i class="bi bi-shield-lock me-2 text-amber"></i>Account Credentials
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Email</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white d-flex align-items-center justify-content-between">
                                <span>{{ $employee->email }}</span>
                                <button class="copy-btn" onclick="copyToClipboard('{{ $employee->email }}')" title="Salin email">
                                    <i class="bi bi-clipboard fs-6"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Role & Access</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10">
                                @if($employee->hasRole('guru'))
                                    <span class="badge px-3 py-2" style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2);">Teacher / Educator</span>
                                @elseif($employee->hasRole('staff'))
                                    <span class="badge px-3 py-2" style="background:rgba(148,163,184,0.1);color:#94a3b8;border:1px solid rgba(148,163,184,0.2);">Administrative Staff</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Professional Records --}}
            <div class="card glass border-0 shadow-lg">
                <div class="card-header border-0 py-3">
                    <h6 class="mb-0 text-white fw-bold">
                        <i class="bi bi-briefcase me-2 text-amber"></i>Professional & Personal Data
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">NIP (Personnel ID)</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white d-flex align-items-center justify-content-between">
                                <span>{{ $employee->employee->nip ?? '-' }}</span>
                                @if($employee->employee->nip ?? false)
                                <button class="copy-btn" onclick="copyToClipboard('{{ $employee->employee->nip }}')" title="Salin NIP">
                                    <i class="bi bi-clipboard fs-6"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Position</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ $employee->employee->position ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Gender</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                @if(($employee->employee->gender ?? '') == 'male') Male
                                @elseif(($employee->employee->gender ?? '') == 'female') Female
                                @else - @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Place, Date of Birth</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ $employee->employee->place_of_birth ?? '-' }},
                                {{ isset($employee->employee->date_of_birth) ? \Carbon\Carbon::parse($employee->employee->date_of_birth)->format('d F Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Religion</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ ucfirst($employee->employee->religion ?? '-') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Mobile Number / WhatsApp</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white d-flex align-items-center justify-content-between">
                                <span>{{ $employee->employee->phone_number ?? '-' }}</span>
                                @if($employee->employee->phone_number ?? false)
                                <button class="copy-btn" onclick="copyToClipboard('{{ $employee->employee->phone_number }}')" title="Salin nomor HP">
                                    <i class="bi bi-clipboard fs-6"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white-50 small fw-semibold">Address</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ $employee->employee->address ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
