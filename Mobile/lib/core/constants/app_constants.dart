import 'package:flutter/material.dart';

class AppConstants {
  // Default LAN endpoint for local development on a physical device.
  // Override BACKEND_HOST only when the laptop's network address changes.
  static const String backendHost = String.fromEnvironment(
    'BACKEND_HOST',
    defaultValue: '192.168.0.104',
  );
  static const String baseUrl = 'http://$backendHost:8000/api/v1';

  /// Root URL (without /api/v1) — used for storage URLs and broadcasting auth.
  static String get rootUrl =>
      baseUrl.replaceFirst(RegExp(r'/api/v1$'), '');

  /// Base URL for public storage assets: e.g. $storageBaseUrl/attendances/proof.jpg
  static String get storageBaseUrl => '$rootUrl/storage';

  // Broadcasting (Reverb) config
  static const String reverbKey = String.fromEnvironment(
    'REVERB_APP_KEY',
    defaultValue: 'local-key',
  );
  static const String reverbHost = backendHost;
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

