@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">

    {{-- ===== PAGE HEADER ===== --}}
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold text-white mb-1">General Attendance Settings</h2>
            <p class="text-white-50 small mb-0">Unified dashboard: Configure location geofencing and time tolerance thresholds.</p>
        </div>
        <div class="col-auto">
            <div class="d-inline-flex align-items-center px-3 py-2 rounded-pill border"
                 style="background:rgba(16,185,129,0.08);border-color:rgba(16,185,129,0.25)!important;">
                <span class="gps-dot me-2"></span>
                <span class="fw-semibold small" style="color:#10b981;">Module Active</span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert border-0 d-flex align-items-center mb-4" role="alert"
             style="background:rgba(16,185,129,0.1);color:#10b981;border:1px solid rgba(16,185,129,0.2)!important;border-radius:10px;">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="filter:invert(1);opacity:0.5;"></button>
        </div>
    @endif

    {{-- ===== NAVIGATION TABS ===== --}}
    <ul class="nav nav-pills settings-tabs mb-4 gap-3 border-bottom border-white border-opacity-10 pb-3" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="btn-tab-location" data-bs-toggle="tab" data-bs-target="#tab-location" type="button" role="tab">
                <i class="bi bi-geo-alt-fill me-2"></i>School Location Border
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="btn-tab-time" data-bs-toggle="tab" data-bs-target="#tab-time" type="button" role="tab">
                <i class="bi bi-stopwatch-fill me-2"></i>Time Rules
            </button>
        </li>
    </ul>

    <div class="tab-content" id="settingsTabsContent">

        <!-- ============================================== -->
        <!--               TAB 1: LOCATION                  -->
        <!-- ============================================== -->
        <div class="tab-pane fade show active" id="tab-location" role="tabpanel">
            <form action="{{ route('admin.update_location') }}" method="POST" id="locationForm">
                @csrf
                <div class="card glass border-0 shadow-lg overflow-hidden mb-4">
                    <div class="row g-0" style="min-height:480px;">

                        {{-- LEFT: OpenStreetMap (Leaflet) Embed --}}
                        <div class="col-lg-7 position-relative p-0 border-end border-white border-opacity-10">
                            <div id="mapContainer" style="width:100%;height:100%;min-height:480px;position:relative;background:#1e293b;">
                                <div id="map" style="width:100%; height:100%; min-height:480px; z-index:1;"></div>
                                {{-- Radius circle overlay label --}}
                                <div class="position-absolute bottom-0 start-0 m-3 px-3 py-2 rounded-2"
                                     style="background:rgba(15,23,42,0.85);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.1);z-index:999;">
                                    <span class="text-white-50 small">Radius: </span>
                                    <span class="text-cyan fw-bold small" id="radiusLabel">{{ $radius }} m</span>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT: Form --}}
                        <div class="col-lg-5 d-flex flex-column justify-content-center">
                            <div class="p-5">
                                <h5 class="fw-bold text-white mb-4">
                                    <i class="bi bi-pin-map-fill me-2 text-cyan"></i>Coordinate Configuration
                                </h5>

                                <div class="mb-3">
                                    <label for="lat" class="form-label text-white-50 small fw-semibold">Latitude</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light-soft text-white-50">
                                            <i class="bi bi-geo-alt-fill" style="color:#06b6d4;"></i>
                                        </span>
                                        <input type="text" class="form-control" id="lat" name="lat"
                                               value="{{ old('lat', $lat) }}" required placeholder="-6.1754" inputmode="decimal">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="long" class="form-label text-white-50 small fw-semibold">Longitude</label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light-soft text-white-50">
                                            <i class="bi bi-geo-alt-fill" style="color:#06b6d4;"></i>
                                        </span>
                                        <input type="text" class="form-control" id="long" name="long"
                                               value="{{ old('long', $long) }}" required placeholder="106.8272" inputmode="decimal">
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="radius" class="form-label text-white-50 small fw-semibold d-flex justify-content-between">
                                        <span><i class="bi bi-radar me-1"></i>Radius Coverage (Meters)</span>
                                        <span class="text-cyan fw-bold" id="radiusDisplay">{{ old('radius', $radius) }} m</span>
                                    </label>
                                    <input type="range" class="form-range mb-2" id="radiusRange"
                                           min="10" max="1000" step="10" value="{{ old('radius', $radius) }}" oninput="syncRadius(this.value)">
                                    <input type="number" class="form-control mt-2" id="radius" name="radius"
                                           value="{{ old('radius', $radius) }}" min="10" max="5000" required placeholder="150" oninput="syncRadiusFromInput(this.value)">
                                </div>

                                <button type="button" class="btn w-100 mb-3 fw-medium border"
                                        id="useMyLocation" style="background:transparent;color:#06b6d4;border-color:rgba(6,182,212,0.35)!important;padding:.65rem;">
                                    <i class="bi bi-crosshair me-2"></i>Use My Location
                                </button>
                                <button type="button" class="btn w-100 fw-bold border-0" id="openLocationConfirmModal"
                                        style="background:linear-gradient(135deg,#06b6d4,#0d9488);color:#fff;padding:.7rem;font-size:.95rem;border-radius:10px;">
                                    <i class="bi bi-shield-check me-2"></i>Apply Map Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="p-4 rounded-3 d-flex align-items-start gap-3 mb-4" style="background:rgba(6,182,212,0.07);border:1px solid rgba(6,182,212,0.2);">
                <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center" style="width:38px;height:38px;background:rgba(6,182,212,0.15);">
                    <i class="bi bi-info-circle-fill" style="color:#06b6d4;"></i>
                </div>
                <div>
                    <p class="text-white fw-semibold mb-1 small">GEOLOCATION NOTICE:</p>
                    <p class="text-white-50 small mb-0">Coordinate changes directly impact distance validation on the teacher and student mobile apps.</p>
                </div>
            </div>
        </div>


        <!-- ============================================== -->
        <!--               TAB 2: ATTENDANCE TIME           -->
        <!-- ============================================== -->
        <div class="tab-pane fade" id="tab-time" role="tabpanel">
            <form action="{{ route('admin.update_attendance_settings') }}" method="POST" id="timeForm">
                @csrf
                
                <div class="row g-4 mb-4">
                    {{-- Card 1: Batas Waktu Masuk --}}
                    <div class="col-lg-4">
                        <div class="card glass border-0 shadow-lg h-100" style="background:rgba(30,41,59,0.5);border:1px solid rgba(255,255,255,0.05)!important;border-radius:16px;">
                            <div class="card-body p-4 p-xl-5 text-center d-flex flex-column">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" 
                                     style="width:64px;height:64px;background:rgba(16,185,129,0.1);color:#10b981;font-size:1.75rem;">
                                    <i class="bi bi-box-arrow-in-right"></i>
                                </div>
                                <h5 class="fw-bold text-white mb-2">Check-In Deadline</h5>
                                <p class="text-white-50 small mb-4 flex-grow-1">Maximum time string to be considered on time.</p>
                                
                                <input type="time" name="check_in_end" value="{{ $checkInEnd }}" 
                                       class="form-control form-control-lg text-center fw-bold fs-3" 
                                       style="background:rgba(15,23,42,0.7);color:#fff;border:1px solid rgba(255,255,255,0.1);border-radius:12px;height:64px;" required>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Toleransi Terlambat --}}
                    <div class="col-lg-4">
                        <div class="card glass border-0 shadow-lg h-100" style="background:rgba(30,41,59,0.5);border:1px solid rgba(255,255,255,0.05)!important;border-radius:16px;">
                            <div class="card-body p-4 p-xl-5 text-center d-flex flex-column">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" 
                                     style="width:64px;height:64px;background:rgba(245,158,11,0.1);color:#f59e0b;font-size:1.75rem;">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <h5 class="fw-bold text-white mb-2">Late Tolerance</h5>
                                <p class="text-white-50 small mb-4 flex-grow-1">Grace period duration after deadline. Valid but marked late.</p>
                                
                                <div class="input-group">
                                    <input type="number" name="late_tolerance" value="{{ $lateTolerance }}" min="0" max="180" 
                                           class="form-control form-control-lg text-center fw-bold fs-3 border-end-0" 
                                           style="background:rgba(15,23,42,0.7);color:#fff;border:1px solid rgba(255,255,255,0.1);border-radius:12px 0 0 12px;height:64px;" required>
                                    <span class="input-group-text border-start-0" style="background:rgba(15,23,42,0.7);color:#94a3b8;border:1px solid rgba(255,255,255,0.1);border-radius:0 12px 12px 0;">Mins</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Mulai Pulang --}}
                    <div class="col-lg-4">
                        <div class="card glass border-0 shadow-lg h-100" style="background:rgba(30,41,59,0.5);border:1px solid rgba(255,255,255,0.05)!important;border-radius:16px;">
                            <div class="card-body p-4 p-xl-5 text-center d-flex flex-column">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-4" 
                                     style="width:64px;height:64px;background:rgba(6,182,212,0.1);color:#06b6d4;font-size:1.75rem;">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>
                                <h5 class="fw-bold text-white mb-2">Check-Out Start</h5>
                                <p class="text-white-50 small mb-4 flex-grow-1">Time when the portal is opened for checkout.</p>
                                
                                <input type="time" name="check_out_start" value="{{ $checkOutStart }}" 
                                       class="form-control form-control-lg text-center fw-bold fs-3" 
                                       style="background:rgba(15,23,42,0.7);color:#fff;border:1px solid rgba(255,255,255,0.1);border-radius:12px;height:64px;" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-5">
                    <button type="submit" class="btn btn-primary px-5 py-3 shadow-lg border-0 fw-bold fs-6 rounded-pill"
                            style="background:linear-gradient(135deg,#06b6d4,#0d9488);">
                        <i class="bi bi-shield-lock-fill me-2"></i> Save Time Settings
                    </button>
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
        <div class="modal-content glass border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;background:rgba(6,182,212,0.12);">
                        <i class="bi bi-geo-alt-fill text-cyan fs-5"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-bold text-white mb-0">Confirm Location Changes</h6>
                        <p class="text-white-50 small mb-0">This will immediately impact all users</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="p-3 rounded-2 bg-light-soft border border-white border-opacity-5">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-50 small">Latitude</span>
                        <span class="text-white small fw-semibold" id="confirmLat">-</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-white-50 small">Longitude</span>
                        <span class="text-white small fw-semibold" id="confirmLong">-</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-white-50 small">Radius</span>
                        <span class="text-white small fw-semibold" id="confirmRadius">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn border-0 bg-light-soft text-white px-4" data-bs-dismiss="modal">Double Check</button>
                <button type="button" class="btn btn-primary px-5 fw-semibold border-0" id="confirmSubmitLocation" style="background:linear-gradient(135deg,#06b6d4,#0d9488);">
                    <i class="bi bi-shield-check me-2"></i>Yes, Apply
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
/* CSS TABS */
.settings-tabs .nav-link {
    color: #94a3b8;
    background: transparent;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}
.settings-tabs .nav-link:hover {
    color: #fff;
    background: rgba(255,255,255,0.05);
}
.settings-tabs .nav-link.active {
    background: rgba(6, 182, 212, 0.15) !important;
    color: #06b6d4 !important;
    border: 1px solid rgba(6, 182, 212, 0.3) !important;
    box-shadow: 0 4px 15px rgba(6, 182, 212, 0.1);
}

/* LEAFLET MAP */
.leaflet-container { background: #0f172a; font-family: inherit; }
.leaflet-control-zoom a { background-color: rgba(30, 41, 59, 0.9) !important; color: #cbd5e1 !important; border-color: rgba(255, 255, 255, 0.1) !important; }
.leaflet-control-zoom a:hover { background-color: #334155 !important; color: #fff !important; }
.leaflet-control-attribution { background-color: rgba(15, 23, 42, 0.7) !important; color: #94a3b8 !important; }
.leaflet-control-attribution a { color: #06b6d4 !important; }

/* RANGE SLIDER */
.form-range::-webkit-slider-thumb { background: #06b6d4; border: 2px solid #0f172a; box-shadow: 0 0 6px rgba(6,182,212,0.5); }
.form-range::-webkit-slider-runnable-track { background: linear-gradient(90deg, #06b6d4, #0d9488); height: 4px; border-radius: 4px; }
.form-range::-moz-range-thumb { background: #06b6d4; border: 2px solid #0f172a; }
.form-range::-moz-range-track { background: rgba(255,255,255,0.1); height: 4px; }

/* Focus helper for dark mode inputs */
input[type="time"]:focus, input[type="number"]:focus {
    outline: none;
    box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.2) !important;
}

/* Spin animation */
@keyframes spin { to { transform: rotate(360deg); } }
.spin { display: inline-block; animation: spin 0.8s linear infinite; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
// ==========================================
// 1. LEAFLET LOCATIONS MAP LOGIC
// ==========================================
let map, marker, circle;

document.addEventListener('DOMContentLoaded', function() {
    initMap();
    
    // Manual tab hash persistence
    let hash = window.location.hash;
    if (hash) {
        let triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
        if (triggerEl) {
            new bootstrap.Tab(triggerEl).show();
        }
    }
    
    // Event listener for manual input changes on Map
    document.getElementById('lat').addEventListener('input', updateMapFromInput);
    document.getElementById('long').addEventListener('input', updateMapFromInput);

    // Save hash on tab change
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            history.pushState(null, null, e.target.dataset.bsTarget);
            // Fix map rendering bug when map container is hidden during init
            if (e.target.dataset.bsTarget === '#tab-location' && map) {
                map.invalidateSize();
            }
        });
    });
});

function initMap() {
    const lat = document.getElementById('lat').value || -6.200000;
    const lng = document.getElementById('long').value || 106.816666;
    const radius = parseInt(document.getElementById('radius').value) || 150;

    map = L.map('map').setView([lat, lng], 16);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; OpenStreetMap &copy; CARTO',
        subdomains: 'abcd',
        maxZoom: 20
    }).addTo(map);

    const customIcon = L.divIcon({
        className: 'custom-div-icon',
        html: `<div style="background-color: #06b6d4; width: 14px; height: 14px; border-radius: 50%; box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.4), 0 0 0 10px rgba(6, 182, 212, 0.1);"></div>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    });

    marker = L.marker([lat, lng], { draggable: true, icon: customIcon }).addTo(map);
    circle = L.circle([lat, lng], { color: '#06b6d4', weight: 2, fillColor: '#06b6d4', fillOpacity: 0.12, radius: radius }).addTo(map);

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
    document.getElementById('radiusLabel').textContent   = val + ' m';
    document.getElementById('radius').value             = val;
    if (circle) circle.setRadius(val);
}

function syncRadiusFromInput(val) {
    const clamped = Math.min(Math.max(parseInt(val) || 10, 10), 5000);
    document.getElementById('radiusDisplay').textContent = val + ' m';
    document.getElementById('radiusLabel').textContent   = val + ' m';
    document.getElementById('radiusRange').value         = clamped;
    if (circle) circle.setRadius(clamped);
}

// LOCATION CONFIRM MODAL
document.getElementById('openLocationConfirmModal').addEventListener('click', function () {
    const lat    = document.getElementById('lat').value.trim();
    const long   = document.getElementById('long').value.trim();
    const radius = document.getElementById('radius').value.trim();

    if (!lat || !long || !radius) { alert('Please complete the Coordinate fields!'); return; }

    document.getElementById('confirmLat').textContent    = lat;
    document.getElementById('confirmLong').textContent   = long;
    document.getElementById('confirmRadius').textContent = radius + ' meters';

    new bootstrap.Modal(document.getElementById('locationConfirmModal')).show();
});

document.getElementById('confirmSubmitLocation').addEventListener('click', function () {
    document.getElementById('locationForm').submit();
});

// GEOLOCATION
document.getElementById('useMyLocation').addEventListener('click', function () {
    if (!navigator.geolocation) { alert('Browser does not support Geolocation.'); return; }

    const btn = this;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise me-2 spin"></i>Locating...';
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
            btn.innerHTML = '<i class="bi bi-crosshair me-2"></i>Use My Location';
            btn.disabled  = false;
        },
        function (err) {
            alert('Failed to get location: ' + err.message);
            btn.innerHTML = '<i class="bi bi-crosshair me-2"></i>Use My Location';
            btn.disabled  = false;
        },
        { enableHighAccuracy: true, timeout: 10000 }
    );
});
</script>
@endpush
