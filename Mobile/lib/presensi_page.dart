import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:geolocator/geolocator.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart' as latlong;
import 'package:image_picker/image_picker.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'api_service.dart';

// Enum untuk tipe presensi di luar kelas
enum PresensiType { hadir, izin, sakit }

class PresensiPage extends StatefulWidget {
  final String token;
  const PresensiPage({Key? key, required this.token}) : super(key: key);

  @override
  State<PresensiPage> createState() => _PresensiPageState();
}

class _PresensiPageState extends State<PresensiPage>
    with SingleTickerProviderStateMixin {
  String? _status;
  String? _error;
  Position? _currentPosition;
  MapController? _mapController;
  PresensiType _selectedPresensiType = PresensiType.hadir;
  final TextEditingController _keteranganController =
      TextEditingController(); // Gunakan satu controller
  File? _buktiFile;
  final ImagePicker _picker = ImagePicker();
  late AnimationController _animationController;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );
    _animationController.forward();
    _ambilLokasiOtomatis(); // Tambahkan ini
  }

  // Fungsi baru: ambil lokasi otomatis saat halaman dibuka
  Future<void> _ambilLokasiOtomatis() async {
    if (_selectedPresensiType != PresensiType.hadir) return;
    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) {
        setState(() {
          _error = 'GPS tidak aktif';
        });
        return;
      }
      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) {
          setState(() {
            _error = 'Izin lokasi ditolak';
          });
          return;
        }
      }
      if (permission == LocationPermission.deniedForever) {
        setState(() {
          _error = 'Izin lokasi ditolak permanen';
        });
        return;
      }
      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.high,
      );
      if (position.latitude.isNaN ||
          position.longitude.isNaN ||
          position.latitude == 0.0 ||
          position.longitude == 0.0) {
        setState(() {
          _error = 'Gagal mendapatkan lokasi yang valid';
        });
        return;
      }
      setState(() {
        _currentPosition = position;
        _mapController = MapController();
      });
    } catch (e) {
      setState(() {
        _error = 'Gagal mengambil lokasi:  {e.toString()}';
      });
    }
  }

  // Mapping enum ke teks yang lebih ramah pengguna
  String _getPresensiTypeText(PresensiType type) {
    switch (type) {
      case PresensiType.hadir:
        return 'Hadir';
      case PresensiType.izin:
        return 'Izin';
      case PresensiType.sakit:
        return 'Sakit';
    }
  }

  // Metode untuk memilih file bukti
  Future<void> _pickBukti() async {
    final XFile? pickedFile = await _picker.pickImage(
      source: ImageSource.gallery,
      maxWidth: 1800,
      maxHeight: 1800,
    );

    if (pickedFile != null) {
      setState(() {
        _buktiFile = File(pickedFile.path);
      });
    }
  }

  Future<void> _ambilPresensi() async {
    setState(() {
      _status = null;
      _error = null;
    });

    try {
      Position? position;

      // Lokasi hanya diperlukan untuk presensi hadir
      if (_selectedPresensiType == PresensiType.hadir) {
        bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
        if (!serviceEnabled) {
          setState(() {
            _error = 'GPS tidak aktif';
          });
          return;
        }

        LocationPermission permission = await Geolocator.checkPermission();
        if (permission == LocationPermission.denied) {
          permission = await Geolocator.requestPermission();
          if (permission == LocationPermission.denied) {
            setState(() {
              _error = 'Izin lokasi ditolak';
            });
            return;
          }
        }

        if (permission == LocationPermission.deniedForever) {
          setState(() {
            _error = 'Izin lokasi ditolak permanen';
          });
          return;
        }

        position = await Geolocator.getCurrentPosition(
          desiredAccuracy: LocationAccuracy.high,
        );

        // Debug logging koordinat
        print('Koordinat yang didapatkan:');
        print('Latitude: ${position.latitude}');
        print('Longitude: ${position.longitude}');

        // Tambahkan validasi koordinat
        if (position.latitude.isNaN ||
            position.longitude.isNaN ||
            position.latitude == 0.0 ||
            position.longitude == 0.0) {
          setState(() {
            _error = 'Gagal mendapatkan lokasi yang valid';
          });
          return;
        }

        setState(() {
          _currentPosition = position;
          _mapController = MapController();
        });
      }

      // Validasi keterangan untuk izin atau sakit
      String keterangan = _keteranganController.text.trim();
      if ((_selectedPresensiType == PresensiType.izin ||
          _selectedPresensiType == PresensiType.sakit)) {
        // Validasi keterangan
        if (keterangan.isEmpty) {
          setState(() {
            _error = 'Harap masukkan alasan';
          });
          return;
        }

        // Validasi panjang keterangan
        if (keterangan.length < 10) {
          setState(() {
            _error = 'Alasan terlalu singkat (minimal 10 karakter)';
          });
          return;
        }

        // Validasi bukti untuk izin/sakit
        if (_buktiFile == null) {
          setState(() {
            _error = 'Harap unggah bukti';
          });
          return;
        }
      }

      // Persiapkan parameter untuk ApiService
      String tipePresensi = _selectedPresensiType.toString().split('.').last;
      double? lat;
      double? long;
      String? buktiFilePath;
      if (_selectedPresensiType == PresensiType.hadir) {
        lat = position!.latitude;
        long = position.longitude;
      }
      if (_selectedPresensiType == PresensiType.izin ||
          _selectedPresensiType == PresensiType.sakit) {
        keterangan = _keteranganController.text.trim();
        if (_buktiFile != null) {
          buktiFilePath = _buktiFile!.path;
        }
      }
      final response = await ApiService.presensi(
        token: widget.token,
        tipePresensi: tipePresensi,
        keterangan: keterangan,
        lat: lat,
        long: long,
        buktiFilePath: buktiFilePath,
      );
      final data = jsonDecode(response.body);
      if (response.statusCode == 200) {
        setState(() {
          _status = data['message'] ?? 'Presensi berhasil';
          _keteranganController.clear();
          _buktiFile = null;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(_status!),
            backgroundColor: Colors.green,
            duration: const Duration(seconds: 2),
          ),
        );
      } else {
        setState(() {
          _error = data['error'] ?? 'Presensi gagal';
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(_error!),
            backgroundColor: Colors.red,
            duration: const Duration(seconds: 2),
          ),
        );
      }
    } catch (e) {
      setState(() {
        _error = 'Terjadi kesalahan:  e.toString()}';
      });
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(_error!),
          backgroundColor: Colors.red,
          duration: const Duration(seconds: 2),
        ),
      );
    } finally {
      setState(() {});
    }
  }

  @override
  void dispose() {
    _animationController.dispose();
    _keteranganController.dispose();
    super.dispose();
  }

  // Warna tema kustom
  final Color _primaryColor = Color(0xFF43C59E); // hijau telur asin (dashboard)
  final Color _secondaryColor = Color(0xFF3A8D99); // biru kehijauan (dashboard)
  final Color _cardBg = Colors.white;
  final Color _chipText = Color(0xFF3A8D99);
  final Color _chipBg = Color(0xFFE3F8F0);
  final Color _headerAccent = Color(0xFF2D9C7A);

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
            child: SingleChildScrollView(
              physics: const BouncingScrollPhysics(),
              child: Padding(
                padding: const EdgeInsets.fromLTRB(24, 48, 24, 24),
                child: Column(
                  children: [
                    const SizedBox(height: 18),
                    // Ikon fingerprint besar di header
                    Container(
                      decoration: BoxDecoration(
                        color: Colors.white,
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black12,
                            blurRadius: 16,
                            offset: Offset(0, 6),
                          ),
                        ],
                      ),
                      child: Padding(
                        padding: const EdgeInsets.all(18.0),
                        child: Icon(
                          Icons.fingerprint,
                          size: 70,
                          color: _headerAccent,
                        ),
                      ),
                    ).animate().fadeIn(duration: 600.ms, delay: 100.ms),
                    const SizedBox(height: 10),
                    Text(
                      'Presensi Siswa',
                      style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: _headerAccent,
                        fontSize: 24,
                      ),
                    ),
                    const SizedBox(height: 18),
                    // Peta lokasi
                    if (_currentPosition != null && _mapController != null)
                      Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(22),
                          border: Border.all(color: _primaryColor, width: 2),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black12,
                              blurRadius: 10,
                              offset: Offset(0, 5),
                            ),
                          ],
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(20),
                          child: SizedBox(height: 210, child: _buildMap()),
                        ),
                      ).animate().fadeIn(duration: 500.ms).slideY(begin: 0.2),
                    if (_currentPosition == null || _mapController == null)
                      Container(
                        height: 210,
                        decoration: BoxDecoration(
                          color: Colors.grey[200],
                          borderRadius: BorderRadius.circular(22),
                          border: Border.all(
                            color: Colors.grey.shade300,
                            width: 2,
                          ),
                        ),
                        child: Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.location_off,
                                color: Colors.red,
                                size: 40,
                              ),
                              const SizedBox(height: 8),
                              Text(
                                'Lokasi belum tersedia',
                                style: TextStyle(
                                  color: Colors.red,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    const SizedBox(height: 22),
                    // Kartu presensi floating
                    AnimatedBuilder(
                      animation: _animationController,
                      builder: (context, child) {
                        return Transform.scale(
                          scale: _animationController.value,
                          child: Opacity(
                            opacity: _animationController.value,
                            child: Card(
                              elevation: 14,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(28),
                              ),
                              shadowColor: Colors.black26,
                              color: _cardBg,
                              child: Container(
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(28),
                                  gradient: LinearGradient(
                                    colors: [
                                      _primaryColor.withOpacity(0.08),
                                      Colors.white,
                                    ],
                                    begin: Alignment.topLeft,
                                    end: Alignment.bottomRight,
                                  ),
                                ),
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 24,
                                  vertical: 32,
                                ),
                                child: Column(
                                  children: [
                                    // Pilihan tipe presensi
                                    SingleChildScrollView(
                                      scrollDirection: Axis.horizontal,
                                      child: Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.center,
                                        children:
                                            PresensiType.values.map((type) {
                                              IconData icon;
                                              Color color;
                                              switch (type) {
                                                case PresensiType.hadir:
                                                  icon = Icons.check_circle;
                                                  color = _primaryColor;
                                                  break;
                                                case PresensiType.izin:
                                                  icon = Icons.description;
                                                  color = Colors.orange;
                                                  break;
                                                case PresensiType.sakit:
                                                  icon = Icons.medical_services;
                                                  color = Colors.blue;
                                                  break;
                                              }
                                              return Padding(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                      horizontal: 8.0,
                                                    ),
                                                child: ChoiceChip(
                                                  avatar: Icon(
                                                    icon,
                                                    color:
                                                        _selectedPresensiType ==
                                                                type
                                                            ? Colors.white
                                                            : color,
                                                    size: 20,
                                                  ),
                                                  label: Text(
                                                    _getPresensiTypeText(type),
                                                    style: TextStyle(
                                                      color:
                                                          _selectedPresensiType ==
                                                                  type
                                                              ? Colors.white
                                                              : _chipText,
                                                      fontWeight:
                                                          FontWeight.w600,
                                                    ),
                                                  ),
                                                  selected:
                                                      _selectedPresensiType ==
                                                      type,
                                                  onSelected: (bool selected) {
                                                    setState(() {
                                                      _selectedPresensiType =
                                                          type;
                                                      _error = null;
                                                      _status = null;
                                                    });
                                                    if (type == PresensiType.hadir) {
                                                      _ambilLokasiOtomatis();
                                                    } else {
                                                      setState(() {
                                                        _currentPosition = null;
                                                        _mapController = null;
                                                      });
                                                    }
                                                  },
                                                  selectedColor: color,
                                                  backgroundColor: _chipBg,
                                                  padding:
                                                      const EdgeInsets.symmetric(
                                                        horizontal: 16,
                                                        vertical: 8,
                                                      ),
                                                ),
                                              );
                                            }).toList(),
                                      ),
                                    ),
                                    // Input alasan & upload bukti
                                    if (_selectedPresensiType !=
                                        PresensiType.hadir)
                                      Padding(
                                        padding: const EdgeInsets.only(
                                          top: 18.0,
                                        ),
                                        child: Column(
                                          children: [
                                            TextFormField(
                                              controller: _keteranganController,
                                              decoration: InputDecoration(
                                                labelText:
                                                    'Alasan ${_getPresensiTypeText(_selectedPresensiType)}',
                                                hintText:
                                                    'Masukkan alasan detail',
                                                prefixIcon: Icon(
                                                  Icons.description,
                                                  color: _primaryColor,
                                                ),
                                                border: OutlineInputBorder(
                                                  borderRadius:
                                                      BorderRadius.circular(16),
                                                  borderSide: BorderSide(
                                                    color: _primaryColor
                                                        .withOpacity(0.5),
                                                  ),
                                                ),
                                                focusedBorder:
                                                    OutlineInputBorder(
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                            16,
                                                          ),
                                                      borderSide: BorderSide(
                                                        color: _primaryColor,
                                                        width: 2,
                                                      ),
                                                    ),
                                                errorText: _error,
                                              ),
                                              maxLines: 3,
                                              maxLength: 500,
                                              keyboardType:
                                                  TextInputType.multiline,
                                              style: TextStyle(
                                                color: Colors.black87,
                                                fontSize: 16,
                                              ),
                                            ).animate().fadeIn(delay: 300.ms),
                                            const SizedBox(height: 16),
                                            Row(
                                              children: [
                                                Expanded(
                                                  child: ElevatedButton.icon(
                                                    icon: Icon(
                                                      Icons.upload_file,
                                                      color: Colors.white,
                                                    ),
                                                    label: Text(
                                                      _buktiFile == null
                                                          ? 'Unggah Bukti'
                                                          : 'Ganti Bukti',
                                                      style: TextStyle(
                                                        color: Colors.white,
                                                        fontWeight:
                                                            FontWeight.bold,
                                                      ),
                                                    ),
                                                    onPressed: _pickBukti,
                                                    style: ElevatedButton.styleFrom(
                                                      backgroundColor:
                                                          _buktiFile != null
                                                              ? _secondaryColor
                                                              : _primaryColor,
                                                      shape: RoundedRectangleBorder(
                                                        borderRadius:
                                                            BorderRadius.circular(
                                                              16,
                                                            ),
                                                      ),
                                                      padding:
                                                          const EdgeInsets.symmetric(
                                                            horizontal: 18,
                                                            vertical: 12,
                                                          ),
                                                    ),
                                                  ),
                                                ),
                                                if (_buktiFile != null)
                                                  IconButton(
                                                    icon: Icon(
                                                      Icons.delete,
                                                      color: Colors.red,
                                                    ),
                                                    onPressed: () {
                                                      setState(() {
                                                        _buktiFile = null;
                                                      });
                                                    },
                                                  ),
                                              ],
                                            ),
                                            if (_buktiFile != null)
                                              Padding(
                                                padding: const EdgeInsets.only(
                                                  top: 16.0,
                                                ),
                                                child: Container(
                                                  decoration: BoxDecoration(
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                          16,
                                                        ),
                                                    boxShadow: [
                                                      BoxShadow(
                                                        color: Colors.black12,
                                                        blurRadius: 10,
                                                        offset: Offset(0, 5),
                                                      ),
                                                    ],
                                                  ),
                                                  child: ClipRRect(
                                                    borderRadius:
                                                        BorderRadius.circular(
                                                          16,
                                                        ),
                                                    child: Image.file(
                                                      _buktiFile!,
                                                      height: 180,
                                                      width: double.infinity,
                                                      fit: BoxFit.cover,
                                                    ),
                                                  ),
                                                ).animate().fadeIn(
                                                  delay: 500.ms,
                                                ),
                                              ),
                                          ],
                                        ),
                                      ),
                                    const SizedBox(height: 18),
                                    // Status/Error
                                    if (_status != null)
                                      Card(
                                        color: Colors.green.shade50,
                                        elevation: 0,
                                        shape: RoundedRectangleBorder(
                                          borderRadius: BorderRadius.circular(
                                            12,
                                          ),
                                        ),
                                        child: Padding(
                                          padding: const EdgeInsets.symmetric(
                                            vertical: 10,
                                            horizontal: 16,
                                          ),
                                          child: Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.center,
                                            children: [
                                              Icon(
                                                Icons.check_circle,
                                                color: Colors.green,
                                                size: 22,
                                              ),
                                              const SizedBox(width: 8),
                                              Flexible(
                                                child: Text(
                                                  _status!,
                                                  style: TextStyle(
                                                    color: Colors.green,
                                                    fontWeight: FontWeight.w600,
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ).animate().fadeIn(),
                                    if (_error != null &&
                                        _selectedPresensiType ==
                                            PresensiType.hadir)
                                      Card(
                                        color: Colors.red.shade50,
                                        elevation: 0,
                                        shape: RoundedRectangleBorder(
                                          borderRadius: BorderRadius.circular(
                                            12,
                                          ),
                                        ),
                                        child: Padding(
                                          padding: const EdgeInsets.symmetric(
                                            vertical: 10,
                                            horizontal: 16,
                                          ),
                                          child: Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.center,
                                            children: [
                                              Icon(
                                                Icons.error,
                                                color: Colors.red,
                                                size: 22,
                                              ),
                                              const SizedBox(width: 8),
                                              Flexible(
                                                child: Text(
                                                  _error!,
                                                  style: TextStyle(
                                                    color: Colors.red,
                                                    fontWeight: FontWeight.w600,
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ).animate().fadeIn(),
                                    const SizedBox(height: 24),
                                    // Tombol Kirim
                                    SizedBox(
                                      width: double.infinity,
                                      child: ElevatedButton.icon(
                                        icon: const Icon(
                                          Icons.fingerprint,
                                          color: Colors.white,
                                        ),
                                        label: Text(
                                          'Kirim ${_getPresensiTypeText(_selectedPresensiType)}',
                                          style: const TextStyle(
                                            color: Colors.white,
                                            fontWeight: FontWeight.bold,
                                            letterSpacing: 1.1,
                                            fontSize: 16,
                                          ),
                                        ),
                                        onPressed: _ambilPresensi,
                                        style: ElevatedButton.styleFrom(
                                          backgroundColor: _primaryColor,
                                          shape: RoundedRectangleBorder(
                                            borderRadius: BorderRadius.circular(
                                              18,
                                            ),
                                          ),
                                          padding: const EdgeInsets.symmetric(
                                            horizontal: 24,
                                            vertical: 18,
                                          ),
                                          elevation: 6,
                                        ),
                                      ).animate().fadeIn(delay: 600.ms),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                    const SizedBox(height: 32),
                  ],
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  // Widget status baru untuk konsistensi

  // Metode baru untuk membuat peta dengan penanganan error
  Widget _buildMap() {
    try {
      // Pastikan koordinat valid
      if (_currentPosition == null ||
          _currentPosition!.latitude.isNaN ||
          _currentPosition!.longitude.isNaN ||
          _currentPosition!.latitude == 0.0 ||
          _currentPosition!.longitude == 0.0) {
        return Container(
          color: Colors.grey[200],
          child: Center(
            child: Text(
              'Lokasi tidak tersedia',
              style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold),
            ),
          ),
        );
      }

      // Konversi koordinat dengan aman
      final lat = double.tryParse(_currentPosition!.latitude.toString()) ?? 0.0;
      final lon =
          double.tryParse(_currentPosition!.longitude.toString()) ?? 0.0;

      return FlutterMap(
        mapController: _mapController!,
        options: MapOptions(center: latlong.LatLng(lat, lon), zoom: 15.0),
        children: [
          TileLayer(
            urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
            userAgentPackageName: 'com.example.epresensi',
          ),
          MarkerLayer(
            markers: [
              Marker(
                point: latlong.LatLng(lat, lon),
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
      );
    } catch (e) {
      print('Error membangun peta: $e');
      return Container(
        color: Colors.grey[200],
        child: Center(
          child: Text(
            'Gagal memuat peta',
            style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold),
          ),
        ),
      );
    }
  }
}

// Custom clipper untuk header curve
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

// Tambahkan _WaveClipper agar sama dengan dashboard
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
