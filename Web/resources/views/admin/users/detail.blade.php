@extends('admin.layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-custom shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Detail Pengguna</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            @php
                                $fotoUrl = $user->pas_foto ? (
                                    \Illuminate\Support\Facades\Storage::disk('public')->exists($user->pas_foto) 
                                        ? \Illuminate\Support\Facades\Storage::url($user->pas_foto)
                                        : asset('images/default-avatar.png')
                                ) : asset('images/default-avatar.png');
                            @endphp

                            @if($user->pas_foto)
                                <img src="{{ $fotoUrl }}" 
                                     alt="Pas Foto" 
                                     class="img-fluid rounded-circle mb-3" 
                                     style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 200px; height: 200px; margin: 0 auto;">
                                    <span class="h3 mb-0">Tidak Ada Foto</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Nama Lengkap</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>NISN</th>
                                    <td>{{ $user->nisn }}</td>
                                </tr>
                                <tr>
                                    <th>NIS</th>
                                    <td>{{ $user->nis ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td>{{ $user->kelas ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>
                                        <span class="badge 
                                            {{ $user->role == 'admin' ? 'bg-danger' : 'bg-success' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>
                                        @if($user->jenis_kelamin == 'laki_laki')
                                            <span class="badge bg-primary">Laki-laki</span>
                                        @elseif($user->jenis_kelamin == 'perempuan')
                                            <span class="badge bg-pink">Perempuan</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tempat, Tanggal Lahir</th>
                                    <td>
                                        {{ $user->tempat_lahir ? $user->tempat_lahir . ', ' : '' }}
                                        {{ $user->tanggal_lahir ? $user->tanggal_lahir->format('d M Y') : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Agama</th>
                                    <td>
                                        @switch($user->agama)
                                            @case('islam')
                                                Islam
                                                @break
                                            @case('kristen')
                                                Kristen
                                                @break
                                            @case('katholik')
                                                Katholik
                                                @break
                                            @case('hindu')
                                                Hindu
                                                @break
                                            @case('buddha')
                                                Buddha
                                                @break
                                            @case('konghucu')
                                                Konghucu
                                                @break
                                            @default
                                                -
                                        @endswitch
                                    </td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $user->alamat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor Telepon</th>
                                    <td>{{ $user->no_telp ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 