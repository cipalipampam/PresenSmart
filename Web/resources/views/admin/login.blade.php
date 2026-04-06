<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #4e9af1, #d0e6ff);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background-color: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }

        .login-card .btn-primary {
            background-color: #4e9af1;
            border-color: #4e9af1;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .login-card .btn-primary:hover {
            background-color: #3d85d8;
            border-color: #3d85d8;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
            background-color: #4e9af1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }

        .brand-logo i {
            font-size: 2rem;
            color: #fff;
        }
    </style>
    <!-- Optional: jika ingin pakai ikon dari Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
</head>

<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-logo">
                <i class="bi bi-shield-lock-fill"></i>
            </div>
            <h3 class="fw-bold text-primary">Admin Login</h3>
        </div>
        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" class="form-control form-control-lg" id="email" name="email" required autofocus />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Kata Sandi</label>
                <input type="password" class="form-control form-control-lg" id="password" name="password" required />
            </div>
            @if ($errors->any())
                <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
            @endif
            <button type="submit" class="btn btn-primary btn-lg w-100">
                Masuk
            </button>
        </form>
        {{-- <div class="text-center mt-3">
            <small class="text-muted">&copy; {{ date('Y') }} Nama Perusahaan Anda</small>
        </div> --}}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
