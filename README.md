# PresenSmart

**PresenSmart** adalah platform presensi digital untuk sekolah atau instansi yang menghubungkan panel administrasi web dengan aplikasi mobile pengguna. Sistem ini menangani presensi masuk dan pulang, validasi lokasi berbasis radius, pengajuan izin/sakit dengan bukti, pengelolaan data anggota, laporan, pengumuman, dan pembaruan informasi melalui WebSocket.

> **Hadir lebih cerdas, kelola lebih mudah.**

## Daftar Isi

- [Gambaran Sistem](#gambaran-sistem)
- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Arsitektur Direktori](#arsitektur-direktori)
- [Prasyarat](#prasyarat)
- [Instalasi dan Menjalankan Backend](#instalasi-dan-menjalankan-backend)
- [Instalasi dan Menjalankan Mobile](#instalasi-dan-menjalankan-mobile)
- [Akun Demo](#akun-demo)
- [API Mobile](#api-mobile)
- [Alur Presensi](#alur-presensi)
- [Konfigurasi Penting](#konfigurasi-penting)
- [Pengujian](#pengujian)
- [Troubleshooting](#troubleshooting)
- [Catatan Keamanan](#catatan-keamanan)

## Gambaran Sistem

PresenSmart terdiri dari dua aplikasi yang memakai backend yang sama:

| Komponen | Peran | Teknologi |
| --- | --- | --- |
| `Web/` | REST API untuk mobile dan panel admin | Laravel 12, PHP 8.5+, Blade, Bootstrap, Vite |
| `Mobile/` | Aplikasi presensi untuk siswa, guru, dan staff | Flutter, Dart, Provider |

Autentikasi mobile menggunakan Laravel Sanctum dengan bearer token. Panel admin memakai autentikasi session dan hanya dapat diakses oleh role `admin`. Data utama disimpan dalam database relasional, sementara file bukti presensi disimpan pada disk `public`.

## Fitur

### Panel Admin Web

- Dashboard statistik presensi.
- CRUD siswa beserta NIS, NISN, kelas, kontak, dan profil.
- CRUD guru dan staff beserta NIP, jabatan, kontak, dan profil.
- Melihat, menambah, mengubah, dan menghapus catatan presensi.
- Filter laporan berdasarkan nama, tanggal, bulan, tahun, role, dan kelas.
- Menyetujui atau menolak pengajuan izin/sakit.
- Ekspor laporan ke Excel (`.xlsx`), CSV, PDF, atau ZIP berisi seluruh format.
- Mengelola pengumuman yang diterima aplikasi mobile.
- Mengatur lokasi sekolah, radius presensi, jam masuk, toleransi keterlambatan, dan jam pulang.
- Pembaruan statistik dan data tertentu secara real-time melalui Laravel Reverb.

### Aplikasi Mobile

- Login, logout, dan pemulihan sesi lokal.
- Dashboard status presensi, jadwal, statistik pribadi, dan pengumuman.
- Check-in dengan koordinat GPS dan validasi radius sekolah.
- Penandaan terlambat berdasarkan jadwal dan toleransi yang dikonfigurasi admin.
- Check-out setelah check-in berhasil.
- Pengajuan izin atau sakit dengan keterangan dan foto bukti.
- Riwayat presensi berdasarkan bulan dan tahun.
- Notifikasi pembaruan pengajuan secara real-time pada channel pengguna.
- Profil dinamis untuk siswa, guru, dan staff.
- Antarmuka dark theme dengan komponen glassmorphism dan animasi.

## Teknologi

### Backend dan Web

- Laravel `^12.0` dan PHP `^8.5`.
- Laravel Sanctum `^4.1` untuk token API.
- Laravel Reverb `^1.10` dan Pusher protocol untuk WebSocket.
- Spatie Laravel Permission `^7.2` untuk role dan permission.
- SQLite sebagai default development; MySQL, MariaDB, PostgreSQL, dan SQL Server tersedia pada konfigurasi database.
- Vite `^6.2.4`, Tailwind CSS `^4.0.0`, Bootstrap 5.3, dan Bootstrap Icons.
- DomPDF untuk PDF dan SimpleXLSXGen untuk Excel.

### Mobile

- Flutter dengan Dart SDK `^3.7.0`.
- Provider untuk state management.
- Dio untuk request HTTP dan interceptor autentikasi.
- Geolocator dan Flutter Map untuk lokasi.
- Image Picker untuk bukti foto.
- `dart_pusher_channels` untuk koneksi Reverb/WebSocket.
- Shared Preferences untuk menyimpan token dan data sesi.
- Flutter Animate, FL Chart, Intl, dan Cached Network Image untuk pengalaman aplikasi.

## Arsitektur Direktori

```text
PresenSmart/
├── README.md
├── Web/                         # Laravel API, admin portal, dan database
│   ├── app/
│   │   ├── Http/                # Controller, request, dan middleware
│   │   ├── Models/              # User, Student, Employee, Attendance, Setting
│   │   ├── Services/            # Logika bisnis API dan web
│   │   └── Events/              # Event broadcast real-time
│   ├── database/                # Migration, factory, dan seeder
│   ├── resources/views/         # Blade admin portal
│   ├── routes/api.php           # API mobile dengan prefix /api/v1
│   ├── routes/web.php           # Route panel admin
│   └── public/                  # Asset dan storage link
└── Mobile/                      # Aplikasi Flutter
    ├── lib/core/                # Konstanta, client API, WebSocket, widget
    └── lib/features/            # Auth, dashboard, attendance, profile
```

## Prasyarat

Pastikan perangkat pengembangan memiliki:

- PHP 8.5 atau versi yang kompatibel dengan `composer.json`.
- Composer dan ekstensi PHP database yang sesuai.
- Node.js dan npm.
- Flutter SDK dengan Dart 3.7 atau lebih baru.
- Android Studio/emulator atau perangkat Android fisik. Xcode diperlukan untuk build iOS di macOS.
- Database SQLite untuk setup paling sederhana, atau MySQL/PostgreSQL untuk environment bersama.
- Ekstensi PHP `zip` jika ingin memakai ekspor ZIP.

## Instalasi dan Menjalankan Backend

Jalankan perintah berikut dari direktori `Web/`.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Pada Windows PowerShell, gunakan perintah berikut untuk menyalin environment:

```powershell
Copy-Item .env.example .env
```

### Database

Konfigurasi default memakai SQLite. Buat file database jika belum tersedia:

```powershell
New-Item database/database.sqlite -ItemType File
```

Kemudian jalankan migration dan seeder:

```bash
php artisan migrate --seed
php artisan storage:link
```

Seeder membuat role, akun demo, data siswa/guru/staff, pengumuman, riwayat presensi, dan konfigurasi sekolah awal.

Untuk MySQL atau PostgreSQL, ubah `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `Web/.env` sebelum menjalankan migration.

### Menjalankan layanan

Buka terminal terpisah dari direktori `Web/`:

```bash
# Terminal 1: Laravel API dan panel admin
php artisan serve

# Terminal 2: Vite untuk asset frontend
npm run dev

# Terminal 3: WebSocket, jika real-time diaktifkan
php artisan reverb:start
```

Panel admin tersedia di `http://127.0.0.1:8000/admin/login`, sedangkan API tersedia di `http://127.0.0.1:8000/api/v1`.

Untuk workflow pengembangan Laravel yang sudah didefinisikan di `composer.json`, perintah berikut menjalankan server, queue listener, log viewer, dan Vite secara bersamaan:

```bash
composer run dev
```

## Instalasi dan Menjalankan Mobile

Jalankan dari direktori `Mobile/`:

```bash
flutter pub get
flutter doctor
flutter devices
flutter run
```

Sebelum menjalankan aplikasi, sesuaikan `Mobile/lib/core/constants/app_constants.dart`:

```dart
static const String baseUrl = 'http://ALAMAT_BACKEND:8000/api/v1';
static const String reverbHost = 'ALAMAT_BACKEND';
static const int reverbPort = 8080;
```

Gunakan nilai berikut sesuai target:

| Target | `baseUrl` host |
| --- | --- |
| Android Emulator | `10.0.2.2` |
| iOS Simulator | `127.0.0.1` |
| Perangkat fisik | IPv4 komputer, misalnya `192.168.1.10` |
| Server production | Domain atau IP publik dengan HTTPS/WSS |

Perangkat fisik dan komputer harus berada pada jaringan yang sama. Android development saat ini mengizinkan HTTP cleartext, tetapi production sebaiknya memakai HTTPS dan WSS.

## Akun Demo

Akun berikut dibuat oleh `DatabaseSeeder`:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@sekolah.com` | `admin123` |
| Siswa | `ahmad.rizki@siswa.sch.id` | `password123` |
| Guru | `hendra.kusuma@sekolah.sch.id` | `password123` |
| Staff | `agus.triyono@sekolah.sch.id` | `password123` |

Ganti seluruh password demo sebelum deployment atau penggunaan nyata.

## API Mobile

Base URL: `http://ALAMAT_BACKEND:8000/api/v1`

| Method | Endpoint | Auth | Keterangan |
| --- | --- | --- | --- |
| `POST` | `/login` | Tidak | Login dan memperoleh token Sanctum |
| `POST` | `/logout` | Sanctum | Menghapus sesi/token server |
| `GET` | `/user` | Sanctum | Mengambil profil pengguna aktif |
| `GET` | `/dashboard` | Sanctum | Jadwal, statistik, dan pengumuman |
| `GET` | `/settings/location` | Sanctum | Mengambil konfigurasi lokasi |
| `GET` | `/attendances` | Sanctum | Riwayat presensi, mendukung `month` dan `year` |
| `POST` | `/attendances/check-in` | Sanctum | Presensi masuk dengan latitude, longitude, dan bukti opsional |
| `POST` | `/attendances/check-out` | Sanctum | Presensi pulang |
| `POST` | `/attendances/permission` | Sanctum | Pengajuan izin/sakit dengan keterangan dan bukti |

Request yang memuat file dikirim sebagai `multipart/form-data`. Token dikirim melalui header:

```http
Authorization: Bearer <SANCTUM_TOKEN>
Accept: application/json
```

## Alur Presensi

1. Pengguna login dan token disimpan secara lokal.
2. Aplikasi mengambil koordinat perangkat melalui GPS.
3. Backend membandingkan koordinat dengan `school_lat`, `school_long`, dan `school_radius` menggunakan perhitungan Haversine.
4. Backend memeriksa jadwal, toleransi keterlambatan, dan apakah pengguna sudah presensi hari itu.
5. Check-in disimpan dengan status `present`; check-out hanya dapat dilakukan setelah check-in.
6. Untuk izin/sakit, pengguna mengirim status, keterangan, dan foto bukti. Pengajuan menunggu persetujuan admin.
7. Perubahan presensi dan pengumuman dipancarkan melalui channel publik atau channel private pengguna.

Konfigurasi awal seeder menggunakan radius 100 meter, jam check-in `06:00`-`07:00`, toleransi terlambat 15 menit, dan jam check-out `15:00`-`17:00`. Nilai ini dapat diubah dari menu pengaturan admin.

## Konfigurasi Penting

### Environment Laravel

Nilai penting pada `Web/.env` meliputi:

```dotenv
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=local-app
REVERB_APP_KEY=local-key
REVERB_APP_SECRET=local-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http
QUEUE_CONNECTION=sync
```

Nama variable Reverb dapat disesuaikan dengan konfigurasi Laravel Reverb yang digunakan. Pastikan key dan host yang dipakai backend sama dengan `reverbKey`, `reverbHost`, dan `reverbPort` pada aplikasi mobile.

### Permission perangkat

Mobile memerlukan internet, lokasi, kamera, dan akses galeri untuk fitur terkait. Permission Android dan iOS sudah dideklarasikan pada manifest/platform configuration, tetapi pengguna tetap harus memberikan izin saat runtime.

### Storage

Jalankan `php artisan storage:link` agar foto bukti yang tersimpan pada disk `public` dapat diakses aplikasi. Jangan menyimpan kredensial production atau secret Reverb di repository.

## Pengujian

### Backend

```bash
cd Web
php artisan test
```

Perintah `composer test` juga tersedia dan menjalankan `php artisan test` setelah membersihkan konfigurasi.

### Mobile

```bash
cd Mobile
flutter analyze
flutter test
```

Test Flutter bawaan saat ini masih berupa smoke test template, sehingga skenario login, geolocation, upload bukti, dan WebSocket tetap perlu diuji pada emulator/perangkat nyata.

## Troubleshooting

### Mobile tidak dapat terhubung ke backend

- Jangan memakai `127.0.0.1` dari perangkat fisik; gunakan IPv4 komputer.
- Untuk Android Emulator gunakan `10.0.2.2`.
- Pastikan `php artisan serve` aktif dan firewall mengizinkan port `8000`.
- Pastikan perangkat dan komputer berada pada jaringan yang sama.

### WebSocket tidak tersambung

- Jalankan `php artisan reverb:start`.
- Pastikan `BROADCAST_CONNECTION` backend menggunakan `reverb`.
- Samakan host, port, dan key Reverb pada backend dan `app_constants.dart`.
- Untuk production, gunakan konfigurasi TLS/WSS dan reverse proxy yang benar.

### Foto atau bukti tidak tampil

- Jalankan `php artisan storage:link`.
- Pastikan `FILESYSTEM_DISK=public` atau disk upload yang dipakai sudah dikonfigurasi.
- Periksa permission kamera/galeri pada perangkat.

### Token dianggap tidak valid

Backend akan merespons `401` ketika token kedaluwarsa atau dicabut. Aplikasi mobile otomatis menghapus token lokal dan mengarahkan pengguna kembali ke login.

## Catatan Keamanan

- Jangan gunakan akun demo dan secret development di production.
- Gunakan HTTPS untuk API serta WSS untuk WebSocket.
- Batasi CORS, aktifkan queue yang sesuai, dan gunakan cache/config hasil build untuk deployment.
- Validasi file upload, ukuran file, dan akses storage sebelum membuka aplikasi ke publik.
- Backup database dan file bukti secara berkala.
- Tinjau ulang role, permission, dan route admin sebelum deployment.

## Lisensi dan Kontribusi

PresenSmart adalah project aplikasi pada repository ini. Aturan lisensi dan kontribusi dapat ditambahkan sesuai kebijakan pemilik project. Untuk pengembangan, pisahkan perubahan backend dan mobile dengan jelas, jalankan test yang relevan, lalu dokumentasikan perubahan konfigurasi atau endpoint baru.
