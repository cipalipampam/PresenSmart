import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:intl/intl.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart' as latlong;
import 'api_service.dart';

class RiwayatPage extends StatefulWidget {
  final String token;
  const RiwayatPage({Key? key, required this.token}) : super(key: key);

  @override
  State<RiwayatPage> createState() => _RiwayatPageState();
}

class _RiwayatPageState extends State<RiwayatPage>
    with SingleTickerProviderStateMixin {
  List<dynamic> _riwayatPresensi = [];
  bool _isLoading = true;
  String? _error;
  late AnimationController _animationController;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );
    _fetchRiwayatPresensi();
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  // Warna tema konsisten dengan dashboard
  final Color _primaryColor = Color(0xFF43C59E); // hijau telur asin (dashboard)
  final Color _secondaryColor = Color(0xFF3A8D99); // biru kehijauan (dashboard)
  final Color _cardBg = Colors.white;
  final Color _headerAccent = Color(0xFF2D9C7A);

  Future<void> _fetchRiwayatPresensi() async {
    setState(() {
      _isLoading = true;
      _error = null;
    });

    try {
      final response = await ApiService.riwayatPresensi(widget.token);
      if (response.statusCode == 200) {
        final List<dynamic> data = jsonDecode(response.body);
        setState(() {
          _riwayatPresensi = data;
          _isLoading = false;
        });
      } else {
        setState(() {
          _error = 'Gagal mengambil riwayat presensi: ${response.body}';
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        _error = 'Terjadi kesalahan: ${e.toString()}';
        _isLoading = false;
      });
      print('Error fetching riwayat presensi: $e');
    }
  }

  // Metode untuk menerjemahkan status presensi
  String _translateStatus(String status) {
    switch (status) {
      case 'hadir':
        return 'Hadir';
      case 'izin':
        return 'Izin';
      case 'sakit':
        return 'Sakit';
      default:
        return status;
    }
  }

  // Metode untuk menampilkan detail presensi
  void _showPresensiDetail(dynamic presensi) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) {
        return DraggableScrollableSheet(
          initialChildSize: 0.7,
          minChildSize: 0.5,
          maxChildSize: 0.9,
          expand: false,
          builder: (context, scrollController) {
            return Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [_primaryColor.withOpacity(0.1), Colors.white],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
                borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
              ),
              child: SingleChildScrollView(
                controller: scrollController,
                child: Padding(
                  padding: const EdgeInsets.all(24.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Center(
                        child: Text(
                          'Detail Presensi',
                          style: Theme.of(
                            context,
                          ).textTheme.titleLarge?.copyWith(
                            fontWeight: FontWeight.bold,
                            color: _primaryColor,
                            letterSpacing: 1.1,
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      _buildDetailItem(
                        Icons.calendar_today,
                        'Tanggal',
                        DateFormat(
                          'dd MMMM yyyy HH:mm',
                        ).format(DateTime.parse(presensi['waktu']).toUtc().add(const Duration(hours: 7))),
                      ),
                      _buildDetailItem(
                        Icons.check_circle,
                        'Status',
                        _translateStatus(presensi['status']),
                      ),
                      if (presensi['keterangan'] != null &&
                          presensi['keterangan'].toString().isNotEmpty)
                        _buildDetailItem(
                          Icons.note,
                          'Keterangan',
                          presensi['keterangan'],
                        ),

                      // Tampilkan bukti untuk izin/sakit
                      if (presensi['status'] != 'hadir' &&
                          presensi['bukti_url'] != null)
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const SizedBox(height: 16),
                            Text(
                              'Bukti',
                              style: Theme.of(
                                context,
                              ).textTheme.titleMedium?.copyWith(
                                fontWeight: FontWeight.bold,
                                color: _primaryColor,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Card(
                              elevation: 4,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(16),
                              ),
                              child: Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(16),
                                  gradient: LinearGradient(
                                    colors: [
                                      _primaryColor.withOpacity(0.1),
                                      Colors.white,
                                    ],
                                    begin: Alignment.topLeft,
                                    end: Alignment.bottomRight,
                                  ),
                                ),
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(16),
                                  child: Column(
                                    children: [
                                      GestureDetector(
                                        onTap: () {
                                          // Buka gambar dalam mode fullscreen
                                          Navigator.of(context).push(
                                            MaterialPageRoute(
                                              builder:
                                                  (context) => Scaffold(
                                                    appBar: AppBar(
                                                      title: const Text(
                                                        'Bukti Presensi',
                                                      ),
                                                      flexibleSpace: Container(
                                                        decoration: BoxDecoration(
                                                          gradient: LinearGradient(
                                                            colors: [
                                                              _primaryColor,
                                                              _secondaryColor,
                                                            ],
                                                            begin:
                                                                Alignment
                                                                    .topLeft,
                                                            end:
                                                                Alignment
                                                                    .bottomRight,
                                                          ),
                                                        ),
                                                      ),
                                                    ),
                                                    body: Center(
                                                      child: CachedNetworkImage(
                                                        imageUrl:
                                                            presensi['bukti_url'],
                                                        fit: BoxFit.contain,
                                                        placeholder:
                                                            (context, url) =>
                                                                const CircularProgressIndicator(),
                                                        errorWidget:
                                                            (
                                                              context,
                                                              url,
                                                              error,
                                                            ) => const Icon(
                                                              Icons.error,
                                                            ),
                                                      ),
                                                    ),
                                                  ),
                                            ),
                                          );
                                        },
                                        child: CachedNetworkImage(
                                          imageUrl: presensi['bukti_url'],
                                          width: double.infinity,
                                          height: 200,
                                          fit: BoxFit.cover,
                                          placeholder:
                                              (context, url) =>
                                                  const CircularProgressIndicator(),
                                          errorWidget:
                                              (context, url, error) =>
                                                  const Icon(Icons.error),
                                        ),
                                      ),
                                      Padding(
                                        padding: const EdgeInsets.all(12.0),
                                        child: Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.center,
                                          children: [
                                            Icon(
                                              Icons.visibility,
                                              color: _primaryColor,
                                              size: 20,
                                            ),
                                            const SizedBox(width: 8),
                                            Text(
                                              'Ketuk untuk melihat detail',
                                              style: TextStyle(
                                                color: _primaryColor,
                                                fontWeight: FontWeight.w600,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ),
                      // Tampilkan peta untuk presensi hadir dengan lokasi
                      if (presensi['status'] == 'hadir' &&
                          presensi['lat'] != null &&
                          presensi['long'] != null)
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const SizedBox(height: 16),
                            Text(
                              'Lokasi Presensi',
                              style: Theme.of(
                                context,
                              ).textTheme.titleMedium?.copyWith(
                                fontWeight: FontWeight.bold,
                                color: _primaryColor,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Card(
                              elevation: 4,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(16),
                              ),
                              child: Container(
                                height: 250,
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(16),
                                  gradient: LinearGradient(
                                    colors: [
                                      _primaryColor.withOpacity(0.1),
                                      Colors.white,
                                    ],
                                    begin: Alignment.topLeft,
                                    end: Alignment.bottomRight,
                                  ),
                                ),
                                child: ClipRRect(
                                  borderRadius: BorderRadius.circular(16),
                                  child: FlutterMap(
                                    options: MapOptions(
                                      center: latlong.LatLng(
                                        double.parse(
                                          presensi['lat'].toString(),
                                        ),
                                        double.parse(
                                          presensi['long'].toString(),
                                        ),
                                      ),
                                      zoom: 15.0,
                                    ),
                                    children: [
                                      TileLayer(
                                        urlTemplate:
                                            'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                                        userAgentPackageName:
                                            'com.example.epresensi',
                                      ),
                                      MarkerLayer(
                                        markers: [
                                          Marker(
                                            point: latlong.LatLng(
                                              double.parse(
                                                presensi['lat'].toString(),
                                              ),
                                              double.parse(
                                                presensi['long'].toString(),
                                              ),
                                            ),
                                            width: 80,
                                            height: 80,
                                            builder:
                                                (context) => const Icon(
                                                  Icons.location_pin,
                                                  color: Colors.red,
                                                  size: 40,
                                                ),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(height: 8),
                            Center(
                              child: Text(
                                'Lokasi Presensi',
                                style: TextStyle(
                                  color: _primaryColor,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ),
                          ],
                        ),
                    ],
                  ),
                ),
              ),
            );
          },
        );
      },
    );
  }

  // Helper untuk membuat item detail
  Widget _buildDetailItem(IconData icon, String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        children: [
          Icon(icon, color: _primaryColor, size: 28),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    color: Colors.grey[700],
                    fontSize: 14,
                  ),
                ),
                Text(
                  value,
                  style: TextStyle(
                    fontSize: 16,
                    color: Colors.black87,
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          // Header gradient melengkung (dashboard style)
          ClipPath(
            clipper: _HeaderClipper(),
            child: Container(
              height: 180,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [_primaryColor, _secondaryColor],
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
          ),
          SafeArea(
            child: Column(
              children: [
                Padding(
                  padding: const EdgeInsets.fromLTRB(24, 32, 24, 12),
                  child: Row(
                    children: [
                      Icon(Icons.history, color: _headerAccent, size: 32),
                      const SizedBox(width: 12),
                      Text(
                        'Riwayat Presensi',
                        style: Theme.of(context).textTheme.titleLarge?.copyWith(
                          fontWeight: FontWeight.bold,
                          color: _headerAccent,
                          fontSize: 24,
                        ),
                      ),
                    ],
                  ),
                ),
                Expanded(
                  child: RefreshIndicator(
                    onRefresh: _fetchRiwayatPresensi,
                    child:
                        _isLoading
                            ? const Center(child: CircularProgressIndicator())
                            : _error != null
                            ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    Icons.error,
                                    color: Colors.red,
                                    size: 64,
                                  ),
                                  const SizedBox(height: 16),
                                  Text(
                                    _error!,
                                    style: TextStyle(
                                      color: Colors.red,
                                      fontSize: 16,
                                    ),
                                    textAlign: TextAlign.center,
                                  ),
                                  const SizedBox(height: 16),
                                  ElevatedButton(
                                    onPressed: _fetchRiwayatPresensi,
                                    child: const Text('Coba Lagi'),
                                  ),
                                ],
                              ),
                            )
                            : _riwayatPresensi.isEmpty
                            ? Center(
                              child: Column(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: [
                                  Icon(
                                    Icons.history,
                                    color: _primaryColor,
                                    size: 64,
                                  ),
                                  const SizedBox(height: 16),
                                  Text(
                                    'Belum ada riwayat presensi',
                                    style: TextStyle(
                                      color: _primaryColor,
                                      fontSize: 18,
                                    ),
                                  ),
                                ],
                              ),
                            )
                            : ListView.builder(
                              padding: const EdgeInsets.only(
                                bottom: 16,
                                top: 8,
                                left: 8,
                                right: 8,
                              ),
                              itemCount: _riwayatPresensi.length,
                              itemBuilder: (context, index) {
                                final presensi = _riwayatPresensi[index];
                                final waktu = DateTime.parse(presensi['waktu']);
                                final status = presensi['status'];
                                Color statusColor;
                                Color statusBg;
                                IconData statusIcon;
                                switch (status) {
                                  case 'hadir':
                                    statusColor = Colors.green.shade700;
                                    statusBg = Colors.green.shade50;
                                    statusIcon = Icons.check_circle;
                                    break;
                                  case 'izin':
                                    statusColor = Colors.orange.shade700;
                                    statusBg = Colors.orange.shade50;
                                    statusIcon = Icons.description;
                                    break;
                                  case 'sakit':
                                    statusColor = Colors.blue.shade700;
                                    statusBg = Colors.blue.shade50;
                                    statusIcon = Icons.medical_services;
                                    break;
                                  default:
                                    statusColor = Colors.grey;
                                    statusBg = Colors.grey.shade100;
                                    statusIcon = Icons.help;
                                }
                                return Card(
                                  elevation: 6,
                                  margin: const EdgeInsets.symmetric(
                                    vertical: 8,
                                    horizontal: 2,
                                  ),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(18),
                                  ),
                                  color: _cardBg,
                                  child: ListTile(
                                    contentPadding: const EdgeInsets.all(16),
                                    leading: CircleAvatar(
                                      backgroundColor: statusBg,
                                      radius: 28,
                                      child: Icon(
                                        statusIcon,
                                        color: statusColor,
                                        size: 28,
                                      ),
                                    ),
                                    title: Text(
                                      _translateStatus(status),
                                      style: TextStyle(
                                        fontWeight: FontWeight.bold,
                                        color: statusColor,
                                        fontSize: 18,
                                      ),
                                    ),
                                    subtitle: Text(
                                      DateFormat(
                                        'dd MMMM yyyy HH:mm',
                                      ).format(waktu.toUtc().add(const Duration(hours: 7))),
                                      style: const TextStyle(
                                        fontSize: 13,
                                        color: Colors.black54,
                                      ),
                                    ),
                                    trailing: const Icon(
                                      Icons.chevron_right,
                                      color: Colors.grey,
                                    ),
                                    onTap: () => _showPresensiDetail(presensi),
                                  ),
                                );
                              },
                            ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // Metode untuk mendapatkan ikon berdasarkan status
}

// Tambahkan _HeaderClipper dan _WaveClipper agar konsisten dengan dashboard
class _HeaderClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    Path path = Path();
    path.lineTo(0, size.height * 0.75);
    path.quadraticBezierTo(
      size.width / 2,
      size.height,
      size.width,
      size.height * 0.75,
    );
    path.lineTo(size.width, 0);
    path.close();
    return path;
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}

class _WaveClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    var path = Path();
    path.lineTo(0, 0);
    path.lineTo(0, size.height);
    path.quadraticBezierTo(
      size.width / 2,
      size.height - 20,
      size.width,
      size.height,
    );
    path.lineTo(size.width, 0);
    path.close();
    return path;
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}
