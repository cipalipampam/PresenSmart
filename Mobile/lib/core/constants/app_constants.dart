class AppConstants {
  // Ganti IP ini sesuaikan dengan IPv4 Local Anda atau Tautan Ngrok / VPS Production Anda
  // - Android Emulator  : gunakan 10.0.2.2 (alias loopback ke host PC)
  // - Device Fisik      : gunakan IPv4 komputer (cek via `ipconfig`), contoh: 192.168.1.x
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  
  static const String tokenKey = 'AUTH_TOKEN_KEY';
  static const String userKey = 'AUTH_USER_KEY';
}
