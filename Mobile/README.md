# Kelasentra Mobile

Aplikasi mobile Kelasentra untuk siswa, guru, dan staff sekolah. Aplikasi ini terhubung ke backend Laravel pada direktori `../Web` untuk akses jadwal, presensi, notifikasi, profil, dan informasi akademik.

## Menjalankan aplikasi

```bash
flutter pub get
flutter run
```

Untuk perangkat fisik atau emulator eksternal, atur alamat backend dengan `BACKEND_HOST`:

```bash
flutter run --dart-define=BACKEND_HOST=YOUR_LAN_IP
```

Lihat README di root repository untuk konfigurasi backend dan detail arsitektur.
