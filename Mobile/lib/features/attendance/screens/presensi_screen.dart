import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:geolocator/geolocator.dart';
import 'package:image_picker/image_picker.dart';
import 'package:latlong2/latlong.dart' as latlong;
import 'package:provider/provider.dart';
import '../providers/attendance_provider.dart';

enum PresensiType { hadir, izin, sakit }

class PresensiScreen extends StatefulWidget {
  const PresensiScreen({Key? key}) : super(key: key);

  @override
  State<PresensiScreen> createState() => _PresensiScreenState();
}

class _PresensiScreenState extends State<PresensiScreen> {
  PresensiType _selectedPresensiType = PresensiType.hadir;
  
  Position? _currentPosition;
  String? _errorGps;
  
  final TextEditingController _keteranganController = TextEditingController();
  File? _buktiFile;
  final ImagePicker _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _fetchLocation();
  }

  Future<void> _fetchLocation() async {
    if (_selectedPresensiType != PresensiType.hadir) return;
    
    setState(() => _errorGps = null);

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

      setState(() {
        _currentPosition = position;
      });
    } catch (e) {
      setState(() => _errorGps = e.toString());
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
      appBar: AppBar(
        title: const Text('Form Presensi', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF43C59E),
        elevation: 0,
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24),
        child: Column(
          children: [
            // Segmented Control untuk Filter Status
            Container(
              decoration: BoxDecoration(
                color: Colors.grey.shade200,
                borderRadius: BorderRadius.circular(16),
              ),
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
                      child: Container(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        decoration: BoxDecoration(
                          color: isSelected ? const Color(0xFF43C59E) : Colors.transparent,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: isSelected
                              ? [const BoxShadow(color: Colors.black12, blurRadius: 4, offset: Offset(0, 2))]
                              : [],
                        ),
                        child: Text(
                          _getTabName(type),
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: isSelected ? Colors.white : Colors.grey.shade600,
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

            // TAMPIL Peta saat Mode = Hadir
            if (_selectedPresensiType == PresensiType.hadir) ...[
              if (_currentPosition != null) ...[
                Container(
                  height: 220,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: const [BoxShadow(color: Colors.black12, blurRadius: 10)],
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(20),
                    child: FlutterMap(
                      options: MapOptions(
                        center: latlong.LatLng(
                          _currentPosition!.latitude,
                          _currentPosition!.longitude,
                        ),
                        zoom: 16.0,
                      ),
                      children: [
                        TileLayer(
                          urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
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
                                color: Colors.red,
                                size: 50,
                              ),
                            )
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 12),
                const Row(
                  children: [
                    Icon(Icons.check_circle, color: Colors.green, size: 20),
                    SizedBox(width: 8),
                    Text('Lokasi GPS berhasil dikunci', style: TextStyle(color: Colors.green, fontWeight: FontWeight.bold)),
                  ],
                ),
              ] else if (_errorGps != null) ...[
                Container(
                  height: 220,
                  width: double.infinity,
                  decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(20)),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Icon(Icons.location_off, color: Colors.red, size: 40),
                      const SizedBox(height: 8),
                      Text(_errorGps!, style: const TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
                    ],
                  ),
                )
              ] else ...[
                Container(
                  height: 220,
                  width: double.infinity,
                  decoration: BoxDecoration(color: Colors.grey.shade100, borderRadius: BorderRadius.circular(20)),
                  child: const Center(child: CircularProgressIndicator(color: Color(0xFF43C59E))),
                )
              ]
            ],

            const SizedBox(height: 24),

            // Keterangan & Lampiran (Hanya opsional untuk Hadir, tapi Wajib untuk Izin/Sakit)
            TextFormField(
              controller: _keteranganController,
              decoration: InputDecoration(
                labelText: _selectedPresensiType == PresensiType.hadir ? 'Catatan (Opsional)' : 'Alasan Lengkap (Wajib)',
                prefixIcon: const Icon(Icons.edit_note, color: Color(0xFF43C59E)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(16),
                  borderSide: const BorderSide(color: Color(0xFF43C59E), width: 2),
                ),
              ),
              maxLines: 3,
            ),
            const SizedBox(height: 16),

            // Bukti File
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      side: BorderSide(color: _buktiFile != null ? Colors.green : const Color(0xFF43C59E)),
                    ),
                    onPressed: _pickBukti,
                    icon: Icon(
                      _buktiFile != null ? Icons.check_circle : Icons.camera_alt,
                      color: _buktiFile != null ? Colors.green : const Color(0xFF43C59E),
                    ),
                    label: Text(
                      _buktiFile != null ? 'Bukti Terlampir' : 'Lampirkan Foto/Selfie',
                      style: TextStyle(
                        color: _buktiFile != null ? Colors.green : const Color(0xFF43C59E),
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ),
                if (_buktiFile != null) ...[
                  const SizedBox(width: 12),
                  IconButton(
                    icon: const Icon(Icons.delete, color: Colors.red),
                    onPressed: () {
                      setState(() {
                        _buktiFile = null;
                      });
                    },
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

            // Tombol Kirim
            Consumer<AttendanceProvider>(
              builder: (context, provider, child) {
                return SizedBox(
                  width: double.infinity,
                  height: 55,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF43C59E),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    ),
                    onPressed: provider.isLoading ? null : _submitAttendance,
                    child: provider.isLoading
                        ? const CircularProgressIndicator(color: Colors.white)
                        : Text(
                            'KIRIM DATA ${_getTabName(_selectedPresensiType).toUpperCase()}',
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 1.2,
                              color: Colors.white,
                            ),
                          ),
                  ),
                );
              },
            ),
          ],
        ),
      ),
    );
  }
}
