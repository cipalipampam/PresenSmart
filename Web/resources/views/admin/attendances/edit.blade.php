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
                <span class="text-muted small">Ubah Data</span>
            </div>
            <h1 class="fw-bold text-dark mb-1" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                Ubah Catatan Presensi
            </h1>
            <p class="text-muted small mb-0">Perbarui rincian riwayat presensi untuk <strong class="text-dark">{{ $attendance->user->name }}</strong>.</p>
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
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
                <h6 class="mb-0 fw-bold">Peringatan Validasi</h6>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.attendances.update', $attendance->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @if($scope)<input type="hidden" name="scope" value="{{ $scope }}">@endif
        
        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Data Section Card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 text-dark fw-bold">
                            <i class="bi bi-pencil-square text-primary me-2"></i>Perubahan Data Presensi
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-dark fw-semibold small">Nama Anggota (Siswa / Pegawai)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-person-fill"></i></span>
                                    <input type="text" class="form-control bg-light"
                                           value="{{ $attendance->user->name }} — {{ $attendance->user->hasRole('siswa') ? 'Siswa (' . ($attendance->user->student?->class_name ?? '-') . ')' : ($attendance->user->hasRole('guru') ? 'Guru' : 'Staff') }}"
                                           readonly disabled>
                                </div>
                                <input type="hidden" name="user_id" value="{{ $attendance->user_id }}">
                            </div>

                            <div class="col-md-6">
                                <label for="status" class="form-label text-dark fw-semibold small">
                                    Status Kehadiran <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="present" {{ old('status', $attendance->status) == 'present' ? 'selected' : '' }}>Hadir (Present)</option>
                                    <option value="permission" {{ old('status', $attendance->status) == 'permission' ? 'selected' : '' }}>Izin (Permission)</option>
                                    <option value="sick" {{ old('status', $attendance->status) == 'sick' ? 'selected' : '' }}>Sakit (Sick)</option>
                                    <option value="absent" {{ old('status', $attendance->status) == 'absent' ? 'selected' : '' }}>Alfa (Absent)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="recorded_at" class="form-label text-dark fw-semibold small">
                                    Waktu Masuk (Check-In) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="bi bi-calendar-check"></i></span>
                                    <input type="datetime-local" class="form-control @error('recorded_at') is-invalid @enderror"
                                           id="recorded_at" name="recorded_at"
                                           value="{{ old('recorded_at', \Carbon\Carbon::parse($attendance->recorded_at)->format('Y-m-d\TH:i')) }}" required>
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
                                           value="{{ old('check_out_time', $attendance->check_out_time?->format('Y-m-d\TH:i')) }}">
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="notes" class="form-label text-dark fw-semibold small">
                                    Catatan Tambahan / Alasan
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror"
                                          id="notes" name="notes" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                {{-- Verification Section --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 text-dark fw-bold">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>Dokumen / Bukti Kehadiran
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        @if($attendance->proof_image)
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-semibold d-block">Bukti Terlampir Saat Ini</label>
                            <div class="p-3 rounded-3 bg-light border text-center">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="small text-dark fw-medium"><i class="bi bi-image text-primary me-1"></i>Dokumen Bukti</span>
                                    <a href="{{ route('admin.attendances.proof', $attendance) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.75rem;">
                                        Lihat File
                                    </a>
                                </div>
                                <img src="{{ route('admin.attendances.proof', $attendance) }}" alt="Bukti Surat" class="img-fluid rounded border bg-white p-1" style="max-height: 120px; object-fit: contain;">
                            </div>
                        </div>
                        @endif

                        <div class="mb-2">
                            <label for="proof_image" class="form-label text-dark fw-semibold small">
                                {{ $attendance->proof_image ? 'Ganti File Bukti' : 'Unggah File Bukti Baru' }}
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
                            <i class="bi bi-check-circle-fill me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route($attendanceRouteName) }}" class="btn btn-outline-secondary w-100 py-2" style="font-size: 0.85rem;">
                            Batal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
