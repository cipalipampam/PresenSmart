import 'package:flutter/material.dart';
import 'presensi_page.dart';
import 'profil_page.dart';
import 'riwayat_page.dart';
import 'package:flutter_animate/flutter_animate.dart';

class DashboardPage extends StatefulWidget {
  final String token;
  final Map user;

  const DashboardPage({Key? key, required this.token, required this.user})
    : super(key: key);

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class DashboardHomePage extends StatefulWidget {
  final Map user;
  const DashboardHomePage({Key? key, required this.user}) : super(key: key);

  @override
  State<DashboardHomePage> createState() => _DashboardHomePageState();
}

class _DashboardHomePageState extends State<DashboardHomePage> {
  // Dummy data untuk fitur baru
  final Map<String, String> jadwalHariIni = {
    'masuk': '07:00',
    'pulang': '15:00',
    'status': 'Sudah Absen Masuk',
  };

  final List<Map<String, String>> riwayatPresensi = [
    {
      'tanggal': '19 Jul',
      'masuk': '07:01',
      'pulang': '15:02',
      'status': 'Hadir',
    },
    {
      'tanggal': '18 Jul',
      'masuk': '07:03',
      'pulang': '15:01',
      'status': 'Hadir',
    },
    {'tanggal': '17 Jul', 'masuk': '07:10', 'pulang': '-', 'status': 'Alpha'},
  ];

  final List<String> pengumuman = [
    'Libur tanggal 20 Juli',
    'Jangan lupa absen masuk sebelum jam 07:15',
    'Pengambilan rapor tanggal 25 Juli',
    'Jaga kebersihan kelas setiap hari',
    'Pakai seragam lengkap setiap Senin dan Kamis',
    'Jangan lupa membawa kartu pelajar',
    'Pengumuman lomba 17 Agustus, daftar ke BK',
    'Jadwal ujian tengah semester mulai 1 Agustus',
    'Dilarang parkir di depan gerbang sekolah',
    'Kegiatan ekstrakurikuler dimulai minggu depan',
  ];

  @override
  Widget build(BuildContext context) {
    return Stack(
      children: [
        // Header gradient dengan wave
        Container(
          height: 180,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF43C59E), Color(0xFF3A8D99)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
          child: Align(
            alignment: Alignment.bottomCenter,
            child: ClipPath(
              clipper: _WaveClipper(),
              child: Container(
                height: 60,
                color: Colors.white.withOpacity(0.3),
              ),
            ),
          ),
        ),
        SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.fromLTRB(24, 48, 24, 24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // PROFIL SINGKAT
                Card(
                  elevation: 12,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(24),
                  ),
                  color: Colors.white,
                  child: Padding(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 24,
                      vertical: 22,
                    ),
                    child: Row(
                      children: [
                        CircleAvatar(
                          radius: 36,
                          backgroundColor: Color(0xFF43C59E),
                          child: Text(
                            (widget.user['name'] ?? '-').isNotEmpty
                                ? widget.user['name'][0]
                                : '-',
                            style: const TextStyle(
                              fontSize: 36,
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ),
                        const SizedBox(width: 22),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              widget.user['name'] ?? '-',
                              style: Theme.of(
                                context,
                              ).textTheme.titleLarge?.copyWith(
                                color: Color(0xFF43C59E),
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 4,
                              ),
                              decoration: BoxDecoration(
                                color: Colors.blueGrey.withOpacity(0.12),
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Row(
                                mainAxisSize: MainAxisSize.min,
                                children: [
                                  Icon(
                                    Icons.verified_user,
                                    color: Colors.blueGrey,
                                    size: 16,
                                  ),
                                  const SizedBox(width: 4),
                                  Text(
                                    widget.user['role'] ?? '-',
                                    style: const TextStyle(
                                      color: Colors.blueGrey,
                                      fontSize: 13,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ).animate().fadeIn(duration: 600.ms).slideY(begin: 0.2),
                const SizedBox(height: 28),
                // JADWAL HARI INI
                Card(
                      elevation: 8,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(20),
                      ),
                      color: Colors.white,
                      child: Padding(
                        padding: const EdgeInsets.all(22.0),
                        child: Row(
                          children: [
                            Icon(
                              Icons.schedule,
                              color: Color(0xFF43C59E),
                              size: 40,
                            ),
                            const SizedBox(width: 18),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    'Jadwal Hari Ini',
                                    style: TextStyle(
                                      fontWeight: FontWeight.bold,
                                      fontSize: 17,
                                      color: Color(0xFF2D3A4A),
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  Row(
                                    children: [
                                      Icon(
                                        Icons.login,
                                        size: 18,
                                        color: Colors.green,
                                      ),
                                      const SizedBox(width: 4),
                                      Text('Masuk: ${jadwalHariIni['masuk']}'),
                                    ],
                                  ),
                                  Row(
                                    children: [
                                      Icon(
                                        Icons.info_outline,
                                        size: 18,
                                        color: Colors.blue,
                                      ),
                                      const SizedBox(width: 4),
                                      Text(
                                        'Status: ${jadwalHariIni['status']}',
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                    .animate()
                    .fadeIn(duration: 600.ms)
                    .slideY(begin: 0.2, delay: 100.ms),
                const SizedBox(height: 28),
                // PENGUMUMAN
                Card(
                      elevation: 8,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(20),
                      ),
                      color: Colors.yellow[50],
                      child: Padding(
                        padding: const EdgeInsets.all(22.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Icon(
                                  Icons.campaign,
                                  color: Colors.orange,
                                  size: 28,
                                ),
                                const SizedBox(width: 8),
                                Text(
                                  'Pengumuman',
                                  style: TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 17,
                                    color: Colors.orange[900],
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 12),
                            ...pengumuman.map(
                              (item) => Padding(
                                padding: const EdgeInsets.symmetric(
                                  vertical: 2.0,
                                ),
                                child: Row(
                                  children: [
                                    Icon(
                                      Icons.circle,
                                      size: 8,
                                      color: Colors.orange,
                                    ),
                                    const SizedBox(width: 8),
                                    Expanded(child: Text(item)),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    )
                    .animate()
                    .fadeIn(duration: 600.ms)
                    .slideY(begin: 0.2, delay: 300.ms),
                const SizedBox(height: 40),
              ],
            ),
          ),
        ),
      ],
    );
  }
}

class _WaveClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    var path = Path();
    path.lineTo(0, size.height - 20);
    var firstControlPoint = Offset(size.width / 4, size.height);
    var firstEndPoint = Offset(size.width / 2, size.height - 20);
    var secondControlPoint = Offset(3 * size.width / 4, size.height - 60);
    var secondEndPoint = Offset(size.width, size.height - 20);
    path.quadraticBezierTo(
      firstControlPoint.dx,
      firstControlPoint.dy,
      firstEndPoint.dx,
      firstEndPoint.dy,
    );
    path.quadraticBezierTo(
      secondControlPoint.dx,
      secondControlPoint.dy,
      secondEndPoint.dx,
      secondEndPoint.dy,
    );
    path.lineTo(size.width, 0);
    path.close();
    return path;
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}

class _DashboardPageState extends State<DashboardPage> {
  int _selectedIndex = 0;

  List<Widget> get _pages => [
    DashboardHomePage(user: widget.user),
    PresensiPage(token: widget.token),
    RiwayatPage(token: widget.token),
    ProfilPage(user: Map<String, dynamic>.from(widget.user)),
  ];

  List<BottomNavigationBarItem> get _navItems => [
    const BottomNavigationBarItem(
      icon: Icon(Icons.dashboard),
      label: 'Dashboard',
    ),
    const BottomNavigationBarItem(
      icon: Icon(Icons.fingerprint),
      label: 'Presensi',
    ),
    const BottomNavigationBarItem(icon: Icon(Icons.history), label: 'Riwayat'),
    const BottomNavigationBarItem(icon: Icon(Icons.person), label: 'Profil'),
  ];

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(child: _pages[_selectedIndex]),
      bottomNavigationBar: BottomNavigationBar(
        items: _navItems,
        currentIndex: _selectedIndex,
        onTap: (index) {
          setState(() {
            _selectedIndex = index;
          });
        },
        selectedItemColor: Colors.blue.shade700,
        unselectedItemColor: Colors.grey,
        type: BottomNavigationBarType.fixed,
      ),
    );
  }
}
