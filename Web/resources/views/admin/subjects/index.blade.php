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
                <span class="text-muted small">Mata Pelajaran</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="fw-bold text-dark mb-0" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                    Mata Pelajaran
                </h1>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    {{ $subjects->total() }} Mapel
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Kelola daftar mata pelajaran yang tersedia dan digunakan dalam jadwal kelas.
            </p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary shadow-sm py-2 px-3" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle-fill me-1"></i>Tambah Mata Pelajaran
            </button>
        </div>
    </div>

    {{-- ===== MINI KPI SUMMARY CARDS ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Total Mata Pelajaran</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1">{{ $subjects->total() }}</h4>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-journal-bookmark-fill fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Mapel Aktif</span>
                        <h4 class="fw-bold text-success mb-0 mt-1">{{ $subjects->getCollection()->where('is_active', true)->count() }}</h4>
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
                        <span class="text-muted small d-block">Digunakan dalam Jadwal</span>
                        <h4 class="fw-bold text-primary mb-0 mt-1">{{ $subjects->getCollection()->where('schedules_count', '>', 0)->count() }}</h4>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #f0f9ff; color: #0284c7;">
                        <i class="bi bi-calendar3 fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SEARCH & FILTER ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.subjects.index') }}" method="GET" class="row g-3 align-items-end js-live-filter">
                <div class="col-lg-12 col-md-12">
                    <label for="search" class="form-label small fw-semibold text-secondary">Cari Mata Pelajaran</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-0"
                               placeholder="Cari nama atau kode mata pelajaran..." value="{{ request('search') }}">
                    </div>
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
                <h6 class="mb-0 fw-bold">Gagal Menyimpan Data Mata Pelajaran</h6>
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
                        <th style="width: 120px;">Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th style="width: 160px;">Rumpun</th>
                        <th style="width: 140px;">Warna Aksen</th>
                        <th style="width: 130px;">Jadwal Terkait</th>
                        <th style="width: 100px;">Status</th>
                        <th class="text-end pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    @php
                        $clusterBadges = [
                            'mipa'   => ['label' => 'MIPA & Teknologi', 'class' => 'bg-primary-subtle text-primary border-primary-subtle'],
                            'bahasa' => ['label' => 'Bahasa', 'class' => 'bg-success-subtle text-success border-success-subtle'],
                            'ips'    => ['label' => 'IPS', 'class' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle'],
                            'umum'   => ['label' => 'Umum / Agama', 'class' => 'bg-secondary-subtle text-secondary border-secondary-subtle'],
                        ];
                        $clusterInfo = $clusterBadges[$subject->cluster] ?? ['label' => ucfirst($subject->cluster), 'class' => 'bg-light text-dark'];
                    @endphp
                    <tr>
                        <td class="ps-4 text-muted small">
                            {{ ($subjects->currentPage() - 1) * $subjects->perPage() + $loop->iteration }}
                        </td>
                        <td>
                            <code class="bg-secondary-subtle text-secondary px-2 py-1 rounded" style="font-size: 0.8rem;">
                                {{ $subject->code }}
                            </code>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle" style="width: 10px; height: 10px; background-color: {{ $subject->color_code }};"></div>
                                <span class="fw-semibold text-dark">{{ $subject->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge border {{ $clusterInfo['class'] }} px-2 py-1" style="font-size: 0.75rem;">
                                {{ $clusterInfo['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded border" style="width: 28px; height: 28px; background-color: {{ $subject->color_code }};"></div>
                                <code class="small text-muted">{{ $subject->color_code }}</code>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-{{ $subject->schedules_count > 0 ? 'primary' : 'secondary' }}-subtle text-{{ $subject->schedules_count > 0 ? 'primary' : 'secondary' }} border px-2 py-1" style="font-size: 0.78rem;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $subject->schedules_count }} Jadwal
                            </span>
                        </td>
                        <td>
                            @if($subject->is_active)
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
                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $subject->id }}"
                                    title="Edit Mata Pelajaran">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                @if($subject->schedules_count == 0)
                                    <button type="button" class="btn-action btn-action-delete"
                                        data-delete-url="{{ route('admin.subjects.destroy', $subject->id) }}"
                                        data-delete-name="{{ $subject->name }}" title="Hapus Mata Pelajaran">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn-action btn-action-delete opacity-50"
                                        disabled title="Tidak dapat dihapus — masih digunakan di {{ $subject->schedules_count }} jadwal">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8" class="py-5 text-center text-muted">
                            <i class="bi bi-journal-bookmark fs-1 d-block mb-3 opacity-50"></i>
                            <h6 class="text-dark fw-medium">Belum Ada Mata Pelajaran</h6>
                            <p class="small mb-3">Tidak ditemukan mata pelajaran yang sesuai dengan pencarian.</p>
                            <button class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="bi bi-plus-circle-fill me-1"></i>Tambah Mata Pelajaran
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small">
                    Menampilkan <strong class="text-dark">{{ $subjects->firstItem() ?? 0 }}-{{ $subjects->lastItem() ?? 0 }}</strong>
                    dari total <strong class="text-dark">{{ $subjects->total() }}</strong> mata pelajaran
                </div>
                <div class="pagination-modern">
                    {{ $subjects->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
        </div>
    </div>
</div>
@endsection

@push('modals')
{{-- EDIT MODALS (di-render di root body agar tidak kena stacking context/backdrop freeze) --}}
@foreach($subjects as $subject)
    <div class="modal fade" id="editModal{{ $subject->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </div>
                            <div>
                                <h6 class="modal-title fw-bold text-dark mb-0">Edit — {{ $subject->name }}</h6>
                                <p class="text-muted small mb-0">Perbarui data mata pelajaran</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body py-3 px-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-semibold small">Kode <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control text-uppercase"
                                       placeholder="MTK" value="{{ old('code', $subject->code) }}"
                                       maxlength="20" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-dark fw-semibold small">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       placeholder="Contoh: Matematika" value="{{ old('name', $subject->name) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-dark fw-semibold small">Rumpun <span class="text-danger">*</span></label>
                                <select name="cluster" class="form-select" required>
                                    <option value="mipa"   {{ old('cluster', $subject->cluster) === 'mipa'   ? 'selected' : '' }}>🔬 MIPA & Teknologi</option>
                                    <option value="bahasa" {{ old('cluster', $subject->cluster) === 'bahasa' ? 'selected' : '' }}>📚 Bahasa & Komunikasi</option>
                                    <option value="ips"    {{ old('cluster', $subject->cluster) === 'ips'    ? 'selected' : '' }}>🌍 Ilmu Pengetahuan Sosial (IPS)</option>
                                    <option value="umum"   {{ old('cluster', $subject->cluster) === 'umum'   ? 'selected' : '' }}>🕌 Umum, Agama & Pengembangan Diri</option>
                                </select>
                                <div class="form-text">Rumpun mapel digunakan untuk menentukan linieritas guru</div>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label text-dark fw-semibold small">Warna Aksen <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="color" name="color_code" class="form-control form-control-color border-end-0"
                                           style="max-width: 50px;"
                                           value="{{ old('color_code', $subject->color_code) }}"
                                           id="colorPickerEdit{{ $subject->id }}">
                                    <input type="text" class="form-control border-start-0 ps-2 color-hex-input"
                                           placeholder="#3B82F6" value="{{ old('color_code', $subject->color_code) }}"
                                           data-color-input="colorPickerEdit{{ $subject->id }}"
                                           maxlength="7" style="font-family: monospace;">
                                </div>
                                <div class="form-text">Pilih warna atau ketik kode hex, misal: #3B82F6</div>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-end">
                                <div class="form-check form-switch mb-1">
                                    <input class="form-check-input" type="checkbox" name="is_active"
                                           id="isActiveEdit{{ $subject->id }}"
                                           {{ old('is_active', $subject->is_active) ? 'checked' : '' }} value="1">
                                    <label class="form-check-label text-dark small fw-medium"
                                           for="isActiveEdit{{ $subject->id }}">
                                        Aktif
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-journal-bookmark-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0">Tambah Mata Pelajaran</h6>
                            <p class="text-muted small mb-0">Daftarkan mata pelajaran baru ke sistem</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3 px-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold small">Kode <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control text-uppercase"
                                   placeholder="MTK" value="{{ old('code') }}"
                                   maxlength="20" required>
                            <div class="form-text">Kode singkat unik, misal: MTK, B.IND</div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label text-dark fw-semibold small">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Contoh: Matematika" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-dark fw-semibold small">Rumpun <span class="text-danger">*</span></label>
                            <select name="cluster" class="form-select" required>
                                <option value="" disabled {{ old('cluster') ? '' : 'selected' }}>— Pilih Rumpun —</option>
                                <option value="mipa"   {{ old('cluster') === 'mipa'   ? 'selected' : '' }}>🔬 MIPA & Teknologi</option>
                                <option value="bahasa" {{ old('cluster') === 'bahasa' ? 'selected' : '' }}>📚 Bahasa & Komunikasi</option>
                                <option value="ips"    {{ old('cluster') === 'ips'    ? 'selected' : '' }}>🌍 Ilmu Pengetahuan Sosial (IPS)</option>
                                <option value="umum"   {{ old('cluster') === 'umum'   ? 'selected' : '' }}>🕌 Umum, Agama & Pengembangan Diri</option>
                            </select>
                            <div class="form-text">Rumpun mapel digunakan untuk menentukan linieritas guru</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-dark fw-semibold small">Warna Aksen <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" name="color_code" class="form-control form-control-color border-end-0"
                                       style="max-width: 50px;"
                                       value="{{ old('color_code', '#3B82F6') }}"
                                       id="colorPickerCreate">
                                <input type="text" class="form-control border-start-0 ps-2 color-hex-input"
                                       placeholder="#3B82F6" value="{{ old('color_code', '#3B82F6') }}"
                                       data-color-input="colorPickerCreate"
                                       maxlength="7" style="font-family: monospace;">
                            </div>
                            <div class="form-text">Pilih warna yang mudah dibedakan antar mata pelajaran di jadwal</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch mb-1">
                                <input class="form-check-input" type="checkbox" name="is_active"
                                       id="isActiveCreate" checked value="1">
                                <label class="form-check-label text-dark small fw-medium" for="isActiveCreate">
                                    Mata pelajaran aktif (bisa digunakan dalam jadwal)
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-sm btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Mapel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
// Sinkronisasi color picker <-> hex text input via event delegation
document.addEventListener('input', function (event) {
    if (event.target.classList && event.target.classList.contains('color-hex-input')) {
        const textInput = event.target;
        const pickerId = textInput.getAttribute('data-color-input');
        const picker = document.getElementById(pickerId);
        if (!picker) return;

        let val = textInput.value.trim();
        if (!val.startsWith('#') && /^[0-9A-Fa-f]{6}$/.test(val)) {
            val = '#' + val;
        }
        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
            picker.value = val;
        }
    } else if (event.target.matches('input[type="color"]')) {
        const picker = event.target;
        const textInput = document.querySelector(`[data-color-input="${picker.id}"]`);
        if (textInput) {
            textInput.value = picker.value.toUpperCase();
        }
    }
});
</script>
@endpush

