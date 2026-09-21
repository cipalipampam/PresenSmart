@extends('admin.layouts.app')

@section('content')
<div class="py-2">

    {{-- ===== BREADCRUMB & PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-8">
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-house-door me-1"></i>Dashboard
                </a>
                <span class="text-muted small">/</span>
                <a href="{{ route('admin.classrooms.index') }}" class="text-decoration-none text-muted small">
                    Rombel & Kelas
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Kenaikan Kelas & Kelulusan</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="fw-bold text-dark mb-0" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                    Kenaikan Kelas & Kelulusan Massal
                </h1>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    Batch Tool
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Pindahkan seluruh atau sebagian siswa ke rombel jenjang baru, atau proses kelulusan bagi siswa tingkat akhir.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.classrooms.index') }}" class="btn btn-outline-secondary shadow-sm py-2 px-3">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Daftar Rombel
            </a>
        </div>
    </div>

    {{-- ===== FLASH ALERTS ===== --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <div class="d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-octagon-fill fs-5 text-danger mt-1"></i>
                <div>
                    <strong class="d-block mb-1">Terjadi kesalahan validasi:</strong>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- ===== PETUNJUK PENGGUNAAN ===== --}}
    <div class="card border-0 shadow-sm rounded-3 mb-4 bg-light">
        <div class="card-body p-3 d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center shrink-0"
                 style="width: 42px; height: 42px; background: #e0f2fe; color: #0284c7;">
                <i class="bi bi-info-circle-fill fs-5"></i>
            </div>
            <div class="small text-secondary">
                <strong>Alur Kerja:</strong> Pilih <strong>Kelas Asal</strong> untuk memuat data siswa, tentukan <strong>Tipe Aksi</strong> (Kenaikan Kelas atau Kelulusan), lalu tentukan <strong>Kelas Tujuan</strong>. Ceklis siswa yang berhak naik kelas. Siswa yang <em>tidak dicentang</em> akan tetap berada di kelas asalnya (tinggal kelas).
            </div>
        </div>
    </div>

    {{-- ===== FORM UTAMA PROMOSI ===== --}}
    <form id="promotionForm" action="{{ route('admin.classrooms.promotion.process') }}" method="POST">
        @csrf

        {{-- KARTU PENGATURAN KELAS & AKSI --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <h5 class="fw-bold text-dark mb-0 fs-6">
                    <i class="bi bi-sliders me-2 text-primary"></i>1. Tentukan Rombel & Jenis Proses
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    {{-- KELAS ASAL --}}
                    <div class="col-md-4">
                        <label for="source_classroom_id" class="form-label fw-semibold text-dark small">
                            Kelas Asal <span class="text-danger">*</span>
                        </label>
                        <select name="source_classroom_id" id="source_classroom_id" class="form-select @error('source_classroom_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas Asal --</option>
                            @foreach($allClassrooms as $classroom)
                                <option value="{{ $classroom->id }}"
                                    data-name="{{ $classroom->name }}"
                                    data-level="{{ $classroom->level }}"
                                    data-year="{{ $classroom->academic_year }}"
                                    {{ (old('source_classroom_id', request('source_classroom_id')) == $classroom->id) ? 'selected' : '' }}>
                                    Tingkat {{ $classroom->level }} - {{ $classroom->name }} ({{ $classroom->academic_year }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Rombel siswa yang saat ini sedang aktif belajar.</div>
                    </div>

                    {{-- JENIS AKSI --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-dark small">
                            Tipe Aksi <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-3 mt-1">
                            <div class="form-check form-check-inline border rounded-3 p-2 px-3 flex-fill m-0 bg-white" style="cursor: pointer;">
                                <input class="form-check-input" type="radio" name="action" id="actionPromote" value="promote" {{ old('action', 'promote') === 'promote' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small text-dark d-block" for="actionPromote" style="cursor: pointer;">
                                    <i class="bi bi-arrow-up-right-circle text-primary me-1"></i>Kenaikan Kelas
                                </label>
                            </div>
                            <div class="form-check form-check-inline border rounded-3 p-2 px-3 flex-fill m-0 bg-white" style="cursor: pointer;">
                                <input class="form-check-input" type="radio" name="action" id="actionGraduate" value="graduate" {{ old('action') === 'graduate' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold small text-dark d-block" for="actionGraduate" style="cursor: pointer;">
                                    <i class="bi bi-mortarboard text-success me-1"></i>Kelulusan (Alumni)
                                </label>
                            </div>
                        </div>
                        <div class="form-text small">Pilih Kelulusan untuk siswa tingkat akhir.</div>
                    </div>

                    {{-- KELAS TUJUAN --}}
                    <div class="col-md-4" id="targetClassContainer">
                        <label for="target_classroom_id" class="form-label fw-semibold text-dark small">
                            Kelas Tujuan <span class="text-danger">*</span>
                        </label>
                        <select name="target_classroom_id" id="target_classroom_id" class="form-select @error('target_classroom_id') is-invalid @enderror">
                            <option value="">-- Pilih Kelas Tujuan --</option>
                            @foreach($allClassrooms as $classroom)
                                <option value="{{ $classroom->id }}"
                                    data-name="{{ $classroom->name }}"
                                    data-level="{{ $classroom->level }}"
                                    data-year="{{ $classroom->academic_year }}"
                                    {{ old('target_classroom_id') == $classroom->id ? 'selected' : '' }}>
                                    Tingkat {{ $classroom->level }} - {{ $classroom->name }} ({{ $classroom->academic_year }})
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Rombel baru tempat siswa akan dipindahkan.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU DAFTAR SISWA & SELEKSI --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom border-light">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold text-dark mb-0 fs-6">
                                <i class="bi bi-people-fill me-2 text-primary"></i>2. Seleksi Siswa Rombel
                            </h5>
                            <span id="selectedBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                0 Siswa Terpilih
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0 d-flex justify-content-md-end gap-2">
                        <div class="input-group input-group-sm" style="max-width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="studentSearchInput" class="form-control bg-light border-start-0" placeholder="Cari nama / NIS...">
                        </div>
                        <button type="button" id="btnSelectAll" class="btn btn-sm btn-outline-primary px-2">
                            <i class="bi bi-check-all me-1"></i>Pilih Semua
                        </button>
                        <button type="button" id="btnDeselectAll" class="btn btn-sm btn-outline-secondary px-2">
                            Batal Semua
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">

                {{-- LOADING SPINNER --}}
                <div id="loadingIndicator" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Memuat data siswa...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Sedang memuat data siswa kelas...</p>
                </div>

                {{-- EMPTY STATE: BELUM PILIH KELAS ASAL --}}
                <div id="emptySelectClass" class="text-center py-5 {{ $selectedClassroom ? 'd-none' : '' }}">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 64px; height: 64px; background: #eff6ff; color: #3b82f6;">
                        <i class="bi bi-arrow-up-circle fs-2"></i>
                    </div>
                    <h6 class="fw-semibold text-dark">Pilih Kelas Asal Terlebih Dahulu</h6>
                    <p class="text-muted small mb-0" style="max-width: 380px; margin: 0 auto;">
                        Silakan tentukan kelas asal pada formulir di atas untuk menampilkan seluruh siswa yang terdaftar di kelas tersebut.
                    </p>
                </div>

                {{-- EMPTY STATE: KELAS TIDAK MEMILIKI SISWA --}}
                <div id="emptyNoStudents" class="text-center py-5 d-none">
                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 64px; height: 64px; background: #fef2f2; color: #ef4444;">
                        <i class="bi bi-person-x fs-2"></i>
                    </div>
                    <h6 class="fw-semibold text-dark">Tidak Ada Siswa Aktif</h6>
                    <p class="text-muted small mb-0" style="max-width: 380px; margin: 0 auto;">
                        Kelas asal yang dipilih saat ini tidak memiliki siswa terdaftar dengan status akademik aktif.
                    </p>
                </div>

                {{-- TABEL SISWA --}}
                <div id="tableContainer" class="table-responsive {{ $selectedClassroom && $students->count() > 0 ? '' : 'd-none' }}">
                    <table class="table table-hover align-middle mb-0" id="studentsTable">
                        <thead class="table-light text-muted small text-uppercase">
                            <tr>
                                <th style="width: 48px;" class="text-center">
                                    <input type="checkbox" id="masterCheckbox" class="form-check-input" checked title="Pilih Semua">
                                </th>
                                <th style="width: 60px;">No</th>
                                <th>Nama Lengkap Siswa</th>
                                <th>NIS / NISN</th>
                                <th>L/P</th>
                                <th>Status Saat Ini</th>
                                <th>Tujuan Pemindahan</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            @if($selectedClassroom && $students->count() > 0)
                                @foreach($students as $index => $student)
                                    <tr class="student-row" data-name="{{ strtolower($student->user?->name ?? '') }}" data-nis="{{ $student->nis ?? '' }}">
                                        <td class="text-center">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="form-check-input student-checkbox" checked>
                                        </td>
                                        <td class="text-muted small">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shrink-0"
                                                     style="width: 34px; height: 34px; background: {{ $student->gender === 'P' ? '#ec4899' : '#3b82f6' }}; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($student->user?->name ?? 'S', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold text-dark d-block student-name">{{ $student->user?->name ?? 'Siswa #'.$student->id }}</span>
                                                    <span class="text-muted small">{{ $student->user?->email ?? '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="small">
                                            <span class="fw-medium text-dark">{{ $student->nis ?? '-' }}</span>
                                            <span class="text-muted d-block" style="font-size: 0.75rem;">NISN: {{ $student->nisn ?? '-' }}</span>
                                        </td>
                                        <td class="small">
                                            <span class="badge {{ $student->gender === 'P' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary' }}">
                                                {{ $student->gender === 'P' ? 'Perempuan' : 'Laki-laki' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                                                Aktif
                                            </span>
                                        </td>
                                        <td class="target-indicator small text-muted">
                                            <span class="target-label">Mengikuti Pengaturan di Atas</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="card-footer bg-white py-3 border-top border-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="small text-muted" id="selectionSummaryText">
                    Silakan tentukan rombel dan centang siswa yang berhak naik kelas.
                </div>
                <div>
                    <button type="button" id="btnOpenConfirmModal" class="btn btn-primary py-2 px-4 shadow-sm" disabled>
                        <i class="bi bi-arrow-repeat me-1"></i>Proses Kenaikan Kelas
                    </button>
                </div>
            </div>
        </div>

        {{-- ===== MODAL KONFIRMASI PROTEKTIF ===== --}}
        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-bottom border-light">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 36px; height: 36px; background: #eff6ff; color: #2563eb;">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <h5 class="modal-title fw-bold text-dark fs-6" id="confirmModalLabel">
                                Konfirmasi Eksekusi Massal
                            </h5>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary small mb-3">
                            Mohon pastikan rincian berikut sudah tepat sebelum melanjutkan proses perubahan data:
                        </p>
                        <div class="bg-light p-3 rounded-3 mb-3 small">
                            <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                <span class="text-muted">Kelas Asal:</span>
                                <span class="fw-bold text-dark" id="modalSourceClass">-</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                <span class="text-muted">Tipe Aksi:</span>
                                <span class="fw-bold text-primary" id="modalActionType">Kenaikan Kelas</span>
                            </div>
                            <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                <span class="text-muted">Tujuan / Status Baru:</span>
                                <span class="fw-bold text-success" id="modalTargetClass">-</span>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Jumlah Siswa Terpilih:</span>
                                <span class="fw-bold text-dark" id="modalStudentCount">0 Siswa</span>
                            </div>
                        </div>
                        <div class="alert alert-warning border-0 p-2 px-3 small d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-exclamation-triangle-fill text-warning shrink-0"></i>
                            <span>Siswa yang tidak dicentang akan tetap berada di kelas asalnya.</span>
                        </div>
                    </div>
                    <div class="modal-footer border-top border-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4" id="btnSubmitFinal">
                            <i class="bi bi-check2-circle me-1"></i>Ya, Jalankan Sekarang
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </form>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sourceClassSelect = document.getElementById('source_classroom_id');
    const targetClassSelect = document.getElementById('target_classroom_id');
    const targetContainer = document.getElementById('targetClassContainer');
    const actionPromote = document.getElementById('actionPromote');
    const actionGraduate = document.getElementById('actionGraduate');
    
    const loadingIndicator = document.getElementById('loadingIndicator');
    const emptySelectClass = document.getElementById('emptySelectClass');
    const emptyNoStudents = document.getElementById('emptyNoStudents');
    const tableContainer = document.getElementById('tableContainer');
    const studentTableBody = document.getElementById('studentTableBody');
    const masterCheckbox = document.getElementById('masterCheckbox');
    
    const selectedBadge = document.getElementById('selectedBadge');
    const selectionSummaryText = document.getElementById('selectionSummaryText');
    const btnOpenConfirmModal = document.getElementById('btnOpenConfirmModal');
    const btnSelectAll = document.getElementById('btnSelectAll');
    const btnDeselectAll = document.getElementById('btnDeselectAll');
    const searchInput = document.getElementById('studentSearchInput');

    // Modal elements
    const modalSourceClass = document.getElementById('modalSourceClass');
    const modalActionType = document.getElementById('modalActionType');
    const modalTargetClass = document.getElementById('modalTargetClass');
    const modalStudentCount = document.getElementById('modalStudentCount');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));

    // ── Toggle action (Promote vs Graduate) ──────────────────────────────────
    function handleActionChange() {
        if (actionGraduate.checked) {
            targetContainer.style.display = 'none';
            targetClassSelect.removeAttribute('required');
            targetClassSelect.value = '';
            btnOpenConfirmModal.innerHTML = '<i class="bi bi-mortarboard me-1"></i>Proses Kelulusan Siswa';
            updateTargetLabels('Status Baru: Alumni (Lulus)');
        } else {
            targetContainer.style.display = 'block';
            targetClassSelect.setAttribute('required', 'required');
            btnOpenConfirmModal.innerHTML = '<i class="bi bi-arrow-repeat me-1"></i>Proses Kenaikan Kelas';
            updateTargetLabelsFromSelect();
        }
        updateButtonState();
    }

    actionPromote.addEventListener('change', handleActionChange);
    actionGraduate.addEventListener('change', handleActionChange);

    targetClassSelect.addEventListener('change', function() {
        if (actionPromote.checked) {
            updateTargetLabelsFromSelect();
        }
        updateButtonState();
    });

    function updateTargetLabels(text) {
        document.querySelectorAll('.target-label').forEach(el => {
            el.textContent = text;
        });
    }

    function updateTargetLabelsFromSelect() {
        const selectedOpt = targetClassSelect.options[targetClassSelect.selectedIndex];
        const text = selectedOpt && selectedOpt.value ? '➔ ' + selectedOpt.text : 'Mengikuti Kelas Tujuan';
        updateTargetLabels(text);
    }

    // ── Load students on source class change ────────────────────────────────
    sourceClassSelect.addEventListener('change', function() {
        const classId = this.value;
        if (!classId) {
            tableContainer.classList.add('d-none');
            emptyNoStudents.classList.add('d-none');
            emptySelectClass.classList.remove('d-none');
            studentTableBody.innerHTML = '';
            updateCounters();
            return;
        }

        // Disable options in target class that match source class
        Array.from(targetClassSelect.options).forEach(opt => {
            opt.disabled = (opt.value === classId);
        });
        if (targetClassSelect.value === classId) {
            targetClassSelect.value = '';
        }

        // Fetch via AJAX
        loadingIndicator.classList.remove('d-none');
        emptySelectClass.classList.add('d-none');
        emptyNoStudents.classList.add('d-none');
        tableContainer.classList.add('d-none');

        fetch(`{{ url('/admin/classrooms') }}/${classId}/students`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            loadingIndicator.classList.add('d-none');
            if (data.success && data.students && data.students.length > 0) {
                renderStudents(data.students);
                tableContainer.classList.remove('d-none');
            } else {
                studentTableBody.innerHTML = '';
                emptyNoStudents.classList.remove('d-none');
            }
            updateCounters();
        })
        .catch(err => {
            console.error('Failed to load students:', err);
            loadingIndicator.classList.add('d-none');
            alert('Gagal memuat data siswa kelas. Silakan coba lagi.');
        });
    });

    function renderStudents(students) {
        studentTableBody.innerHTML = '';
        const targetText = actionGraduate.checked 
            ? 'Status Baru: Alumni (Lulus)' 
            : (targetClassSelect.selectedIndex > 0 ? '➔ ' + targetClassSelect.options[targetClassSelect.selectedIndex].text : 'Mengikuti Kelas Tujuan');

        students.forEach((student, index) => {
            const initial = (student.name || 'S').charAt(0).toUpperCase();
            const avatarBg = student.gender === 'P' ? '#ec4899' : '#3b82f6';
            const genderLabel = student.gender === 'P' ? 'Perempuan' : 'Laki-laki';
            const genderBadge = student.gender === 'P' ? 'bg-danger-subtle text-danger' : 'bg-primary-subtle text-primary';

            const tr = document.createElement('tr');
            tr.className = 'student-row';
            tr.dataset.name = (student.name || '').toLowerCase();
            tr.dataset.nis = student.nis || '';

            tr.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" name="student_ids[]" value="${student.id}" class="form-check-input student-checkbox" checked>
                </td>
                <td class="text-muted small">${index + 1}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                             style="width: 34px; height: 34px; background: ${avatarBg}; font-size: 0.8rem;">
                            ${initial}
                        </div>
                        <div>
                            <span class="fw-semibold text-dark d-block student-name">${student.name}</span>
                        </div>
                    </div>
                </td>
                <td class="small">
                    <span class="fw-medium text-dark">${student.nis}</span>
                    <span class="text-muted d-block" style="font-size: 0.75rem;">NISN: ${student.nisn}</span>
                </td>
                <td class="small">
                    <span class="badge ${genderBadge}">
                        ${genderLabel}
                    </span>
                </td>
                <td>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                        Aktif
                    </span>
                </td>
                <td class="target-indicator small text-muted">
                    <span class="target-label">${targetText}</span>
                </td>
            `;
            studentTableBody.appendChild(tr);
        });

        attachCheckboxListeners();
        masterCheckbox.checked = true;
    }

    function attachCheckboxListeners() {
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.addEventListener('change', function() {
                updateMasterCheckbox();
                updateCounters();
            });
        });
    }

    function updateMasterCheckbox() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        const checked = document.querySelectorAll('.student-checkbox:checked');
        masterCheckbox.checked = (checkboxes.length > 0 && checkboxes.length === checked.length);
    }

    masterCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            // Hanya toggle yang terlihat (jika sedang difilter)
            const row = cb.closest('tr');
            if (row.style.display !== 'none') {
                cb.checked = isChecked;
            }
        });
        updateCounters();
    });

    btnSelectAll.addEventListener('click', function() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = true);
        masterCheckbox.checked = true;
        updateCounters();
    });

    btnDeselectAll.addEventListener('click', function() {
        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
        masterCheckbox.checked = false;
        updateCounters();
    });

    // ── Live Client-side Search in Table ─────────────────────────────────────
    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        document.querySelectorAll('.student-row').forEach(row => {
            const name = row.dataset.name || '';
            const nis = row.dataset.nis || '';
            if (name.includes(term) || nis.includes(term)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // ── Counters and Button State ───────────────────────────────────────────
    function updateCounters() {
        const total = document.querySelectorAll('.student-checkbox').length;
        const checked = document.querySelectorAll('.student-checkbox:checked').length;

        selectedBadge.textContent = `${checked} Siswa Terpilih`;
        if (total > 0) {
            selectionSummaryText.innerHTML = `<strong>${checked}</strong> dari <strong>${total}</strong> siswa terpilih untuk diproses.`;
        } else {
            selectionSummaryText.textContent = 'Silakan tentukan rombel dan centang siswa yang berhak naik kelas.';
        }

        updateButtonState();
    }

    function updateButtonState() {
        const checked = document.querySelectorAll('.student-checkbox:checked').length;
        const hasSource = sourceClassSelect.value !== '';
        const isGraduate = actionGraduate.checked;
        const hasTarget = isGraduate || (targetClassSelect.value !== '' && targetClassSelect.value !== sourceClassSelect.value);

        btnOpenConfirmModal.disabled = !(hasSource && hasTarget && checked > 0);
    }

    // ── Open Confirmation Modal ─────────────────────────────────────────────
    btnOpenConfirmModal.addEventListener('click', function() {
        const sourceOpt = sourceClassSelect.options[sourceClassSelect.selectedIndex];
        const isGraduate = actionGraduate.checked;
        const targetOpt = targetClassSelect.options[targetClassSelect.selectedIndex];
        const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;

        modalSourceClass.textContent = sourceOpt ? sourceOpt.text : '-';
        modalActionType.textContent = isGraduate ? 'Kelulusan Siswa (Alumni)' : 'Kenaikan Kelas Baru';
        modalTargetClass.textContent = isGraduate ? 'Alumni (Status: Graduated)' : (targetOpt ? targetOpt.text : '-');
        modalStudentCount.textContent = `${checkedCount} Siswa`;

        confirmModal.show();
    });

    // Inisialisasi awal
    attachCheckboxListeners();
    handleActionChange();
    updateCounters();
});
</script>
@endpush
@endsection

