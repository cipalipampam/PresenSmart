<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login — PresenSmart</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <style>
        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: stretch;
            margin: 0;
            overflow: hidden;
        }

        /* ---- LEFT BRANDING PANEL ---- */
        .login-brand {
            width: 45%;
            background: linear-gradient(145deg, #0f172a 0%, #1e293b 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            position: relative;
            overflow: hidden;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        /* Dot grid background */
        .login-brand::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(6,182,212,0.15) 1px, transparent 1px);
            background-size: 28px 28px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 40%, transparent 100%);
        }

        /* Glow orbs */
        .login-brand::after {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(6,182,212,0.12) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 50%;
        }

        .brand-content {
            position: relative;
            z-index: 1;
            text-align: center;
            animation: fadeInUp 0.5s ease both;
        }

        .brand-logo-box {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #06b6d4, #0d9488);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 30px rgba(6,182,212,0.35);
            font-size: 1.15rem;
            font-weight: 900;
            color: white;
            letter-spacing: -1px;
        }

        .brand-title {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #06b6d4 0%, #10b981 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .brand-tagline {
            color: #94a3b8;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2.5rem;
        }

        .brand-features {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
            display: inline-block;
        }

        .brand-features li {
            color: #64748b;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.75rem;
        }

        .brand-features li i {
            color: #06b6d4;
            font-size: 1rem;
        }

        /* ---- RIGHT FORM PANEL ---- */
        .login-form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem;
            background-color: #0f172a;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 2.5rem;
            animation: fadeInUp 0.5s ease 0.1s both;
        }

        .login-card .form-label {
            color: #94a3b8;
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.4rem;
        }

        .login-card .form-control {
            background-color: rgba(255,255,255,0.05) !important;
            border: 1px solid rgba(255,255,255,0.1) !important;
            color: #f8fafc !important;
            border-radius: 8px;
            padding: 0.7rem 1rem;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .login-card .form-control:focus {
            background-color: rgba(255,255,255,0.08) !important;
            border-color: #06b6d4 !important;
            box-shadow: 0 0 0 3px rgba(6,182,212,0.15) !important;
            outline: none;
        }

        .login-card .form-control::placeholder { color: rgba(255,255,255,0.2) !important; }

        /* Password wrapper */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-right: 3rem;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0.2rem;
            transition: color 0.2s;
            line-height: 1;
            z-index: 5;
        }

        .password-toggle:hover { color: #06b6d4; }

        /* Submit button */
        .btn-login {
            background: linear-gradient(135deg, #06b6d4, #0d9488);
            border: none;
            color: white;
            font-weight: 700;
            font-size: 0.95rem;
            padding: 0.75rem;
            border-radius: 10px;
            width: 100%;
            transition: opacity 0.2s, box-shadow 0.2s, transform 0.15s;
            letter-spacing: 0.02em;
        }

        .btn-login:hover {
            opacity: 0.9;
            box-shadow: 0 6px 20px rgba(6,182,212,0.35);
            transform: translateY(-1px);
        }

        .btn-login:active { transform: translateY(0); }

        /* Alert */
        .login-alert {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 8px;
            color: #fca5a5;
            padding: 0.65rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        /* Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-brand { display: none; }
            body { align-items: center; justify-content: center; }
            .login-form-panel { padding: 1.5rem; }
        }
    </style>
</head>

<body>
    <!-- LEFT: Branding -->
    <div class="login-brand d-none d-md-flex">
        <div class="brand-content">
            <div class="brand-logo-box">PS</div>
            <div class="brand-title">PresenSmart</div>
            <p class="brand-tagline">Smart Attendance. Real Results.</p>
            <ul class="brand-features">
                <li><i class="bi bi-geo-alt-fill"></i> GPS-based attendance validation</li>
                <li><i class="bi bi-shield-check-fill"></i> Real-time & accurate presence data</li>
                <li><i class="bi bi-graph-up-arrow"></i> Centralized attendance reports</li>
                <li><i class="bi bi-people-fill"></i> Manage students & employees easily</li>
            </ul>
        </div>
    </div>

    <!-- RIGHT: Login Form -->
    <div class="login-form-panel">
        <div class="login-card">
            <div class="mb-4">
                <h4 class="fw-bold text-white mb-1">Welcome Back 👋</h4>
                <p class="text-secondary mb-0" style="font-size:0.875rem;">Sign in to PresenSmart admin panel</p>
            </div>

            @if ($errors->any())
                <div class="login-alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ old('email') }}" required autofocus
                           placeholder="admin@sekolah.com" />
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="password" name="password"
                               required placeholder="••••••••" />
                        <button type="button" class="password-toggle" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>

            <p class="text-center mt-4 mb-0" style="color:#334155; font-size:0.78rem;">
                &copy; {{ date('Y') }} PresenSmart. All rights reserved.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleIcon.className = isPassword ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    </script>
</body>

</html>
