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
                <span class="text-muted small">Pengaturan Sistem</span>
            </div>
            <h1 class="fw-bold text-dark mb-0" style="font-size: 1.65rem; letter-spacing: -0.4px;">
                Pengaturan Sistem Presensi
            </h1>
            <p class="text-muted small mb-0 mt-1">
                Konfigurasi perimeter lokasi geofence sekolah, jadwal operasional jam masuk/pulang, dan profil institusi.
            </p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
            <span class="nav-status-pill">
                <span class="gps-dot"></span>
                <span>Konfigurasi Aktif</span>
            </span>
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
                <h6 class="mb-0 fw-bold">Gagal Menyimpan Pengaturan</h6>
            </div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===== NAVIGATION TABS ===== --}}
    <ul class="nav nav-pills settings-tabs mb-4 gap-2 border-bottom pb-3" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-3 px-4 py-2" id="btn-tab-location" data-bs-toggle="tab" data-bs-target="#tab-location" type="button" role="tab">
                <i class="bi bi-geo-alt-fill me-2"></i>Perimeter & Titik Lokasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 px-4 py-2" id="btn-tab-time" data-bs-toggle="tab" data-bs-target="#tab-time" type="button" role="tab">
                <i class="bi bi-clock-history me-2"></i>Jadwal & Jam Operasional
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-3 px-4 py-2" id="btn-tab-school" data-bs-toggle="tab" data-bs-target="#tab-school" type="button" role="tab">
                <i class="bi bi-building me-2"></i>Profil & Identitas Sekolah
            </button>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabsContent">

        <!-- ============================================== -->
        <!--               TAB 1: LOKASI SEKOLAH            -->
        <!-- ============================================== -->
        <div class="tab-pane fade show active" id="tab-location" role="tabpanel">
            <form action="{{ route('admin.update_location') }}" method="POST" id="locationForm">
                @csrf
                <div class="card border-0 shadow-sm overflow-hidden mb-4">
                    <div class="row g-0" style="min-height: 480px;">

                        {{-- LEFT: OpenStreetMap (Leaflet) Embed --}}
                        <div class="col-lg-7 position-relative p-0 border-end">
                            <div id="mapContainer" style="width:100%; height:100%; min-height:480px; position:relative; background:#f8fafc;">
                                <div id="map" style="width:100%; height:100%; min-height:480px; z-index:1;"></div>
                                {{-- Radius circle overlay label --}}
                                <div class="position-absolute bottom-0 inset-s-0 inset-e-0 m-3 px-3 py-2 rounded-3 bg-white border shadow-sm" style="z-index: 999;">
                                    <span class="text-muted small">Cakupan Radius: </span>
                                    <span class="text-primary fw-bold small" id="radiusLabel">{{ $radius }} meter</span>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: Form --}}
                        <div class="col-lg-5 d-flex flex-column justify-content-center bg-white">
                            <div class="p-4 p-xl-5">
                                <h5 class="fw-bold text-dark mb-4">
                                    <i class="bi bi-pin-map-fill me-2 text-primary"></i>Koordinat Geofence
                                </h5>

                                <div class="mb-3">
                                    <label for="lat" class="form-label text-dark small fw-semibold">Latitude</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-primary">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </span>
                                        <input type="text" class="form-control" id="lat" name="lat"
                                               value="{{ old('lat', $lat) }}" required placeholder="-6.200000" inputmode="decimal">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="long" class="form-label text-dark small fw-semibold">Longitude</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white text-primary">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </span>
                                        <input type="text" class="form-control" id="long" name="long"
                                               value="{{ old('long', $long) }}" required placeholder="106.816666" inputmode="decimal">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="radiusRange" class="form-label text-dark small fw-semibold mb-0"><i class="bi bi-radar me-1 text-primary"></i>Radius Presensi (Meter)</label>
                                        <span class="text-primary fw-bold small" id="radiusDisplay">{{ old('radius', $radius) }} m</span>
                                    </div>
                                    <input type="range" class="form-range mb-2" id="radiusRange"
                                           min="10" max="1000" step="10" value="{{ old('radius', $radius) }}" oninput="syncRadius(this.value)">
                                    <input type="number" class="form-control" id="radius" name="radius"
                                           value="{{ old('radius', $radius) }}" min="10" max="5000" required placeholder="100" oninput="syncRadiusFromInput(this.value)">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-outline-primary py-2 fw-medium shadow-sm" id="useMyLocation">
                                        <i class="bi bi-crosshair me-2"></i>Gunakan Lokasi Perangkat Saya
                                    </button>
                                    <button type="button" class="btn btn-primary py-2 fw-semibold shadow-sm" id="openLocationConfirmModal">
                                        <i class="bi bi-shield-check me-2"></i>Terapkan Perubahan Peta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="p-3 rounded-3 d-flex align-items-start gap-3 mb-4 bg-white border shadow-sm">
                <div class="shrink-0 rounded-circle d-flex align-items-center justify-content-center" style="width:36px;height:36px;background:#eff6ff;color:#2563eb;">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <h6 class="text-dark fw-bold mb-1 small">CATATAN GEOFENCING:</h6>
                    <p class="text-muted small mb-0">Perubahan titik koordinat dan radius akan langsung diverifikasi saat siswa atau guru melakukan presensi masuk di aplikasi mobile menggunakan rumus jarak Haversine.</p>
                </div>
            </div>
        </div>


        <!-- ============================================== -->
        <!--               TAB 2: JADWAL PRESENSI           -->
        <!-- ============================================== -->
        <div class="tab-pane fade" id="tab-time" role="tabpanel">
            <form action="{{ route('admin.update_attendance_settings') }}" method="POST" id="timeForm">
                @csrf
                
                <div class="row g-4 mb-4">
                    {{-- Card 1: Buka Presensi Masuk --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center d-flex flex-column">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width:52px;height:52px;background:#eff6ff;color:#2563eb;font-size:1.4rem;">
                                    <i class="bi bi-door-open"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Mulai Presensi Masuk</h6>
                                <p class="text-muted small mb-3 grow">Waktu paling awal tombol presensi masuk dibuka di mobile.</p>
                                
                                <input type="time" name="check_in_start" value="{{ old('check_in_start', $checkInStart) }}" 
                                       class="form-control form-control-lg text-center fw-bold fs-4" style="height: 54px;" required>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Batas Waktu Masuk Tepat Waktu --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center d-flex flex-column">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width:52px;height:52px;background:#ecfdf5;color:#059669;font-size:1.4rem;">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Batas Masuk Tepat Waktu</h6>
                                <p class="text-muted small mb-3 grow">Check-in sebelum jam ini dicatat status Hadir Tepat Waktu.</p>
                                
                                <input type="time" name="check_in_end" value="{{ old('check_in_end', $checkInEnd) }}" 
                                       class="form-control form-control-lg text-center fw-bold fs-4" style="height: 54px;" required>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Toleransi Terlambat --}}
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center d-flex flex-column">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width:52px;height:52px;background:#fffbeb;color:#d97706;font-size:1.4rem;">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Toleransi Keterlambatan</h6>
                                <p class="text-muted small mb-3 grow">Menit toleransi setelah batas jam masuk sebelum presensi ditutup.</p>
                                
                                <div class="input-group">
                                    <input type="number" name="late_tolerance" value="{{ old('late_tolerance', $lateTolerance) }}" min="0" max="180" 
                                           class="form-control form-control-lg text-center fw-bold fs-4 border-end-0" style="height: 54px;" required>
                                    <span class="input-group-text bg-white text-muted fw-semibold">Menit</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: Jam Pulang Minimal --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center d-flex flex-column">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width:52px;height:52px;background:#f0f9ff;color:#0284c7;font-size:1.4rem;">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Mulai Presensi Pulang</h6>
                                <p class="text-muted small mb-3 grow">Waktu paling awal pengguna dapat melakukan check-out kepulangan.</p>
                                
                                <input type="time" name="check_out_start" value="{{ old('check_out_start', $checkOutStart) }}" 
                                       class="form-control form-control-lg text-center fw-bold fs-4" style="height: 54px;" required>
                            </div>
                        </div>
                    </div>

                    {{-- Card 5: Batas Akhir Jam Pulang --}}
                    <div class="col-lg-6 col-md-12">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4 text-center d-flex flex-column">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                                     style="width:52px;height:52px;background:#fdf2f8;color:#db2777;font-size:1.4rem;">
                                    <i class="bi bi-door-closed"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Batas Akhir Presensi Pulang</h6>
                                <p class="text-muted small mb-3 grow">Waktu penutupan pencatatan check-out kepulangan harian.</p>
                                
                                <input type="time" name="check_out_end" value="{{ old('check_out_end', $checkOutEnd) }}" 
                                       class="form-control form-control-lg text-center fw-bold fs-4" style="height: 54px;" required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Timeline Illustration --}}
                <div class="card border-0 shadow-sm p-4 mb-4 bg-light">
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-diagram-3 text-primary me-2"></i>Alur Logika Jadwal Presensi Harian:</h6>
                    <div class="row g-3 text-center">
                        <div class="col-md-3">
                            <div class="p-3 bg-white rounded-3 border shadow-sm">
                                <span class="badge bg-primary-subtle text-primary mb-1">Pagi (Check-in Buka)</span>
                                <div class="fw-bold text-dark">{{ $checkInStart }}</div>
                                <span class="text-muted small" style="font-size: 0.75rem;">Tombol presensi aktif</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-white rounded-3 border shadow-sm">
                                <span class="badge bg-success-subtle text-success mb-1">Tepat Waktu</span>
                                <div class="fw-bold text-dark">s/d {{ $checkInEnd }}</div>
                                <span class="text-muted small" style="font-size: 0.75rem;">Status tercatat Hadir</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-white rounded-3 border shadow-sm">
                                <span class="badge bg-warning-subtle text-warning-emphasis mb-1">Toleransi Terlambat</span>
                                <div class="fw-bold text-dark">+{{ $lateTolerance }} Menit</div>
                                <span class="text-muted small" style="font-size: 0.75rem;">Status Hadir Terlambat</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 bg-white rounded-3 border shadow-sm">
                                <span class="badge bg-info-subtle text-info mb-1">Presensi Pulang</span>
                                <div class="fw-bold text-dark">{{ $checkOutStart }} - {{ $checkOutEnd }}</div>
                                <span class="text-muted small" style="font-size: 0.75rem;">Check-out kepulangan</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-check2-circle me-2"></i>Simpan Konfigurasi Jadwal
                    </button>
                </div>
            </form>
        </div>


        <!-- ============================================== -->
        <!--               TAB 3: IDENTITAS SEKOLAH         -->
        <!-- ============================================== -->
        <div class="tab-pane fade" id="tab-school" role="tabpanel">
            <form action="{{ route('admin.update_institution') }}" method="POST" id="institutionForm">
                @csrf
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 text-dark fw-bold">
                            <i class="bi bi-building text-primary me-2"></i>Profil & Identitas Sekolah / Lembaga
                        </h6>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label for="school_name" class="form-label text-dark fw-semibold small">
                                    Nama Resmi Institusi / Sekolah <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-mortarboard"></i></span>
                                    <input type="text" class="form-control border-start-0 @error('school_name') is-invalid @enderror"
                                           id="school_name" name="school_name" value="{{ old('school_name', $schoolName) }}"
                                           placeholder="Contoh: SMA Negeri 1 Jakarta" required>
                                </div>
                                <div class="form-text text-muted small">Tampil pada kop surat cetak dokumen laporan presensi dan identitas sistem.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="school_phone" class="form-label text-dark fw-semibold small">Nomor Telepon / Kontak Kantor</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control border-start-0"
                                           id="school_phone" name="school_phone" value="{{ old('school_phone', $schoolPhone) }}"
                                           placeholder="021-12345678">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="school_email" class="form-label text-dark fw-semibold small">Email Resmi Sekolah</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control border-start-0 @error('school_email') is-invalid @enderror"
                                           id="school_email" name="school_email" value="{{ old('school_email', $schoolEmail) }}"
                                           placeholder="info@sekolah.sch.id">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label for="school_address" class="form-label text-dark fw-semibold small">Alamat Lengkap Institusi</label>
                                <textarea class="form-control" id="school_address" name="school_address" rows="3"
                                          placeholder="Alamat jalan, nomor, kelurahan, kecamatan, kota/kabupaten...">{{ old('school_address', $schoolAddress) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i>Simpan Profil Sekolah
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

@push('modals')
{{-- ===== CONFIRM MODAL (LOKASI) ===== --}}
<div class="modal fade" id="locationConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:46px;height:46px;background:#eff6ff;color:#2563eb;">
                        <i class="bi bi-geo-alt-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-dark mb-0">Konfirmasi Titik Sekolah</h6>
                        <p class="text-muted small mb-0">Perubahan akan langsung berlaku untuk semua pengguna</p>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body py-3 px-4">
                <div class="p-3 rounded-3 bg-light border">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Latitude</span>
                        <span class="text-dark small fw-semibold" id="confirmLat">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Longitude</span>
                        <span class="text-dark small fw-semibold" id="confirmLong">-</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Radius</span>
                        <span class="text-primary small fw-bold" id="confirmRadius">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4 px-4">
                <button type="button" class="btn btn-sm btn-light border px-4" data-bs-dismiss="modal">Periksa Kembali</button>
                <button type="button" class="btn btn-sm btn-primary px-4 fw-semibold shadow-sm" id="confirmSubmitLocation">
                    <i class="bi bi-check-lg me-1"></i>Ya, Terapkan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
.settings-tabs .nav-link {
    color: #64748b;
    background: transparent;
    font-weight: 500;
    transition: all 0.2s ease;
    border: 1px solid #e2e8f0;
}
.settings-tabs .nav-link:hover {
    color: #2563eb;
    background: #eff6ff;
    border-color: #bfdbfe;
}
.settings-tabs .nav-link.active {
    background: #2563eb !important;
    color: #ffffff !important;
    border-color: #2563eb !important;
    box-shadow: 0 2px 6px rgba(37,99,235,0.25);
}

.leaflet-container { background: #f8fafc; font-family: inherit; }
.form-range::-webkit-slider-thumb { background: #2563eb; border: 2px solid #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.2); }
.form-range::-webkit-slider-runnable-track { background: #e2e8f0; height: 6px; border-radius: 4px; }
.form-range::-moz-range-thumb { background: #2563eb; border: 2px solid #ffffff; }
.form-range::-moz-range-track { background: #e2e8f0; height: 6px; }

@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin 0.8s linear infinite; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let map, marker, circle;

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    let hash = window.location.hash;
    if (hash) {
        let triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
        if (triggerEl) {
            new bootstrap.Tab(triggerEl).show();
        }
    }
    
    document.getElementById('lat').addEventListener('input', updateMapFromInput);
    document.getElementById('long').addEventListener('input', updateMapFromInput);

    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            history.pushState(null, null, e.target.dataset.bsTarget);
            if (e.target.dataset.bsTarget === '#tab-location' && map) {
                map.invalidateSize();
            }
        });
    });
});

function initMap() {
    const lat = document.getElementById('lat').value || -6.200000;
    const lng = document.getElementById('long').value || 106.816666;
    const radius = parseInt(document.getElementById('radius').value) || 100;

    map = L.map('map').setView([lat, lng], 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    const customIcon = L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background-color: #2563eb; width: 16px; height: 16px; border-radius: 50%; border: 2px solid #ffffff; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.35);"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    marker = L.marker([lat, lng], { draggable: true, icon: customIcon }).addTo(map);
    circle = L.circle([lat, lng], { color: '#2563eb', weight: 2, fillColor: '#2563eb', fillOpacity: 0.15, radius: radius }).addTo(map);

    marker.on('drag', function(e) {
        let latlng = marker.getLatLng();
        circle.setLatLng(latlng);
        document.getElementById('lat').value = latlng.lat.toFixed(6);
        document.getElementById('long').value = latlng.lng.toFixed(6);
    });

    map.on('click', function(e) {
        let latlng = e.latlng;
        marker.setLatLng(latlng);
        circle.setLatLng(latlng);
        document.getElementById('lat').value = latlng.lat.toFixed(6);
        document.getElementById('long').value = latlng.lng.toFixed(6);
    });
}

function updateMapFromInput() {
    const lat = parseFloat(document.getElementById('lat').value);
    const lng = parseFloat(document.getElementById('long').value);
    if (lat && lng && !isNaN(lat) && !isNaN(lng) && map && marker && circle) {
        const latlng = [lat, lng];
        map.setView(latlng, map.getZoom());
        marker.setLatLng(latlng);
        circle.setLatLng(latlng);
    }
}

function syncRadius(val) {
    document.getElementById('radiusDisplay').textContent = val + ' m';
    document.getElementById('radiusLabel').textContent   = val + ' meter';
    document.getElementById('radius').value             = val;
    if (circle) circle.setRadius(val);
}

function syncRadiusFromInput(val) {
    const clamped = Math.min(Math.max(parseInt(val) || 10, 10), 5000);
    document.getElementById('radiusDisplay').textContent = clamped + ' m';
    document.getElementById('radiusLabel').textContent   = clamped + ' meter';
    document.getElementById('radiusRange').value         = clamped;
    if (circle) circle.setRadius(clamped);
}

// LOCATION CONFIRM MODAL
document.getElementById('openLocationConfirmModal').addEventListener('click', function () {
    const lat    = document.getElementById('lat').value.trim();
    const long   = document.getElementById('long').value.trim();
    const radius = document.getElementById('radius').value.trim();

    if (!lat || !long || !radius) { alert('Harap isi koordinat secara lengkap!'); return; }

    document.getElementById('confirmLat').textContent    = lat;
    document.getElementById('confirmLong').textContent   = long;
    document.getElementById('confirmRadius').textContent = radius + ' meter';

    new bootstrap.Modal(document.getElementById('locationConfirmModal')).show();
});

document.getElementById('confirmSubmitLocation').addEventListener('click', function () {
    document.getElementById('locationForm').submit();
});

// GEOLOCATION
document.getElementById('useMyLocation').addEventListener('click', function () {
    if (!navigator.geolocation) { alert('Browser tidak mendukung pendeteksian lokasi.'); return; }

    const btn = this;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2 spin"></i>Mencari Koordinat GPS...';
    btn.disabled  = true;

    navigator.geolocation.getCurrentPosition(
        function (pos) {
            const newLat  = pos.coords.latitude.toFixed(6);
            const newLong = pos.coords.longitude.toFixed(6);
            document.getElementById('lat').value  = newLat;
            document.getElementById('long').value = newLong;

            if (map && marker && circle) {
                const latlng = [newLat, newLong];
                map.setView(latlng, 16);
                marker.setLatLng(latlng);
                circle.setLatLng(latlng);
            }
            btn.innerHTML = '<i class="bi bi-crosshair me-2"></i>Gunakan Lokasi Perangkat Saya';
            btn.disabled  = false;
        },
        function (err) {
            alert('Gagal mendeteksi lokasi: ' + err.message);
            btn.innerHTML = '<i class="bi bi-crosshair me-2"></i>Gunakan Lokasi Perangkat Saya';
            btn.disabled  = false;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
});
</script>
@endpush
