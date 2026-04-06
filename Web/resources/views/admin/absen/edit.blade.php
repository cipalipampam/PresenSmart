@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Presensi</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.absen.update', $presensi->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nama Pengguna</label>
                            <input type="text" class="form-control" value="{{ $presensi->user->name }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Waktu Presensi</label>
                            <input type="text" class="form-control" value="{{ $presensi->waktu->format('d M Y H:i') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>NISN</label>
                            <input type="text" class="form-control" value="{{ $presensi->user->nisn }}" readonly>
                        </div>

                        <div class="form-group">
                            <label>Kelas</label>
                            <input type="text" class="form-control" value="{{ $presensi->user->kelas ?? '-' }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="status">Status Presensi</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="hadir" {{ $presensi->status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="izin" {{ $presensi->status == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ $presensi->status == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="alfa" {{ $presensi->status == 'alfa' ? 'selected' : '' }}>Alfa</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ $presensi->keterangan }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="bukti">Bukti (Opsional)</label>
                            <input type="file" name="bukti" id="bukti" class="form-control-file" accept="image/*,application/pdf">
                            <small class="form-text text-muted">Unggah bukti baru untuk mengganti bukti sebelumnya</small>
                        </div>

                        @if($presensi->bukti_url)
                        <div class="form-group">
                            <label>Bukti Saat Ini</label>
                            <div class="text-center">
                                <img src="{{ $presensi->bukti_url }}" alt="Bukti Presensi" class="img-fluid" style="max-height: 300px;">
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('admin.absen') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 