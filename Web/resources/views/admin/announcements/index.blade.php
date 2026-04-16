@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    {{-- HEADER --}}
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold text-white mb-1">Pengumuman Board</h2>
            <p class="text-white-50 small mb-0">Kelola informasi, timeline, dan pengumuman untuk ditampilkan ke mobile siswa & staf.</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary fw-semibold px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal" style="background:linear-gradient(135deg,#06b6d4,#0d9488); border:none;">
                <i class="bi bi-plus-circle me-1"></i> Buat Pengumuman Baru
            </button>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success d-flex align-items-center mb-4 rounded-3">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- CONTENT --}}
    <div class="card glass border-0 shadow-lg" style="background:rgba(30,41,59,0.5);border:1px solid rgba(255,255,255,0.05)!important;border-radius:16px;">
        <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle mb-0 text-white">
                <thead style="background:rgba(255,255,255,0.05);">
                    <tr>
                        <th class="py-3 px-4 fw-semibold text-white-50" style="width: 50px;">#</th>
                        <th class="py-3 px-4 fw-semibold text-white-50">Judul Pengumuman</th>
                        <th class="py-3 px-4 fw-semibold text-white-50">Konten Singkat</th>
                        <th class="py-3 px-4 fw-semibold text-white-50">Status</th>
                        <th class="py-3 px-4 fw-semibold text-white-50 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($announcements as $index => $announcement)
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td class="py-3 px-4 text-white-50">{{ $index + 1 }}</td>
                        <td class="py-3 px-4 fw-medium">{{ $announcement->title }}</td>
                        <td class="py-3 px-4 text-white-50 text-truncate" style="max-width: 200px;">{{ $announcement->content ?? '-' }}</td>
                        <td class="py-3 px-4">
                            @if($announcement->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2"><i class="bi bi-broadcast me-1"></i>aktif</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2"><i class="bi bi-eye-slash text-danger me-1"></i>mati</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-end">
                            <button class="btn btn-sm btn-outline-info border-0 rounded-circle me-1" 
                                data-bs-toggle="modal" data-bs-target="#editModal{{ $announcement->id }}" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" 
                                data-delete-url="{{ route('admin.announcements.destroy', $announcement->id) }}"
                                data-delete-name="{{ $announcement->title }}" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>

                    @push('modals')
                    {{-- EDIT MODAL --}}
                    <div class="modal fade" id="editModal{{ $announcement->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content glass border-0 shadow-lg">
                                <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square text-cyan me-2"></i>Edit Pengumuman</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label text-white-50 small">Judul <span class="text-danger">*</span></label>
                                            <input type="text" name="title" class="form-control" style="background:rgba(15,23,42,0.7);color:#fff;border:1px solid rgba(255,255,255,0.1);" value="{{ $announcement->title }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-white-50 small">Isi Konten</label>
                                            <textarea name="content" class="form-control" rows="4" style="background:rgba(15,23,42,0.7);color:#fff;border:1px solid rgba(255,255,255,0.1);">{{ $announcement->content }}</textarea>
                                        </div>
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" id="isActiveEdit{{ $announcement->id }}" {{ $announcement->is_active ? 'checked' : '' }} value="1">
                                            <label class="form-check-label text-white" for="isActiveEdit{{ $announcement->id }}">Tampilkan di Mobile App (Aktif)</label>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn text-white-50" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary px-4 border-0" style="background:linear-gradient(135deg,#06b6d4,#0d9488);">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endpush

                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center text-white-50">
                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                            Belum ada pengumuman yang dibuat.<br>
                            Tekan tombol <strong>"Buat Pengumuman Baru"</strong> di atas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('modals')
{{-- CREATE MODAL --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass border-0 shadow-lg">
            <form action="{{ route('admin.announcements.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-plus-circle text-cyan me-2"></i>Buat Pengumuman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white-50 small">Judul <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" style="background:rgba(15,23,42,0.7);color:#fff;border:1px solid rgba(255,255,255,0.1);" placeholder="Contoh: Upacara Bendera Senin" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50 small">Isi Konten</label>
                        <textarea name="content" class="form-control" rows="4" style="background:rgba(15,23,42,0.7);color:#fff;border:1px solid rgba(255,255,255,0.1);" placeholder="Beri rincian bila dirasa perlu.."></textarea>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCreate" checked value="1">
                        <label class="form-check-label text-white" for="isActiveCreate">Tampilkan di Mobile App (Aktif)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn text-white-50" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 border-0" style="background:linear-gradient(135deg,#06b6d4,#0d9488);">Terbitkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
