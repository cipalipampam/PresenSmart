@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold m-0">Edit User</h1>
        <small class="text-muted">{{ now()->format('l, j F Y') }}</small>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="container">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nama Lengkap *</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="nisn">NISN *</label>
                        <input type="text" class="form-control" id="nisn" name="nisn" value="{{ old('nisn', $user->nisn) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="role">Role *</label>
                        <select name="role" class="form-control" required>
                            <option value="">Pilih Role</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="siswa" {{ old('role', $user->role) == 'siswa' ? 'selected' : '' }}>Siswa</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="col-md-6 mb-3">
                        <label for="nis" class="form-label">NIS</label>
                        <input type="text" class="form-control" id="nis" name="nis" value="{{ old('nis', $user->nis) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input type="text" class="form-control" id="kelas" name="kelas" value="{{ old('kelas', $user->kelas) }}">
                    </div>

                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select class="form-control" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="laki_laki" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'laki_laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="perempuan" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="{{ old('tempat_lahir', $user->tempat_lahir) }}">
                    </div>

                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '') }}">
                    </div>

                    <div class="form-group">
                        <label for="agama">Agama</label>
                        <select class="form-control" id="agama" name="agama">
                            <option value="">Pilih Agama</option>
                            <option value="islam" {{ old('agama', $user->agama) == 'islam' ? 'selected' : '' }}>Islam</option>
                            <option value="kristen" {{ old('agama', $user->agama) == 'kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="katholik" {{ old('agama', $user->agama) == 'katholik' ? 'selected' : '' }}>Katholik</option>
                            <option value="hindu" {{ old('agama', $user->agama) == 'hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="buddha" {{ old('agama', $user->agama) == 'buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="konghucu" {{ old('agama', $user->agama) == 'konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $user->alamat) }}</textarea>
                        </div>

                    <div class="form-group">
                        <label for="no_telp">Nomor Telepon</label>
                        <input type="tel" class="form-control" id="no_telp" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}">
                        </div>

                    <div class="form-group">
                        <label for="pas_foto">Pas Foto</label>
                        @if($user->pas_foto)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $user->pas_foto) }}" alt="Pas Foto" style="max-width: 200px; max-height: 200px;">
                            </div>
                        @endif
                        <input type="file" class="form-control-file" id="pas_foto" name="pas_foto" accept="image/*">
                        <small class="form-text text-muted">Maks. 2MB, format gambar</small>
                        </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update User</button>
        </form>
    </div>
@endsection
