import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../constants/app_constants.dart';

/// Stores the bearer token in platform-backed encrypted storage.
///
/// The SharedPreferences fallback is read only once to migrate sessions from
/// app versions before secure storage was introduced.
class SessionStorage {
  static const FlutterSecureStorage _secureStorage = FlutterSecureStorage();

  Future<String?> readToken() async {
    final secureToken = await _secureStorage.read(key: AppConstants.tokenKey);
    if (secureToken != null) {
      return secureToken;
    }

    final preferences = await SharedPreferences.getInstance();
    final legacyToken = preferences.getString(AppConstants.tokenKey);
    if (legacyToken != null) {
      await _secureStorage.write(
        key: AppConstants.tokenKey,
        value: legacyToken,
      );
      await preferences.remove(AppConstants.tokenKey);
    }

    return legacyToken;
  }

  Future<void> writeToken(String token) {
    return _secureStorage.write(key: AppConstants.tokenKey, value: token);
  }

  Future<void> deleteToken() async {
    await _secureStorage.delete(key: AppConstants.tokenKey);

    // Remove any token left by an older app version.
    final preferences = await SharedPreferences.getInstance();
    await preferences.remove(AppConstants.tokenKey);
  }
}
