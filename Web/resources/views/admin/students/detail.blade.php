@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-1">Student Profile</h2>
            <p class="text-white-50 small mb-0">Complete data for <strong>{{ $student->name }}</strong>.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.students.index') }}" class="btn border-0 bg-light-soft text-white px-4">
                <i class="bi bi-arrow-left me-2"></i>Back to Directory
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- ===== PHOTO PANEL ===== --}}
        <div class="col-lg-4">
            <div class="card glass border-0 shadow-lg">
                <div class="card-body p-4 text-center">
                    @if(isset($student->student->profile_picture) && \Illuminate\Support\Facades\Storage::disk('public')->exists($student->student->profile_picture))
                        <div class="mx-auto rounded-4 mb-3 overflow-hidden border border-white border-opacity-10 shadow-lg"
                             style="width:170px;height:210px;flex-shrink:0;">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($student->student->profile_picture) }}"
                                 alt="Profile" style="width:100%;height:100%;object-fit:cover;display:block;">
                        </div>
                    @else
                        <div class="avatar-preview mx-auto rounded-4 mb-3 d-flex align-items-center justify-content-center bg-light-soft border border-white border-opacity-10"
                             style="width:170px;height:210px;">
                            <i class="bi bi-person-bounding-box text-white-25 display-3"></i>
                        </div>
                    @endif

                    <h5 class="fw-bold text-white mb-1">{{ $student->name }}</h5>

                    <div class="d-flex align-items-center justify-content-center gap-2 mt-2 flex-wrap">
                        <span class="badge px-3 py-2" style="background:rgba(6,182,212,0.12);color:#06b6d4;border:1px solid rgba(6,182,212,0.25);">
                            <i class="bi bi-mortarboard me-1"></i>Student
                        </span>
                        <span class="badge px-3 py-2" style="background:rgba(16,185,129,0.12);color:#10b981;border:1px solid rgba(16,185,129,0.25);">
                            <i class="bi bi-circle-fill me-1" style="font-size:0.45rem;"></i>Active
                        </span>
                    </div>

                    @if($student->student->grade ?? false)
                        <p class="text-white-50 small mt-2 mb-0">
                            <i class="bi bi-mortarboard me-1"></i>{{ $student->student->grade }}
                        </p>
                    @endif
                </div>
                <div class="card-footer bg-light-soft border-0 p-3">
                    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-warning w-100 fw-semibold text-dark">
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
                        <i class="bi bi-shield-lock me-2 text-cyan"></i>Account Information
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Email</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white d-flex align-items-center justify-content-between">
                                <span>{{ $student->email }}</span>
                                <button class="copy-btn" onclick="copyToClipboard('{{ $student->email }}')" title="Salin email">
                                    <i class="bi bi-clipboard fs-6"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Role</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10">
                                <span class="badge px-3 py-2" style="background:rgba(6,182,212,0.1);color:#06b6d4;border:1px solid rgba(6,182,212,0.2);">Student</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Academic & Personal Records --}}
            <div class="card glass border-0 shadow-lg mb-4">
                <div class="card-header border-0 py-3">
                    <h6 class="mb-0 text-white fw-bold">
                        <i class="bi bi-mortarboard me-2 text-cyan"></i>Academic & Personal Records
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">NISN</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white d-flex align-items-center justify-content-between">
                                <span>{{ $student->student->nisn ?? '-' }}</span>
                                @if($student->student->nisn ?? false)
                                <button class="copy-btn" onclick="copyToClipboard('{{ $student->student->nisn }}')" title="Salin NISN">
                                    <i class="bi bi-clipboard fs-6"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">NIS (Local ID)</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white d-flex align-items-center justify-content-between">
                                <span>{{ $student->student->nis ?? '-' }}</span>
                                @if($student->student->nis ?? false)
                                <button class="copy-btn" onclick="copyToClipboard('{{ $student->student->nis }}')" title="Salin NIS">
                                    <i class="bi bi-clipboard fs-6"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Grade</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ $student->student->grade ?? '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Gender</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                @if(($student->student->gender ?? '') == 'male') Male
                                @elseif(($student->student->gender ?? '') == 'female') Female
                                @else - @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Place, Date of Birth</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ $student->student->place_of_birth ?? '-' }},
                                {{ isset($student->student->date_of_birth) ? \Carbon\Carbon::parse($student->student->date_of_birth)->format('d F Y') : '-' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Religion</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ ucfirst($student->student->religion ?? '-') }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white-50 small fw-semibold">Phone Number</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white d-flex align-items-center justify-content-between">
                                <span>{{ $student->student->phone_number ?? '-' }}</span>
                                @if($student->student->phone_number ?? false)
                                <button class="copy-btn" onclick="copyToClipboard('{{ $student->student->phone_number }}')" title="Salin nomor HP">
                                    <i class="bi bi-clipboard fs-6"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white-50 small fw-semibold">Address</label>
                            <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-10 text-white">
                                {{ $student->student->address ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== RIWAYAT PRESENSI TERAKHIR ===== --}}
            <div class="card glass border-0 shadow-lg">
                <div class="card-header border-0 py-3 d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 text-white fw-bold">
                        <i class="bi bi-calendar-check me-2 text-cyan"></i>Recent Attendance History
                    </h6>
                    <a href="{{ route('admin.attendances.index', ['search' => $student->name]) }}"
                       class="btn btn-sm btn-link text-cyan text-decoration-none p-0 fw-semibold">
                        View All →
                    </a>
                </div>
                <div class="card-body p-0">
                    @php
                        $recentAttendances = $student->attendances()->latest('recorded_at')->take(5)->get() ?? collect();
                    @endphp

                    @if($recentAttendances->isEmpty())
                        <div class="text-center py-4 border-top border-white border-opacity-5">
                            <i class="bi bi-calendar-x text-white-25 fs-4 mb-2 d-block"></i>
                            <p class="text-white-50 small mb-0">No attendance history yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4">Date & Time</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendances as $att)
                                    <tr class="border-top border-white border-opacity-5">
                                        <td class="ps-4 text-white-50 small">
                                            {{ \Carbon\Carbon::parse($att->recorded_at)->format('d M Y, H:i') }}
                                        </td>
                                        <td>
                                            @if($att->status == 'present')
                                                <span class="badge px-2 py-1" style="background:rgba(16,185,129,0.15);color:#10b981;">Present</span>
                                            @elseif($att->status == 'permission')
                                                <span class="badge px-2 py-1" style="background:rgba(245,158,11,0.15);color:#f59e0b;">Permission</span>
                                            @elseif($att->status == 'sick')
                                                <span class="badge px-2 py-1" style="background:rgba(6,182,212,0.15);color:#06b6d4;">Sick</span>
                                            @else
                                                <span class="badge px-2 py-1" style="background:rgba(239,68,68,0.15);color:#ef4444;">Absent</span>
                                            @endif
                                        </td>
                                        <td class="pe-4 text-end text-white-50 small">{{ $att->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
