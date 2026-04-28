import 'package:flutter/material.dart';

class AppConstants {
  // Ganti IP ini sesuaikan dengan IPv4 Local Anda atau Tautan Ngrok / VPS Production Anda
  // - Android Emulator  : gunakan 10.0.2.2 (alias loopback ke host PC)
  // - Device Fisik      : gunakan IPv4 komputer (cek via `ipconfig`), contoh: 192.168.1.x
  static const String baseUrl = 'http://127.0.0.1:8000/api/v1';

  /// Root URL (without /api/v1) — used for storage URLs and broadcasting auth.
  static String get rootUrl =>
      baseUrl.replaceFirst(RegExp(r'/api/v1$'), '');

  /// Base URL for public storage assets: e.g. $storageBaseUrl/attendances/proof.jpg
  static String get storageBaseUrl => '$rootUrl/storage';

  // Broadcasting (Reverb) config
  static const String reverbKey = 'jcmmlbeczzzo8yfemdvf';
  static const String reverbHost = '127.0.0.1';
  static const int reverbPort = 8080;

  // SharedPreferences keys
  static const String tokenKey = 'AUTH_TOKEN_KEY';
  static const String userKey = 'AUTH_USER_KEY';

  // --- UI Theme Redesign Colors ---
  static const Color colorPrimaryBase = Color(0xFF00D9B5);   // Teal / Cyan
  static const Color colorSecondaryBase = Color(0xFF6C63FF); // Deep Purple
  static const Color colorBackgroundDark = Color(0xFF0D0E1C);// Deep Navy
  static const Color colorCardDark = Color(0xFF1E1E2E);      // Dark Card
  static const Color colorTextPrimary = Color(0xFFFFFFFF);
  static const Color colorTextSecondary = Color(0xFFA0A0AB);
}

