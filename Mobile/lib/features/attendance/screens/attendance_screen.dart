import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:latlong2/latlong.dart' as latlong;
import 'package:provider/provider.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/widgets/glass_container.dart';
import '../providers/attendance_provider.dart';

enum PresensiType { hadir, izin, sakit }

class AttendanceScreen extends StatefulWidget {
  final VoidCallback? onNavigateToHistory;
  const AttendanceScreen({Key? key, this.onNavigateToHistory}) : super(key: key);

  @override
  _AttendanceScreenState createState() => _AttendanceScreenState();
}

class _AttendanceScreenState extends State<AttendanceScreen> {
  PresensiType _selectedPresensiType = PresensiType.hadir;
  
  Position? _currentPosition;
  String? _errorGps;
  
  final TextEditingController _keteranganController = TextEditingController();
  File? _buktiFile;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final provider = Provider.of<AttendanceProvider>(context, listen: false);
      provider.fetchHistory();
      provider.fetchLocationSettings();
    });
    _fetchLocation();
  }

  Future<void> _fetchLocation() async {
    if (_selectedPresensiType != PresensiType.hadir) return;
    
    if (mounted) setState(() => _errorGps = null);

    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) throw 'GPS tidak aktif';

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied) throw 'Izin lokasi ditolak';
      }

      if (permission == LocationPermission.deniedForever) {
        throw 'Izin lokasi ditolak permanen';
      }

      Position position = await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.best,
      );

      if (!mounted) return;
      setState(() {
        _currentPosition = position;
      });
    } catch (e) {
      if (mounted) setState(() => _errorGps = e.toString());
    }
  }

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

  Future<void> _submitAttendance() async {
    final attendanceProvider = Provider.of<AttendanceProvider>(context, listen: false);

    bool success = false;

    if (_selectedPresensiType == PresensiType.hadir) {
      if (_currentPosition == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Lokasi tidak valid, harap nyalakan GPS')),
        );
        return;
      }

      success = await attendanceProvider.checkIn(
        latitude: _currentPosition!.latitude,
        longitude: _currentPosition!.longitude,
        notes: _keteranganController.text,
        proofImage: _buktiFile,
      );
    } else {
      if (_keteranganController.text.trim().isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Harap masukkan alasan Izin/Sakit')),
        );
        return;
      }

      if (_buktiFile == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Harap unggah bukti foto surat dokter/izin')),
        );
        return;
      }

      success = await attendanceProvider.submitPermission(
        status: _selectedPresensiType == PresensiType.izin ? 'permission' : 'sick',
        notes: _keteranganController.text,
        proofImage: _buktiFile,
      );
    }

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Data Presensi Berhasil Dikirim!'), backgroundColor: Colors.green),
      );
      _keteranganController.clear();
      setState(() => _buktiFile = null);
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(attendanceProvider.errorMessage ?? 'Terjadi kesalahan sistem'), backgroundColor: Colors.red),
      );
    }
  }

  Widget _buildBannerBlock({required IconData icon, required Color color, required String text}) {
    return GlassContainer(
      padding: const EdgeInsets.all(16),
      backgroundColor: color.withOpacity(0.1),
      border: Border.all(color: color.withOpacity(0.3)),
      child: Row(
        children: [
          Icon(icon, color: color, size: 30),
          const SizedBox(width: 12),
          Expanded(
            child: Text(
              text,
              style: TextStyle(color: color, fontWeight: FontWeight.bold),
            ),
          ),
        ],
      ),
    );
  }

  String _getTabName(PresensiType type) {
    if (type == PresensiType.hadir) return 'Hadir';
    if (type == PresensiType.izin) return 'Izin';
    return 'Sakit';
  }

  @override
  void dispose() {
    _keteranganController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppConstants.colorBackgroundDark,
      appBar: AppBar(
        title: const Text('Form Presensi', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, letterSpacing: 1.2)),
        backgroundColor: Colors.transparent,
        elevation: 0,
        centerTitle: true,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: Stack(
        children: [
          // Background Glow
          Positioned(
            top: 50,
            left: -100,
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppConstants.colorPrimaryBase.withOpacity(0.05),
                boxShadow: [BoxShadow(color: AppConstants.colorPrimaryBase.withOpacity(0.1), blurRadius: 100)],
              ),
            ),
          ),
          SingleChildScrollView(
            padding: const EdgeInsets.fromLTRB(24, 0, 24, 120),
            child: Column(
              children: [
                // Segmented Control (Pill Tabs)
                GlassContainer(
                  padding: const EdgeInsets.all(6),
                  borderRadius: BorderRadius.circular(32),
                  backgroundColor: AppConstants.colorCardDark.withOpacity(0.5),
                  child: Row(
                    children: PresensiType.values.map((type) {
                      final isSelected = _selectedPresensiType == type;
                      return Expanded(
                        child: GestureDetector(
                          onTap: () {
                            setState(() {
                              _selectedPresensiType = type;
                            });
                            if (type == PresensiType.hadir && _currentPosition == null) {
                              _fetchLocation();
                            }
                          },
                          child: AnimatedContainer(
                            duration: const Duration(milliseconds: 300),
                            curve: Curves.easeOutCubic,
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            decoration: BoxDecoration(
                              color: isSelected ? AppConstants.colorPrimaryBase : Colors.transparent,
                              borderRadius: BorderRadius.circular(24),
                              boxShadow: isSelected
                                  ? [
                                      BoxShadow(
                                        color: AppConstants.colorPrimaryBase.withOpacity(0.4),
                                        blurRadius: 12,
                                        offset: const Offset(0, 4),
                                      )
                                    ]
                                  : [],
                            ),
                            child: Text(
                              _getTabName(type),
                              textAlign: TextAlign.center,
                              style: TextStyle(
                                color: isSelected ? AppConstants.colorBackgroundDark : AppConstants.colorTextSecondary,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ),
                const SizedBox(height: 24),

                // MAP SECTION for Hadir
                if (_selectedPresensiType == PresensiType.hadir) ...[
                  if (_currentPosition != null) ...[
                    Consumer<AttendanceProvider>(
                      builder: (context, provider, child) {
                        return GlassContainer(
                          padding: const EdgeInsets.all(8),
                          borderRadius: BorderRadius.circular(28),
                          backgroundColor: AppConstants.colorCardDark,
                          child: Container(
                            height: 250,
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(20),
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(20),
                              child: FlutterMap(
                                options: MapOptions(
                                  center: provider.officeLat != null 
                                    ? latlong.LatLng(provider.officeLat!, provider.officeLng!) 
                                    : latlong.LatLng(_currentPosition!.latitude, _currentPosition!.longitude),
                                  zoom: 16.0,
                                ),
                                children: [
                                  TileLayer(
                                    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                                    userAgentPackageName: 'com.example.epresensi',
                                  ),
                                  if (provider.officeLat != null)
                                    CircleLayer(
                                      circles: [
                                        CircleMarker(
                                          point: latlong.LatLng(provider.officeLat!, provider.officeLng!),
                                          color: AppConstants.colorSecondaryBase.withOpacity(0.2),
                                          borderStrokeWidth: 2,
                                          borderColor: AppConstants.colorSecondaryBase,
                                          useRadiusInMeter: true,
                                          radius: provider.officeRadius?.toDouble() ?? 50.0,
                                        ),
                                      ],
                                    ),
                                  MarkerLayer(
                                    markers: [
                                      Marker(
                                        point: latlong.LatLng(
                                          _currentPosition!.latitude,
                                          _currentPosition!.longitude,
                                        ),
                                        width: 80,
                                        height: 80,
                                        builder: (ctx) => const Icon(
                                          Icons.person_pin_circle,
                                          color: Colors.redAccent,
                                          size: 50,
                                        ),
                                      ),
                                      if (provider.officeLat != null)
                                        Marker(
                                          point: latlong.LatLng(provider.officeLat!, provider.officeLng!),
                                          width: 80,
                                          height: 80,
                                          builder: (ctx) => const Icon(
                                            Icons.business,
                                            color: AppConstants.colorSecondaryBase,
                                            size: 30,
                                          ),
                                        ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ),
                        );
                      }
                    ),
                    const SizedBox(height: 16),
                    GlassContainer(
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                      backgroundColor: Colors.greenAccent.withOpacity(0.1),
                      border: Border.all(color: Colors.greenAccent.withOpacity(0.3)),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: const [
                          Icon(Icons.gps_fixed, color: Colors.greenAccent, size: 18),
                          SizedBox(width: 8),
                          Text('Lokasi GPS Terkunci', style: TextStyle(color: Colors.greenAccent, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    ),
                  ] else if (_errorGps != null) ...[
                    GlassContainer(
                      height: 220,
                      width: double.infinity,
                      backgroundColor: Colors.redAccent.withOpacity(0.1),
                      border: Border.all(color: Colors.redAccent.withOpacity(0.3)),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(Icons.location_off, color: Colors.redAccent, size: 40),
                          const SizedBox(height: 8),
                          Text(_errorGps!, style: const TextStyle(color: Colors.redAccent, fontWeight: FontWeight.bold)),
                        ],
                      ),
                    )
                  ] else ...[
                    GlassContainer(
                      height: 220,
                      width: double.infinity,
                      backgroundColor: AppConstants.colorCardDark,
                      child: const Center(child: CircularProgressIndicator(color: AppConstants.colorPrimaryBase)),
                    )
                  ]
                ],

                const SizedBox(height: 24),

                // Keterangan / Alasan
                TextFormField(
                  controller: _keteranganController,
                  style: const TextStyle(color: Colors.white),
                  decoration: InputDecoration(
                    labelText: _selectedPresensiType == PresensiType.hadir ? 'Catatan (Opsional)' : 'Alasan Lengkap (Wajib)',
                    labelStyle: const TextStyle(color: AppConstants.colorTextSecondary),
                    prefixIcon: const Icon(Icons.edit_note, color: AppConstants.colorTextSecondary),
                    filled: true,
                    fillColor: Colors.white.withOpacity(0.03),
                    border: OutlineInputBorder(borderRadius: BorderRadius.circular(16), borderSide: BorderSide.none),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(16),
                      borderSide: const BorderSide(color: AppConstants.colorPrimaryBase, width: 2),
                    ),
                  ),
                  maxLines: 3,
                ),
                const SizedBox(height: 16),

                // Upload Bukti
                Row(
                  children: [
                    Expanded(
                      child: InkWell(
                        onTap: _pickBukti,
                        borderRadius: BorderRadius.circular(16),
                        child: GlassContainer(
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          backgroundColor: _buktiFile != null ? Colors.green.withOpacity(0.1) : AppConstants.colorCardDark,
                          border: Border.all(
                            color: _buktiFile != null ? Colors.green : AppConstants.colorTextSecondary.withOpacity(0.2)
                          ),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                _buktiFile != null ? Icons.check_circle : Icons.camera_alt,
                                color: _buktiFile != null ? Colors.greenAccent : AppConstants.colorTextSecondary,
                                size: 20,
                              ),
                              const SizedBox(width: 8),
                              Text(
                                _buktiFile != null ? 'Bukti Terlampir' : 'Lampirkan Foto',
                                style: TextStyle(
                                  color: _buktiFile != null ? Colors.greenAccent : AppConstants.colorTextSecondary,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                    if (_buktiFile != null) ...[
                      const SizedBox(width: 12),
                      InkWell(
                        onTap: () => setState(() => _buktiFile = null),
                        borderRadius: BorderRadius.circular(16),
                        child: GlassContainer(
                          padding: const EdgeInsets.all(16),
                          backgroundColor: Colors.redAccent.withOpacity(0.1),
                          border: Border.all(color: Colors.redAccent.withOpacity(0.3)),
                          child: const Icon(Icons.delete, color: Colors.redAccent, size: 20),
                        ),
                      )
                    ]
                  ],
                ),
                
                if (_buktiFile != null)
                  Container(
                    margin: const EdgeInsets.only(top: 16),
                    height: 150,
                    width: double.infinity,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(16),
                      image: DecorationImage(
                        image: FileImage(_buktiFile!),
                        fit: BoxFit.cover,
                      ),
                    ),
                  ),

                const SizedBox(height: 32),

                // BUTTONS
                Consumer<AttendanceProvider>(
                  builder: (context, provider, child) {
                    if (provider.isRejectedToday) {
                      return _buildBannerBlock(
                        icon: Icons.cancel,
                        color: Colors.redAccent,
                        text: 'Permohonan Anda ditolak. Anda tercatat Alfa hari ini.',
                      );
                    }
                    if (provider.isPendingIzinSakitToday) {
                      return _buildBannerBlock(
                        icon: Icons.hourglass_empty,
                        color: Colors.orangeAccent,
                        text: 'Permohonan Izin/Sakit Anda sedang menunggu persetujuan Admin.',
                      );
                    }
                    if (provider.isIzinSakitApprovedToday) {
                      return _buildBannerBlock(
                        icon: Icons.check_circle,
                        color: Colors.greenAccent,
                        text: 'Permohonan disetujui. Selamat beristirahat hari ini.',
                      );
                    }
                    if (provider.hasCheckedOutToday) {
                      return _buildBannerBlock(
                        icon: Icons.check_circle,
                        color: AppConstants.colorPrimaryBase,
                        text: 'Anda telah menyelesaikan jam kerja/sekolah hari ini.',
                      );
                    }

                    if (provider.hasCheckedInToday) {
                      return SizedBox(
                        width: double.infinity,
                        height: 56,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.transparent,
                            shadowColor: Colors.transparent,
                            padding: EdgeInsets.zero,
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                          ),
                          onPressed: provider.isLoading ? null : () async {
                            final success = await provider.checkOut();
                            if (success) {
                              if (mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  const SnackBar(content: Text('Berhasil absen pulang!'), backgroundColor: Colors.green),
                                );
                                widget.onNavigateToHistory?.call();
                              }
                            } else {
                              if (mounted && provider.errorMessage != null) {
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(content: Text(provider.errorMessage!), backgroundColor: Colors.red),
                                );
                              }
                            }
                          },
                          child: Ink(
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [Color(0xFFFF9A9E), Color(0xFFFECFEF)],
                              ),
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: Container(
                              alignment: Alignment.center,
                              child: provider.isLoading
                                  ? const CircularProgressIndicator(color: AppConstants.colorBackgroundDark)
                                  : const Text(
                                      'ABSEN PULANG',
                                      style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: AppConstants.colorBackgroundDark, letterSpacing: 1.2),
                                    ),
                            ),
                          ),
                        ),
                      );
                    }

                    return SizedBox(
                      width: double.infinity,
                      height: 56,
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.transparent,
                          shadowColor: Colors.transparent,
                          padding: EdgeInsets.zero,
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                        ),
                        onPressed: provider.isLoading ? null : _submitAttendance,
                        child: Ink(
                          decoration: BoxDecoration(
                            gradient: const LinearGradient(
                              colors: [AppConstants.colorSecondaryBase, AppConstants.colorPrimaryBase],
                            ),
                            borderRadius: BorderRadius.circular(16),
                          ),
                          child: Container(
                            alignment: Alignment.center,
                            child: provider.isLoading
                                ? const CircularProgressIndicator(color: Colors.white)
                                : Text(
                                    'KIRIM PRESENSI ${_getTabName(_selectedPresensiType).toUpperCase()}',
                                    style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white, letterSpacing: 1.2),
                                  ),
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
