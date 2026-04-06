import 'dart:convert';
import 'package:http/http.dart' as http;

class ApiService {
  static const String baseUrl =
      'https://87bd8c9d633f.ngrok-free.app/api'; // Ganti dengan URL API backend

  // Login API
  static Future<http.Response> login(String email, String password) async {
    final url = Uri.parse('$baseUrl/login');
    return await http.post(
      url,
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );
  }

  // Presensi (Attendance) API
  static Future<http.Response> presensi({
    required String token,
    required String tipePresensi,
    String? keterangan,
    double? lat,
    double? long,
    String? buktiFilePath,
  }) async {
    var request = http.MultipartRequest('POST', Uri.parse('$baseUrl/presensi'));
    request.headers['Authorization'] = 'Bearer $token';
    request.fields['tipe_presensi'] = tipePresensi;
    if (keterangan != null) request.fields['keterangan'] = keterangan;
    if (lat != null && long != null) {
      request.fields['lat'] = lat.toString();
      request.fields['long'] = long.toString();
    }
    if (buktiFilePath != null) {
      request.files.add(
        await http.MultipartFile.fromPath(
          'bukti',
          buktiFilePath,
          filename: 'bukti_${DateTime.now().millisecondsSinceEpoch}.jpg',
        ),
      );
    }
    var streamedResponse = await request.send();
    return await http.Response.fromStream(streamedResponse);
  }

  // Riwayat Presensi (History) API
  static Future<http.Response> riwayatPresensi(String token) async {
    final url = Uri.parse('$baseUrl/presensi/riwayat');
    return await http.get(
      url,
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );
  }

  // Statistik Presensi API
  static Future<http.Response> getStatistikPresensi(String token) async {
    final url = Uri.parse('$baseUrl/statistik');
    return await http.get(
      url,
      headers: {
        'Authorization': 'Bearer $token',
        'Content-Type': 'application/json',
      },
    );
  }

  // Tambahkan fungsi API lain di sini sesuai kebutuhan aplikasi
}
