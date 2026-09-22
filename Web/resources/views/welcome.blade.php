<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Kelasentra adalah sistem operasi akademik terpusat untuk sekolah.">
    <title>{{ config('app.name', 'Kelasentra') }} — Pusat Operasional Akademik Sekolah</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>
<body>
    <div class="welcome-page">
        <header class="welcome-header">
            <a class="welcome-brand" href="{{ url('/') }}" aria-label="{{ config('app.name', 'Kelasentra') }}">
                <span class="welcome-brand-mark" aria-hidden="true">KS</span>
                <span>Kelasentra</span>
            </a>
            @auth
                <a class="welcome-login-link" href="{{ route('admin.dashboard') }}">Buka Dashboard</a>
            @else
                <a class="welcome-login-link" href="{{ route('login') }}">Masuk Admin</a>
            @endauth
        </header>

        <main>
            <section class="welcome-hero" aria-labelledby="welcome-title">
                <div class="welcome-copy">
                    <div class="welcome-eyebrow"><span class="welcome-status-dot" aria-hidden="true"></span>Sistem operasi akademik terintegrasi</div>
                    <h1 id="welcome-title">Operasional akademik yang rapi.<br><span>Informasi yang selalu terhubung.</span></h1>
                    <p class="welcome-description">Kelasentra membantu sekolah mengelola data, jadwal, dan kehadiran siswa serta pegawai secara akurat, terpusat, dan real-time.</p>
                    <div class="welcome-actions">
                        @auth
                            <a class="welcome-button welcome-button-primary" href="{{ route('admin.dashboard') }}">Masuk ke Dashboard <span aria-hidden="true">→</span></a>
                        @else
                            <a class="welcome-button welcome-button-primary" href="{{ route('login') }}">Masuk sebagai Admin <span aria-hidden="true">→</span></a>
                        @endauth
                        <a class="welcome-button welcome-button-secondary" href="#fitur">Lihat fitur</a>
                    </div>
                    <ul class="welcome-trust-list" aria-label="Keunggulan Kelasentra">
                        <li>Data terpusat</li><li>Pembaruan real-time</li><li>Aman untuk sekolah</li>
                    </ul>
                </div>

                <div class="welcome-preview" aria-label="Ilustrasi dashboard Kelasentra">
                    <div class="welcome-preview-glow" aria-hidden="true"></div>
                    <div class="welcome-dashboard-card">
                        <div class="welcome-card-topbar">
                            <div class="welcome-card-brand"><span>KS</span> Kelasentra</div>
                            <div class="welcome-card-avatar">A</div>
                        </div>
                        <div class="welcome-card-content">
                            <div class="welcome-card-title-row">
                                <div><span>Ringkasan hari ini</span><strong>{{ now()->translatedFormat('l, d F Y') }}</strong></div>
                                <span class="welcome-live-pill"><i></i>Live</span>
                            </div>
                            <div class="welcome-stats-grid">
                                <article class="welcome-stat welcome-stat-present"><span>Hadir</span><strong>128</strong><small>+12 hari ini</small></article>
                                <article class="welcome-stat welcome-stat-pending"><span>Menunggu</span><strong>06</strong><small>Perlu ditinjau</small></article>
                                <article class="welcome-stat welcome-stat-late"><span>Terlambat</span><strong>04</strong><small>Dicatat otomatis</small></article>
                            </div>
                            <div class="welcome-activity-card">
                                <div class="welcome-activity-heading"><span>Aktivitas terbaru</span><span>09:42 WIB</span></div>
                                <div class="welcome-activity-row">
                                    <span class="welcome-person-initial">R</span>
                                    <div><strong>Rizky Pratama</strong><span>Check-in berhasil dicatat</span></div>
                                    <b>Hadir</b>
                                </div>
                                <div class="welcome-activity-row">
                                    <span class="welcome-person-initial welcome-person-initial-alt">S</span>
                                    <div><strong>Siti Aulia</strong><span>Pengajuan izin menunggu persetujuan</span></div>
                                    <b class="welcome-waiting">Menunggu</b>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="welcome-features" id="fitur" aria-labelledby="feature-title">
                <div class="welcome-section-heading">
                    <span>Dirancang untuk operasional harian</span>
                    <h2 id="feature-title">Satu sistem, informasi akademik yang selalu siap.</h2>
                </div>
                <div class="welcome-feature-grid">
                    <article class="welcome-feature-card"><span class="welcome-feature-icon" aria-hidden="true">✓</span><h3>Presensi terverifikasi</h3><p>Catat check-in dan check-out dengan bukti serta lokasi yang dapat ditinjau kembali.</p></article>
                    <article class="welcome-feature-card"><span class="welcome-feature-icon" aria-hidden="true">↻</span><h3>Informasi real-time</h3><p>Dashboard dan data administrasi ikut diperbarui saat ada aktivitas baru.</p></article>
                    <article class="welcome-feature-card"><span class="welcome-feature-icon" aria-hidden="true">▣</span><h3>Kontrol terpusat</h3><p>Kelola siswa, guru, rombel, jadwal, pengumuman, izin, dan laporan dari satu tempat.</p></article>
                </div>
            </section>
        </main>

        <footer class="welcome-footer">
            <span>&copy; {{ now()->year }} {{ config('app.name', 'Kelasentra') }}</span>
            <span>Sistem operasi akademik sekolah</span>
        </footer>
    </div>
</body>
</html>
