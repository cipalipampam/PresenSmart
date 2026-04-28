<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title'     => 'Jadwal Ujian Akhir Semester Genap 2024/2025',
                'content'   => 'Ujian Akhir Semester (UAS) Genap akan dilaksanakan pada tanggal 2–12 Juni 2025. Semua siswa wajib hadir tepat waktu dan membawa kartu ujian. Siswa yang absen tanpa keterangan resmi tidak diperkenankan mengikuti susulan.',
                'is_active' => true,
            ],
            [
                'title'     => 'Libur Nasional Hari Pendidikan Nasional',
                'content'   => 'Sehubungan dengan Hari Pendidikan Nasional pada tanggal 2 Mei 2025, kegiatan belajar mengajar diliburkan. Kegiatan akan kembali normal pada hari Senin, 5 Mei 2025.',
                'is_active' => true,
            ],
            [
                'title'     => 'Pengumuman Penerimaan Peserta Didik Baru (PPDB) 2025/2026',
                'content'   => 'Pendaftaran PPDB tahun ajaran 2025/2026 dibuka mulai 1 Juli s.d. 15 Juli 2025. Informasi lengkap mengenai persyaratan dan alur pendaftaran dapat dilihat di papan pengumuman sekolah atau menghubungi bagian tata usaha.',
                'is_active' => true,
            ],
            [
                'title'     => 'Kegiatan Classmeeting Akhir Semester',
                'content'   => 'Classmeeting akan diselenggarakan pada 16–20 Juni 2025 setelah pelaksanaan UAS. Kegiatan meliputi lomba voli, futsal, bulu tangkis, dan seni. Pendaftaran peserta melalui wali kelas masing-masing paling lambat 30 Mei 2025.',
                'is_active' => true,
            ],
            [
                'title'     => 'Peringatan Tata Tertib Presensi',
                'content'   => 'Diberitahukan kepada seluruh siswa bahwa batas toleransi keterlambatan masuk adalah 15 menit setelah bel berbunyi. Siswa yang terlambat lebih dari batas tersebut wajib melapor ke guru piket dan mendapatkan surat izin masuk kelas.',
                'is_active' => true,
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::firstOrCreate(
                ['title' => $data['title']],
                [
                    'content'   => $data['content'],
                    'is_active' => $data['is_active'],
                ]
            );
        }

        $this->command->info('✅ 5 pengumuman berhasil di-seed.');
    }
}
