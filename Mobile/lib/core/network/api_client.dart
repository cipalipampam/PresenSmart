import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../constants/app_constants.dart';

/// Singleton ApiClient — shared across all providers.
/// Creates a single Dio instance with auth interceptor and 401 force-logout.
class ApiClient {
  // ─── Singleton ───────────────────────────────────────────────────────────
  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;
  ApiClient._internal() {
    _initDio();
  }

  late Dio _dio;

  void _initDio() {
    _dio = Dio(BaseOptions(
      baseUrl: AppConstants.baseUrl,
      connectTimeout: const Duration(seconds: 15),
      receiveTimeout: const Duration(seconds: 15),
      headers: {
        'Accept': 'application/json',
      },
    ));

    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final prefs = await SharedPreferences.getInstance();
        final token = prefs.getString(AppConstants.tokenKey);
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (DioException e, handler) async {
        // Fix #4: Auto-logout when token is expired or revoked (401 Unauthorized)
        if (e.response?.statusCode == 401) {
          final prefs = await SharedPreferences.getInstance();
          await prefs.remove(AppConstants.tokenKey);
          await prefs.remove(AppConstants.userKey);

          // Notify the app-level navigator to redirect to login.
          // We use the callback pattern so ApiClient stays framework-agnostic.
          onUnauthorized?.call();
        }
        return handler.next(e);
      },
    ));
  }

  /// Set this callback in main.dart or AuthProvider so the app can
  /// navigate to /login when a 401 is received anywhere.
  static void Function()? onUnauthorized;

  Dio get client => _dio;
}
