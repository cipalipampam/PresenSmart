@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Presensi</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Informasi Pengguna</h5>
                    <table class="table">
                        <tr>
                            <th>Nama</th>
                            <td>{{ $presensi->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $presensi->user->email }}</td>
                        </tr>
                        <tr>
                            <th>NISN</th>
                            <td>{{ $presensi->user->nisn }}</td>
                        </tr>
                        <tr>
                            <th>Kelas</th>
                            <td>{{ $presensi->user->kelas ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Detail Presensi</h5>
                    <table class="table">
                        <tr>
                            <th>Waktu</th>
                            <td>{{ $presensi->waktu->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge 
                                    @if($presensi->status == 'hadir') badge-success
                                    @elseif($presensi->status == 'izin') badge-warning
                                    @elseif($presensi->status == 'sakit') badge-info
                                    @else badge-danger
                                    @endif">
                                    {{ ucfirst($presensi->status) }}
                                </span>
                            </td>
                        </tr>
                        @if($presensi->keterangan)
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $presensi->keterangan }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($presensi->bukti_url)
            <div class="row mt-4">
                <div class="col-12">
                    <h5>Bukti</h5>
                    <div class="text-center">
                        <img src="{{ $presensi->bukti_url }}" alt="Bukti Presensi" class="img-fluid" style="max-height: 400px;">
                    </div>
                </div>
            </div>
            @endif

            <div class="row mt-4">
                <div class="col-12">
                    <a href="{{ route('admin.absen') }}" class="btn btn-secondary">Kembali</a>
                    <a href="{{ route('admin.absen.edit', $presensi->id) }}" class="btn btn-warning">Edit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 