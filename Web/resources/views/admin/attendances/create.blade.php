@extends('admin.layouts.app')

@section('content')
@php
    $attendanceRouteName = match ($scope) {
        'siswa' => 'admin.attendances.students',
        'employee' => 'admin.attendances.employees',
        default => 'admin.attendances.index',
    };
@endphp
<div class="py-2">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route($attendanceRouteName) }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-calendar-check me-1"></i>Data Presensi
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Input Manual</span>
            </div>
            <h1 class="fw-bold text-dark mb-1" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                Catat Presensi Manual
            </h1>
            <p class="text-muted small mb-0">
                Buat atau koreksi pencatatan presensi siswa dan pegawai secara resmi oleh administrator.
            </p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <a href="{{ route($attendanceRouteName) }}" class="btn btn-outline-secondary py-2 px-3" style="font-size: 0.85rem;">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar
            </a>
        </div>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert" style="background-color: #fff1f2; color: #9f1239; border-left: 4px solid #e11d48 !important;">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-octagon-fill me-2 fs-5 text-danger"></i>
                <h6 class="mb-0 fw-bold">Gagal Menyimpan Data</h6>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.attendances.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($scope)<input type="hidden" name="scope" value="{{ $scope }}">@endif
        
        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Entry Section Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 text-dark fw-bold">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Formulir Data Presensi
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="user_id" class="form-label text-dark fw-semibold small">
                                    Pilih Pengguna (Siswa / Pegawai) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-search"></i></span>
                                    <select class="form-select border-start-0 @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">-- Pilih Anggota Terdaftar --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} — 
                                                @if($user->hasRole('siswa'))
                                                    Siswa ({{ $user->student?->class_name ?? 'Belum ada kelas' }})
                                                @elseif($user->hasRole('guru'))
                                                    Guru ({{ $user->employee?->position ?? 'Tenaga Pengajar' }})
                                                @elseif($user->hasRole('staff'))
                                                    Staff / Tenaga Kependidikan
                                                @else
                                                    {{ $user->email }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-text text-muted small">Pilih nama siswa atau pegawai yang ingin dicatat kehadirannya.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label text-dark fw-semibold small">
                                    Status Kehadiran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="present" {{ old('status') == 'present' ? 'selected' : '' }}>Hadir (Present)</option>
                                    <option value="permission" {{ old('status') == 'permission' ? 'selected' : '' }}>Izin (Permission)</option>
                                    <option value="sick" {{ old('status') == 'sick' ? 'selected' : '' }}>Sakit (Sick)</option>
                                    <option value="absent" {{ old('status') == 'absent' ? 'selected' : '' }}>Alfa / Tanpa Keterangan (Absent)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="recorded_at" class="form-label text-dark fw-semibold small">
                                    Waktu Masuk (Check-In) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar-event"></i></span>
                                    <input type="datetime-local" class="form-control @error('recorded_at') is-invalid @enderror"
                                           id="recorded_at" name="recorded_at"
                                           value="{{ old('recorded_at', now()->format('Y-m-d\TH:i')) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="check_out_time" class="form-label text-dark fw-semibold small">
                                    Waktu Pulang (Check-Out) <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-box-arrow-right"></i></span>
                                    <input type="datetime-local" class="form-control @error('check_out_time') is-invalid @enderror"
                                           id="check_out_time" name="check_out_time"
                                           value="{{ old('check_out_time') }}">
                                </div>
                                <div class="form-text text-muted small">Kosongkan jika pengguna belum melakukan absensi pulang.</div>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label text-dark fw-semibold small">
                                    Catatan / Alasan Kehadiran <span class="text-muted fw-normal">(Opsional)</span>
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="3"
                                          placeholder="Contoh: Terlambat karena kendala transportasi / Izin keperluan keluarga...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Bukti / Dokumen Pendukung --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h6 class="mb-0 text-dark fw-bold">
                            <i class="bi bi-paperclip text-primary me-2"></i>Dokumen / Bukti
                        </h6>
                        <span id="proof-badge-status" class="badge bg-secondary-subtle text-secondary" style="font-size: 0.72rem;">Opsional</span>
                    </div>
                    <div class="card-body p-4 text-center bg-light">
                        <div class="mb-3">
                            <div class="rounded-3 mx-auto mb-3 d-flex align-items-center justify-content-center bg-white border" style="width: 100px; height: 100px;">
                                <i id="proof-icon" class="bi bi-file-earmark-medical text-primary display-6"></i>
                            </div>
                            <p id="proof-help-text" class="text-muted small mb-0">Surat dokter, surat izin orang tua, atau dokumen pendukung.</p>
                        </div>
                        <div class="mb-2 text-start">
                            <label for="proof_image" class="form-label text-dark fw-semibold small d-block">
                                <span id="proof-label-text">File Surat / Bukti</span>
                                <span id="proof-required-mark" class="text-danger d-none">*</span>
                            </label>
                            <input type="file" class="form-control @error('proof_image') is-invalid @enderror"
                                   id="proof_image" name="proof_image" accept="image/*,.pdf">
                            <div class="form-text text-muted small mt-1">Format: JPG, PNG, PDF (Maksimal 5MB).</div>
                        </div>
                    </div>
                </div>

                {{-- Action Card --}}
                <div class="card border-0 shadow-sm position-sticky" style="top: 2rem;">
                    <div class="card-body p-4">
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm mb-3">
                            <i class="bi bi-check-circle-fill me-2"></i>Simpan Presensi
                        </button>
                        <a href="{{ route($attendanceRouteName) }}" class="btn btn-outline-secondary w-100 py-2" style="font-size: 0.85rem;">
                            Batal
                        </a>
                        <p class="text-muted small text-center mb-0 mt-3 px-2" style="font-size: 0.75rem;">
                            <i class="bi bi-shield-check me-1 text-success"></i>Pencatatan manual akan diaudit dan langsung tersinkronisasi ke laporan institusi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const proofBadge = document.getElementById('proof-badge-status');
    const proofLabel = document.getElementById('proof-label-text');
    const proofRequiredMark = document.getElementById('proof-required-mark');
    const proofHelpText = document.getElementById('proof-help-text');
    const proofInput = document.getElementById('proof_image');
    const proofIcon = document.getElementById('proof-icon');

    function updateProofRequirement() {
        if (!statusSelect) return;
        if (statusSelect.value === 'sick') {
            proofBadge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle';
            proofBadge.textContent = 'Wajib Dilampirkan';
            proofLabel.textContent = 'Surat Keterangan Sakit / Dokter';
            proofRequiredMark.classList.remove('d-none');
            proofHelpText.innerHTML = '<strong class="text-danger">Surat dokter atau bukti medis wajib diunggah</strong> untuk status Sakit.';
            proofInput.required = true;
            proofIcon.className = 'bi bi-hospital-fill text-danger display-6';
        } else if (statusSelect.value === 'permission') {
            proofBadge.className = 'badge bg-primary-subtle text-primary border border-primary-subtle';
            proofBadge.textContent = 'Surat Izin';
            proofLabel.textContent = 'Surat Izin Orang Tua / Bukti';
            proofRequiredMark.classList.add('d-none');
            proofHelpText.textContent = 'Lampirkan surat permohonan izin dari orang tua atau pihak terkait jika ada.';
            proofInput.required = false;
            proofIcon.className = 'bi bi-file-earmark-text text-primary display-6';
        } else {
            proofBadge.className = 'badge bg-secondary-subtle text-secondary';
            proofBadge.textContent = 'Opsional';
            proofLabel.textContent = 'File Surat / Bukti';
            proofRequiredMark.classList.add('d-none');
            proofHelpText.textContent = 'Surat dokter, surat izin orang tua, atau dokumen pendukung.';
            proofInput.required = false;
            proofIcon.className = 'bi bi-file-earmark-medical text-primary display-6';
        }
    }

    statusSelect.addEventListener('change', updateProofRequirement);
    updateProofRequirement();
});
</script>
@endsection
