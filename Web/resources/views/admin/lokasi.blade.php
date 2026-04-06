@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold m-0">Edit Lokasi Sekolah</h1>
        <small class="text-muted">{{ now()->format('l, j F Y') }}</small>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card card-custom shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-4">Pengaturan Koordinat Sekolah</h5>
                    <form action="{{ route('admin.update_lokasi') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="lat" class="form-label">Latitude</label>
                            <input type="text" class="form-control form-control-lg" id="lat" name="lat"
                                value="{{ old('lat', $lat) }}" required placeholder="e.g. -6.200000">
                        </div>

                        <div class="mb-3">
                            <label for="long" class="form-label">Longitude</label>
                            <input type="text" class="form-control form-control-lg" id="long" name="long"
                                value="{{ old('long', $long) }}" required placeholder="e.g. 106.816666">
                        </div>

                        <div class="mb-4">
                            <label for="radius" class="form-label">Radius (meter)</label>
                            <input type="number" class="form-control form-control-lg" id="radius" name="radius"
                                value="{{ old('radius', $radius) }}" required min="1" placeholder="e.g. 100">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-lg me-2">
                                <i class="bi bi-save me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
