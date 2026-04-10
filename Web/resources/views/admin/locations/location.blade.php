@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="fw-bold text-white mb-0">Location Boundary</h2>
            <p class="text-white-50 small mb-0">Configure the geofencing coordinates and proximity radius for presence validation.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <div class="d-inline-flex align-items-center bg-light-soft rounded-pill px-4 py-2 border border-white border-opacity-10 shadow-sm">
                <i class="bi bi-geo-alt me-3 text-cyan"></i>
                <span class="text-white-50 fw-medium small">Active GPS Node</span>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card glass border-0 shadow-lg overflow-hidden">
                <div class="card-body p-5">
                    <div class="d-flex align-items-center mb-4">
                        <div class="icon-circle bg-primary-soft text-cyan me-3">
                            <i class="bi bi-pin-map-fill fs-4"></i>
                        </div>
                        <h5 class="fw-bold text-white m-0">Coordinate Configuration</h5>
                    </div>

                    <form action="{{ route('admin.update_location') }}" method="POST" class="row g-4">
                        @csrf
                        <div class="col-md-6">
                            <label for="lat" class="form-label text-white-50 small fw-semibold">Latitude</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-arrow-down-up"></i></span>
                                <input type="text" class="form-control" id="lat" name="lat"
                                    value="{{ old('lat', $lat) }}" required placeholder="-6.200000">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="long" class="form-label text-white-50 small fw-semibold">Longitude</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-arrow-left-right"></i></span>
                                <input type="text" class="form-control" id="long" name="long"
                                    value="{{ old('long', $long) }}" required placeholder="106.816666">
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="radius" class="form-label text-white-50 small fw-semibold">Proximity Threshold (Meters)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light-soft text-white-50"><i class="bi bi-radar"></i></span>
                                <input type="number" class="form-control" id="radius" name="radius"
                                    value="{{ old('radius', $radius) }}" required min="1" placeholder="100">
                            </div>
                            <div class="form-text text-white-25 mt-2">The maximum distance allowed for personnel to register their presence from the center point.</div>
                        </div>

                        <div class="col-12 mt-5">
                            <div class="d-flex gap-3 justify-content-end">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary border-0 bg-light-soft text-white px-4">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary px-5 shadow-sm border-0">
                                    <i class="bi bi-shield-check me-2"></i>Apply Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-4 p-4 rounded bg-light-soft border border-white border-opacity-5 d-flex align-items-center">
                <i class="bi bi-info-circle text-cyan fs-4 me-3"></i>
                <p class="text-white-50 small mb-0">Changes to the location boundary will affect all personnel immediately. Ensure coordinates are verified via Google Maps for accuracy.</p>
            </div>
        </div>
    </div>
</div>
@endsection
