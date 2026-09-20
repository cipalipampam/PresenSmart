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
                <span class="text-muted small">Papan Pengumuman</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <h1 class="fw-bold text-dark mb-0" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                    Papan Pengumuman Sekolah
                </h1>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    {{ $totalCount ?? $announcements->total() }} Pengumuman
                </span>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Kelola maklumat penting, siaran informasi darurat, dan broadcast berkala langsung ke aplikasi mobile.
            </p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary shadow-sm py-2 px-3" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-megaphone-fill me-1"></i>Buat Pengumuman Baru
            </button>
        </div>
    </div>

    {{-- ===== MINI KPI SUMMARY CARDS ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Total Semua Pengumuman</span>
                        <h4 class="fw-bold text-dark mb-0 mt-1">{{ $totalCount ?? 0 }}</h4>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                        <i class="bi bi-card-heading fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Aktif di Aplikasi Mobile</span>
                        <h4 class="fw-bold text-success mb-0 mt-1">{{ $activeCount ?? 0 }}</h4>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #ecfdf5; color: #059669;">
                        <i class="bi bi-broadcast fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small d-block">Diarsipkan / Nonaktif</span>
                        <h4 class="fw-bold text-secondary mb-0 mt-1">{{ $inactiveCount ?? 0 }}</h4>
                    </div>
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 44px; height: 44px; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                        <i class="bi bi-eye-slash fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== SEARCH & FILTER ===== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('admin.announcements.index') }}" method="GET" class="row g-3 align-items-end js-live-filter">
                <div class="col-lg-6 col-md-12">
                    <label for="search" class="form-label small fw-semibold text-secondary">Pencarian Pengumuman</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" id="search" class="form-control border-start-0 ps-0"
                               placeholder="Cari judul atau isi konten pengumuman..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3 col-md-5">
                    <label for="status" class="form-label small fw-semibold text-secondary">Status Publikasi</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif di Mobile</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif / Arsip</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-7 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        <i class="bi bi-filter me-1"></i>Terapkan
                    </button>
                    @if(request()->filled('search') || request()->filled('status'))
                        <a href="{{ route('admin.announcements.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 d-flex align-items-center mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4" role="alert" style="background-color: #fff1f2; color: #9f1239; border-left: 4px solid #e11d48 !important;">
            <div class="d-flex align-items-center mb-1">
                <i class="bi bi-exclamation-octagon-fill me-2 fs-5 text-danger"></i>
                <h6 class="mb-0 fw-bold">Gagal Menyimpan Pengumuman</h6>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== DATA TABLE ===== --}}
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Judul & Ringkasan</th>
                        <th style="width: 170px;">Status Siaran</th>
                        <th style="width: 170px;">Tanggal Terbit</th>
                        <th class="text-end pe-4" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="announcement-table-body">
                    @forelse($announcements as $index => $announcement)
                    <tr>
                        <td class="ps-4 text-muted small">
                            {{ ($announcements->currentPage() - 1) * $announcements->perPage() + $loop->iteration }}
                        </td>
                        <td>
                            <div class="fw-semibold text-dark mb-1">{{ $announcement->title }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 480px;">
                                {{ $announcement->content ? Str::limit($announcement->content, 90) : 'Tidak ada rincian tambahan.' }}
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('admin.announcements.toggle', $announcement->id) }}" method="POST" class="d-inline">
                                @csrf
                                @if($announcement->is_active)
                                    <button type="submit" class="btn p-0 border-0 text-start" title="Klik untuk menonaktifkan">
                                        <span class="badge-status badge-present hover-lift">
                                            <i class="bi bi-broadcast me-1"></i>Aktif di Mobile
                                        </span>
                                    </button>
                                @else
                                    <button type="submit" class="btn p-0 border-0 text-start" title="Klik untuk mengaktifkan">
                                        <span class="badge-status badge-absent hover-lift">
                                            <i class="bi bi-eye-slash me-1"></i>Nonaktif
                                        </span>
                                    </button>
                                @endif
                            </form>
                        </td>
                        <td>
                            <span class="text-dark small d-block">
                                {{ $announcement->created_at ? $announcement->created_at->translatedFormat('d M Y') : '-' }}
                            </span>
                            <span class="text-muted small" style="font-size: 0.75rem;">
                                {{ $announcement->created_at ? $announcement->created_at->format('H:i') : '' }} WIB
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-1">
                                <button type="button" class="btn-action btn-action-view"
                                    data-bs-toggle="modal" data-bs-target="#viewModal{{ $announcement->id }}" title="Lihat Tampilan Penuh">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button type="button" class="btn-action btn-action-edit"
                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $announcement->id }}" title="Edit Pengumuman">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn-action btn-action-delete"
                                    data-delete-url="{{ route('admin.announcements.destroy', $announcement->id) }}"
                                    data-delete-name="{{ $announcement->title }}" title="Hapus Pengumuman">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    @push('modals')
                    {{-- VIEW MODAL --}}
                    <div class="modal fade" id="viewModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                <div class="modal-header border-0 pb-0 pt-4 px-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                                            <i class="bi bi-card-text fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="modal-title fw-bold text-dark mb-0">Rincian Pengumuman</h6>
                                            <p class="text-muted small mb-0">Tampilan persis seperti di aplikasi pengguna</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body py-3 px-4">
                                    <div class="p-3 rounded-3 bg-light border mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="text-muted small">
                                                <i class="bi bi-clock me-1"></i>{{ $announcement->created_at ? $announcement->created_at->translatedFormat('d F Y, H:i') : '-' }} WIB
                                            </span>
                                            @if($announcement->is_active)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-broadcast me-1"></i>Aktif di Mobile
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1" style="font-size: 0.72rem;">
                                                    <i class="bi bi-eye-slash me-1"></i>Nonaktif
                                                </span>
                                            @endif
                                        </div>
                                        <h5 class="fw-bold text-dark mb-2">{{ $announcement->title }}</h5>
                                        <div class="text-secondary small" style="white-space: pre-wrap; line-height: 1.6;">{{ $announcement->content ?? 'Tidak ada rincian konten.' }}</div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                    <button type="button" class="btn btn-sm btn-outline-secondary px-4" data-bs-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-sm btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $announcement->id }}">
                                        <i class="bi bi-pencil me-1"></i>Edit Konten
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- EDIT MODAL --}}
                    <div class="modal fade" id="editModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-0 pb-0 pt-4 px-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                                                <i class="bi bi-pencil-square fs-5"></i>
                                            </div>
                                            <div>
                                                <h6 class="modal-title fw-bold text-dark mb-0">Edit Pengumuman</h6>
                                                <p class="text-muted small mb-0">Ubah judul, isi, dan status tampil</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body py-3 px-4">
                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-semibold small">Judul Pengumuman <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" value="{{ old('title', $announcement->title) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-semibold small">Isi Konten</label>
                                            <textarea name="content" class="form-control" rows="4" placeholder="Tuliskan rincian pengumuman...">{{ old('content', $announcement->content) }}</textarea>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveEdit{{ $announcement->id }}" {{ $announcement->is_active ? 'checked' : '' }} value="1">
                                            <label class="form-check-label text-dark small fw-medium" for="isActiveEdit{{ $announcement->id }}">Tampilkan di Aplikasi Mobile (Aktif)</label>
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
                    @endpush

                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center text-muted">
                            <i class="bi bi-megaphone fs-1 d-block mb-3 opacity-50"></i>
                            <h6 class="text-dark fw-medium">Belum Ada Pengumuman</h6>
                            <p class="small mb-3">Tidak ditemukan pengumuman yang sesuai dengan kriteria pencarian.</p>
                            <button class="btn btn-sm btn-primary px-3" data-bs-toggle="modal" data-bs-target="#createModal">
                                <i class="bi bi-megaphone-fill me-1"></i>Buat Pengumuman Baru
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($announcements->hasPages())
            <div class="card-footer bg-white border-top p-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-muted small">
                    Menampilkan <strong class="text-dark">{{ $announcements->firstItem() ?? 0 }}-{{ $announcements->lastItem() ?? 0 }}</strong> dari total <strong class="text-dark">{{ $announcements->total() }}</strong> pengumuman
                </div>
                <div class="pagination-modern">
                    {{ $announcements->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function registerAnnouncementWebSocket() {
    if (typeof window.Echo === 'undefined') return;

    let refreshTimer;
    let refreshController;
    window.Echo.private('announcements')
        .listen('.AnnouncementChanged', () => {
            clearTimeout(refreshTimer);
            refreshTimer = setTimeout(refreshAnnouncementList, 150);
        });
}

async function refreshAnnouncementList() {
    refreshController?.abort();
    refreshController = new AbortController();

    try {
        const response = await fetch(window.location.href, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            signal: refreshController.signal,
        });
        if (!response.ok) throw new Error(`Announcement refresh failed: ${response.status}`);

        const html = await response.text();
        const nextDocument = new DOMParser().parseFromString(html, 'text/html');
        const currentBody = document.getElementById('announcement-table-body');
        const nextBody = nextDocument.getElementById('announcement-table-body');
        if (!currentBody || !nextBody) return;

        document.querySelectorAll('[id^="editModal"].show, [id^="viewModal"].show').forEach((modal) => {
            bootstrap.Modal.getInstance(modal)?.hide();
        });
        document.querySelectorAll('[id^="editModal"], [id^="viewModal"]').forEach((modal) => modal.remove());
        nextDocument.querySelectorAll('[id^="editModal"], [id^="viewModal"]').forEach((modal) => {
            document.body.appendChild(modal);
        });

        nextBody.classList.add('announcement-list-entering');
        currentBody.replaceWith(nextBody);
    } catch (error) {
        if (error.name !== 'AbortError') {
            console.error('Realtime announcement list refresh failed:', error);
        }
    }
}

if (typeof window.Echo !== 'undefined') {
    registerAnnouncementWebSocket();
} else {
    window.addEventListener('echo:ready', registerAnnouncementWebSocket, { once: true });
}
</script>
@endpush

@push('modals')
{{-- CREATE MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 44px; height: 44px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-megaphone-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0">Buat Pengumuman Baru</h6>
                            <p class="text-muted small mb-0">Informasi akan langsung disiarkan ke pengguna</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3 px-4">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Judul Pengumuman <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Jadwal Ujian Tengah Semester Genap" value="{{ old('title') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold small">Isi Konten</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Tuliskan detail pengumuman secara rinci...">{{ old('content') }}</textarea>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCreate" checked value="1">
                        <label class="form-check-label text-dark small fw-medium" for="isActiveCreate">Tampilkan di Aplikasi Mobile (Aktif)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-sm btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm">Terbitkan Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
