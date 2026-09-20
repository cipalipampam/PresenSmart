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

  // --- UI Theme: Modern Enterprise EdTech (Clean Slate & Royal Blue) ---
  static const Color colorPrimaryBase = Color(0xFF2563EB);   // Royal Blue 600
  static const Color colorPrimaryDark = Color(0xFF1E40AF);   // Royal Blue 800
  static const Color colorPrimaryLight = Color(0xFFEFF6FF);  // Blue 50
  static const Color colorSecondaryBase = Color(0xFF0F172A); // Slate 900

  // Canvas & Surfaces
  static const Color colorBackground = Color(0xFFF8FAFC);    // Slate 50
  static const Color colorSurface = Color(0xFFFFFFFF);       // Crisp White Card
  static const Color colorBorder = Color(0xFFE2E8F0);        // Slate 200
  static const Color colorBorderSubtle = Color(0xFFF1F5F9);  // Slate 100

  // Compatibility aliases for existing screens during transition
  static const Color colorBackgroundDark = Color(0xFFF8FAFC);
  static const Color colorCardDark = Color(0xFFFFFFFF);

  // Typography
  static const Color colorTextPrimary = Color(0xFF0F172A);   // Slate 900
  static const Color colorTextSecondary = Color(0xFF64748B); // Slate 500
  static const Color colorTextMuted = Color(0xFF94A3B8);     // Slate 400

  // Semantic Status Colors
  static const Color colorPresent = Color(0xFF059669);       // Emerald 600
  static const Color colorPresentBg = Color(0xFFECFDF5);     // Emerald 50
  static const Color colorLate = Color(0xFFD97706);          // Amber 600
  static const Color colorLateBg = Color(0xFFFFFBEB);        // Amber 50
  static const Color colorSick = Color(0xFF0284C7);          // Sky 600
  static const Color colorSickBg = Color(0xFFF0F9FF);        // Sky 50
  static const Color colorPermission = Color(0xFF4F46E5);    // Indigo 600
  static const Color colorPermissionBg = Color(0xFFEEF2FF);  // Indigo 50
  static const Color colorAbsent = Color(0xFFE11D48);        // Rose 600
  static const Color colorAbsentBg = Color(0xFFFFF1F2);      // Rose 50
}
