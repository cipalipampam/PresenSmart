<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Masuk Admin Portal — PresenSmart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            margin: 0;
            overflow-x: hidden;
        }

        /* ---- LEFT BRANDING PANEL ---- */
        .login-brand {
            width: 48%;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #1e40af 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 4rem;
            position: relative;
            overflow: hidden;
            color: #ffffff;
        }

        .login-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 24px 24px;
            opacity: 0.6;
        }

        .brand-logo-icon {
            width: 44px;
            height: 44px;
            background: #ffffff;
            color: #1e40af;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.15rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .feature-icon {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 1.05rem;
        }

        /* ---- RIGHT FORM PANEL ---- */
        .login-form-panel {
            width: 52%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2rem;
            background-color: #ffffff;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
        }

        .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18);
        }

        .btn-submit {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0.8rem;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
            color: #ffffff;
        }

        .demo-box {
            border-radius: 10px;
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            transition: all 0.2s;
        }

        .demo-box:hover {
            border-color: #93c5fd;
            background-color: #eff6ff;
        }

        @media (max-width: 991.98px) {
            .login-brand { display: none; }
            .login-form-panel { width: 100%; min-height: 100vh; }
        }
    </style>
</head>

<body>
    <!-- LEFT PANEL: Institutional Branding -->
    <div class="login-brand">
        <div class="position-relative" style="z-index: 2;">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="brand-logo-icon">PS</div>
                <div>
                    <h4 class="fw-bold mb-0 text-white" style="letter-spacing: -0.3px;">PresenSmart</h4>
                    <span class="small text-white-50">Enterprise Attendance & Monitoring</span>
                </div>
            </div>
        </div>

        <div class="position-relative" style="z-index: 2; margin: 3rem 0;">
            <span class="badge bg-white bg-opacity-20 text-white px-3 py-2 rounded-pill mb-3">
                <i class="bi bi-shield-lock me-1"></i>Portal Keamanan Instansi
            </span>
            <h2 class="fw-bold display-6 mb-3 text-white">
                Sistem Presensi Digital Terintegrasi & Akurat
            </h2>
            <p class="text-white-50 mb-4" style="line-height: 1.6;">
                Solusi presensi komprehensif yang menghubungkan kontrol admin, validasi koordinat GPS sekolah, surat keterangan sakit, dan transparansi laporan real-time.
            </p>

            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-geo-alt-fill text-info"></i></div>
                <div>
                    <h6 class="fw-bold text-white mb-0">Geofencing & Radius Perimeter</h6>
                    <small class="text-white-50">Validasi jarak pengguna secara otomatis dengan rumus Haversine saat check-in.</small>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-broadcast text-warning"></i></div>
                <div>
                    <h6 class="fw-bold text-white mb-0">Sinkronisasi Real-Time Reverb</h6>
                    <small class="text-white-50">Live dashboard counter kehadiran, persetujuan izin, dan broadcast papan pengumuman.</small>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="bi bi-file-earmark-bar-graph-fill text-success"></i></div>
                <div>
                    <h6 class="fw-bold text-white mb-0">Laporan Resmi Berkop Institusi</h6>
                    <small class="text-white-50">Cetak dokumen rekapan kehadiran dan ekspor multi-format (PDF, Excel, CSV).</small>
                </div>
            </div>
        </div>

        <div class="position-relative" style="z-index: 2;">
            <small class="text-white-50">&copy; {{ date('Y') }} PresenSmart. Seluruh hak cipta dilindungi.</small>
        </div>
    </div>

    <!-- RIGHT PANEL: Login Form -->
    <div class="login-form-panel">
        <div class="login-card">
            <div class="text-start mb-4">
                <div class="d-inline-flex p-2.5 rounded-3 bg-primary-subtle text-primary mb-3">
                    <i class="bi bi-person-fill-lock fs-4"></i>
                </div>
                <h3 class="fw-bold text-dark mb-1">Masuk ke Admin Portal</h3>
                <p class="text-muted small mb-0">Silakan masukkan kredensial akun administrator Anda untuk melanjutkan.</p>
            </div>

            {{-- SUCCESS FLASH MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success border-0 d-flex align-items-center mb-3 shadow-xs" style="border-radius: 8px; font-size: 0.88rem; background-color: #ecfdf5; color: #065f46;">
                    <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- ERROR FLASH MESSAGE --}}
            @if(session('error'))
                <div class="alert alert-danger border-0 d-flex align-items-center mb-3 shadow-xs" style="border-radius: 8px; font-size: 0.88rem; background-color: #fff1f2; color: #9f1239;">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if($errors->any())
                <div class="alert alert-danger border-0 mb-3 shadow-xs" style="border-radius: 8px; font-size: 0.85rem; background-color: #fff1f2; color: #9f1239; border-left: 4px solid #e11d48 !important;">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold text-secondary small">Alamat Email Administrator</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', 'admin@sekolah.com') }}"
                               placeholder="nama@sekolah.com" required autofocus autocomplete="email">
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label fw-semibold text-secondary small mb-0">Kata Sandi</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror"
                               id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary border-start-0 border bg-white" type="button" id="togglePassword" title="Lihat/Sembunyikan Sandi">
                            <i class="bi bi-eye text-muted" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" checked>
                        <label class="form-check-label text-secondary small" for="remember">
                            Ingat sesi di perangkat ini
                        </label>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-submit shadow-xs">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Portal
                    </button>
                </div>
            </form>

            {{-- DEMO CREDENTIAL BOX --}}
            <div class="demo-box p-3 mt-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-key-fill text-primary"></i>
                        <span class="fw-semibold text-dark small">Kredensial Demo Admin:</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary py-0.5 px-2" style="font-size: 0.75rem;" onclick="fillDemoCredentials()">
                        Gunakan Otomatis
                    </button>
                </div>
                <div class="text-muted small" style="font-size: 0.8rem;">
                    Email: <code class="text-primary">admin@sekolah.com</code> &bull; Sandi: <code class="text-primary">admin123</code>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password Visibility Toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput && eyeIcon) {
            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                eyeIcon.className = isPassword ? 'bi bi-eye-slash text-muted' : 'bi bi-eye text-muted';
            });
        }

        // Demo Credentials Auto-Fill
        function fillDemoCredentials() {
            const emailInput = document.getElementById('email');
            const passInput = document.getElementById('password');
            if (emailInput && passInput) {
                emailInput.value = 'admin@sekolah.com';
                passInput.value = 'admin123';
                passInput.focus();
            }
        }
    </script>
</body>

</html>
