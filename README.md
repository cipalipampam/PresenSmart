# PresenSmart - Sistem Absensi Cerdas (Web & Mobile)

**PresenSmart** adalah platform absensi modern yang dirancang untuk sekolah atau instansi, mengintegrasikan panel admin berbasis web dengan aplikasi mobile untuk pengguna (siswa/karyawan). Sistem ini dilengkapi dengan validasi lokasi (Geofencing) dan pembaruan data secara real-time.

---

## 🚀 Fitur Utama

### 🌐 Web (Admin Portal)
- **Dashboard Real-time:** Memantau statistik kehadiran harian secara instan.
- **Manajemen User:** CRUD lengkap untuk Siswa, Guru, dan Staff.
- **Konfigurasi Sistem:** Pengaturan lokasi sekolah (lat/long), radius absen, dan jadwal jam masuk/pulang.
- **Rekap Absensi:** Laporan kehadiran dengan fitur filter pencarian.
- **Multi-format Export:** Cetak laporan ke PDF, Excel (XLSX), CSV, atau ZIP (gabungan semua format).
- **Pengumuman:** Membuat dan menyebarkan informasi ke seluruh pengguna aplikasi mobile.

### 📱 Mobile (Aplikasi Pengguna)
- **Absensi Geofencing:** Hanya bisa absen jika berada dalam radius yang ditentukan dari lokasi sekolah.
- **Bukti Foto:** Mendukung pengambilan foto (kamera) sebagai bukti kehadiran atau izin.
- **Izin & Sakit:** Pengajuan permohonan izin/sakit langsung dari aplikasi.
- **Riwayat Absensi:** Melihat catatan kehadiran pribadi per bulan.
- **Notifikasi Real-time:** Menerima pengumuman dan update status absensi (Disetujui/Ditolak) secara instan.
- **UI Premium:** Desain modern dengan tema gelap (Dark Mode) dan Glassmorphism.

---

## 🛠️ Tech Stack

### Backend & Web
- **Framework:** Laravel 12.x
- **Frontend Assets:** Vite, Tailwind CSS v4, Bootstrap 5.3
- **Real-time Engine:** Laravel Reverb (WebSockets)
- **Database:** MySQL / PostgreSQL
- **Auth:** Laravel Sanctum (API) & Session (Web)

### Mobile
- **Framework:** Flutter (Dart)
- **State Management:** Provider
- **Networking:** Dio (HTTP Client)
- **Maps:** Flutter Map (OpenStreetMap)
- **Animations:** Flutter Animate

---

## 📥 Cara Menjalankan Project

### 1. Persiapan Web (Backend)
1. Buka direktori `Web/`.
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Salin file environment:
   ```bash
   cp .env.example .env
   ```
4. Sesuaikan konfigurasi database dan Reverb di `.env`.
5. Generate application key:
   ```bash
   php artisan key:generate
   ```
6. Jalankan migrasi dan seeding data:
   ```bash
   php artisan migrate --seed
   ```
7. Jalankan server:
   ```bash
   php artisan serve
   npm run dev
   # Jika menggunakan Reverb, jalankan juga:
   php artisan reverb:start
   ```

### 2. Persiapan Mobile (Flutter)
1. Buka direktori `Mobile/`.
2. Install dependencies:
   ```bash
   flutter pub get
   ```
3. Sesuaikan konfigurasi API di `lib/core/constants/app_constants.dart`. Pastikan `baseUrl` mengarah ke IP komputer Anda (contoh: `192.168.1.x`) agar bisa diakses dari perangkat fisik/emulator.
4. Jalankan aplikasi:
   ```bash
   flutter run
   ```

---

## 🔑 Akun Demo (Default)

| Role | Email | Password |
| :--- | :--- | :--- |
| **Admin** | `admin@sekolah.com` | `admin123` |
| **Siswa** | `ahmad.rizki@siswa.sch.id` | `password123` |
| **Guru** | `hendra.kusuma@sekolah.sch.id` | `password123` |
| **Staff** | `agus.triyono@sekolah.sch.id` | `password123` |

---

## 📂 Struktur Project
- `Web/`: Aplikasi backend Laravel & panel admin.
- `Mobile/`: Aplikasi Flutter untuk Android & iOS.

---
**PresenSmart** - *Hadir Lebih Cerdas, Kelola Lebih Mudah.*
