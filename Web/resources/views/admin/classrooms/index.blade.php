@extends('admin.layouts.app')

@section('content')
<div class="py-2">

    {{-- ===== BREADCRUMB & PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-house-door me-1"></i>Dashboard
                </a>
                <span class="text-muted small">/</span>
                <span class="text-muted small">Rombel & Kelas</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="fw-bold text-dark mb-0" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                    Rombel & Kelas
                </h1>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    {{ $classrooms->total() }} Rombel
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Kelola rombongan belajar, wali kelas, dan informasi akademik tiap kelas.
            </p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap justify-content-md-end gap-2">
            <a href="{{ route('admin.classrooms.promotion') }}" class="btn btn-outline-primary shadow-sm py-2 px-3">
                <i class="bi bi-arrow-up-right-circle-fill me-1"></i>Kenaikan Kelas Massal
            </a>
            <button class="btn btn-primary shadow-sm py-2 px-3" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle-fill me-1"></i>Tambah Rombel Baru
            </button>
        </div>
    </div>

    {{-- ===== MINI KPI SUMMARY CARDS ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Total Rombel</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1">{{ $classrooms->total() }}</h4>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-diagram-3-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Rombel Aktif</span>
                        <h4 class="fw-bold text-success mb-0 mt-1">{{ $classrooms->getCollection()->where('is_active', true)->count() }}</h4>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #ecfdf5; color: #059669;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Tidak Aktif</span>
                        <h4 class="fw-bold text-secondary mb-0 mt-1">{{ $classrooms->getCollection()->where('is_active', false)->count() }}</h4>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                        <i class="bi bi-x-circle fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SEARCH & FILTER ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.classrooms.index') }}" method="GET" class="row g-3 align-items-end js-live-filter">
                <div class="col-lg-4 col-md-12">
                    <label for="search" class="form-label small fw-semibold text-secondary">Cari Rombel</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-0"
                               placeholder="Nama rombel atau tahun ajaran..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-5">
                    <label for="level" class="form-label small fw-semibold text-secondary">Tingkat</label>
                    <select name="level" id="level" class="form-select">
                        <option value="">Semua Tingkat</option>
                        <option value="10" {{ request('level') == '10' ? 'selected' : '' }}>Kelas X</option>
                        <option value="11" {{ request('level') == '11' ? 'selected' : '' }}>Kelas XI</option>
                        <option value="12" {{ request('level') == '12' ? 'selected' : '' }}>Kelas XII</option>
                    </select>
                </div>
                <div class="col-lg-5 col-md-12">
                    <label for="major" class="form-label small fw-semibold text-secondary">Jurusan</label>
                    <input type="text" name="major" id="major" class="form-control"
                           placeholder="Contoh: IPA, IPS..." value="{{ request('major') }}">
                </div>
            </form>
        </div>
    </div>

    {{-- ===== FLASH MESSAGES ===== --}}
    @if (session('success'))
        <div class="alert alert-success border-0 d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger border-0 d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="bi bi-exclamation-octagon-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert" style="background-color: #fff1f2; color: #9f1239; border-left: 4px solid #e11d48 !important;">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-octagon-fill me-2 fs-5 text-danger"></i>
                <h6 class="mb-0 fw-bold">Gagal Menyimpan Data Rombel</h6>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== DATA TABLE ===== --}}
    <div class="js-live-results">
        <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Nama Rombel</th>
                        <th style="width: 90px;">Tingkat</th>
                        <th style="width: 120px;">Jurusan</th>
                        <th style="width: 80px;">Rombel</th>
                        <th style="width: 130px;">Tahun Ajaran</th>
                        <th>Wali Kelas</th>
                        <th style="width: 110px;">Jml Siswa</th>
                        <th style="width: 100px;">Status</th>
                        <th class="text-end pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classrooms as $classroom)
                    <tr>
                        <td class="ps-4 text-muted small">
                            {{ ($classrooms->currentPage() - 1) * $classrooms->perPage() + $loop->iteration }}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $classroom->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size: 0.78rem;">
                                Kelas {{ $classroom->level }}
                            </span>
                        </td>
                        <td class="text-dark small">{{ $classroom->major }}</td>
                        <td class="text-dark small text-center">{{ $classroom->section }}</td>
                        <td class="text-dark small">{{ $classroom->academic_year }}</td>
                        <td>
                            @if($classroom->homeroomTeacher)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-placeholder" style="width: 28px; height: 28px; font-size: 0.65rem;">
                                        {{ strtoupper(substr($classroom->homeroomTeacher->name, 0, 2)) }}
                                    </div>
                                    <span class="text-dark small">{{ $classroom->homeroomTeacher->name }}</span>
                                </div>
                            @else
                                <span class="text-muted small fst-italic">Belum ditentukan</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-people-fill me-1"></i>{{ $classroom->students_count }} Siswa
                            </span>
                        </td>
                        <td>
                            @if($classroom->is_active)
                                <span class="badge-status badge-present">
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                </span>
                            @else
                                <span class="badge-status badge-absent">
                                    <i class="bi bi-x-circle me-1"></i>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <button type="button" class="btn-action btn-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $classroom->id }}" title="Edit Rombel">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if($classroom->students_count == 0)
                                    <button type="button" class="btn-action btn-action-delete"
                                        data-delete-url="{{ route('admin.classrooms.destroy', $classroom->id) }}"
                                        data-delete-name="{{ $classroom->name }}" title="Hapus Rombel">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn-action btn-action-delete opacity-50"
                                        disabled title="Tidak dapat dihapus — masih ada {{ $classroom->students_count }} siswa">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="10" class="py-5 text-center text-muted">
                            <i class="bi bi-diagram-3 fs-1 d-block mb-3 opacity-50"></i>
                            <h6 class="text-dark fw-medium">Belum Ada Rombel</h6>
                            <p class="small mb-3">Tidak ditemukan rombongan belajar yang sesuai dengan kriteria pencarian.</p>
                            <button class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="bi bi-plus-circle-fill me-1"></i>Tambah Rombel Baru
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($classrooms->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small">
                    Menampilkan <strong class="text-dark">{{ $classrooms->firstItem() ?? 0 }}-{{ $classrooms->lastItem() ?? 0 }}</strong>
                    dari total <strong class="text-dark">{{ $classrooms->total() }}</strong> rombel
                </div>
                <div class="pagination-modern">
                    {{ $classrooms->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
        </div>
    </div>
</div>
@endsection

@push('modals')
{{-- EDIT MODALS (di-render di root body agar tidak kena stacking context/backdrop freeze) --}}
@foreach($classrooms as $classroom)
    <div class="modal fade" id="editModal{{ $classroom->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <form action="{{ route('admin.classrooms.update', $classroom->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold text-dark mb-0">Edit Rombel — {{ $classroom->name }}</h6>
                                <p class="text-muted small mb-0">Perbarui informasi rombongan belajar</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-3 px-4">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label text-dark fw-semibold small">Nama Rombel <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="Contoh: X IPA 1" value="{{ old('name', $classroom->name) }}" required>
                                <div class="form-text">Nama lengkap rombel, misal: X IPA 1, XI IPS 2</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-semibold small">Tingkat <span class="text-danger">*</span></label>
                                <select name="level" class="form-select" required>
                                    <option value="10" {{ old('level', $classroom->level) == 10 ? 'selected' : '' }}>Kelas X</option>
                                    <option value="11" {{ old('level', $classroom->level) == 11 ? 'selected' : '' }}>Kelas XI</option>
                                    <option value="12" {{ old('level', $classroom->level) == 12 ? 'selected' : '' }}>Kelas XII</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-semibold small">Jurusan <span class="text-danger">*</span></label>
                                <input type="text" name="major" class="form-control"
                                       placeholder="Contoh: IPA, IPS, TKJ" value="{{ old('major', $classroom->major) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-semibold small">Nomor Rombel <span class="text-danger">*</span></label>
                                <input type="text" name="section" class="form-control"
                                       placeholder="Contoh: 1, 2, A, B" value="{{ old('section', $classroom->section) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-semibold small">Tahun Ajaran <span class="text-danger">*</span></label>
                                <input type="text" name="academic_year" class="form-control"
                                       placeholder="Contoh: 2025/2026" value="{{ old('academic_year', $classroom->academic_year) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark fw-semibold small">Wali Kelas</label>
                                <select name="homeroom_teacher_id" class="form-select">
                                    <option value="">— Belum Ditentukan —</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}"
                                            {{ old('homeroom_teacher_id', $classroom->homeroom_teacher_id) == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" name="is_active"
                                           id="isActiveEdit{{ $classroom->id }}"
                                           {{ old('is_active', $classroom->is_active) ? 'checked' : '' }} value="1">
                                    <label class="form-check-label text-dark small fw-medium"
                                           for="isActiveEdit{{ $classroom->id }}">
                                        Rombel Aktif (tampil dan bisa digunakan di sistem)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 pb-4 px-4">
                        <button type="button" class="btn btn-sm btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

{{-- CREATE MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form action="{{ route('admin.classrooms.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-diagram-3-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0">Tambah Rombel Baru</h6>
                            <p class="text-muted small mb-0">Daftarkan rombongan belajar baru ke sistem</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3 px-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold small">Nama Rombel <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Contoh: X IPA 1" value="{{ old('name') }}" required>
                            <div class="form-text">Nama lengkap rombel, misal: X IPA 1, XI IPS 2</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">Tingkat <span class="text-danger">*</span></label>
                            <select name="level" class="form-select" required>
                                <option value="">— Pilih Tingkat —</option>
                                <option value="10" {{ old('level') == '10' ? 'selected' : '' }}>Kelas X</option>
                                <option value="11" {{ old('level') == '11' ? 'selected' : '' }}>Kelas XI</option>
                                <option value="12" {{ old('level') == '12' ? 'selected' : '' }}>Kelas XII</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">Jurusan <span class="text-danger">*</span></label>
                            <input type="text" name="major" class="form-control"
                                   placeholder="Contoh: IPA, IPS, TKJ" value="{{ old('major') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">Nomor Rombel <span class="text-danger">*</span></label>
                            <input type="text" name="section" class="form-control"
                                   placeholder="Contoh: 1, 2, A, B" value="{{ old('section') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold small">Tahun Ajaran <span class="text-danger">*</span></label>
                            <input type="text" name="academic_year" class="form-control"
                                   placeholder="Contoh: 2025/2026" value="{{ old('academic_year') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold small">Wali Kelas</label>
                            <select name="homeroom_teacher_id" class="form-select">
                                <option value="">— Belum Ditentukan —</option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                       id="isActiveCreate" checked value="1">
                                <label class="form-check-label text-dark small fw-medium" for="isActiveCreate">
                                    Rombel Aktif (tampil dan bisa digunakan di sistem)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-sm btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Rombel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
