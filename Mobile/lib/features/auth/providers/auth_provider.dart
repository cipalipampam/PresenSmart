import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/network/api_client.dart';
import '../../../core/services/websocket_service.dart';
import '../models/user_model.dart';

class AuthProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();
  
  UserModel? _currentUser;
  bool _isLoading = false;
  String? _errorMessage;

  UserModel? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<bool> initializeAuth() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString(AppConstants.tokenKey);
    final userJson = prefs.getString(AppConstants.userKey);

    if (token != null && userJson != null) {
      _currentUser = UserModel.fromJson(json.decode(userJson));
      notifyListeners();
      
      // Initialize WebSocket
      WebSocketService().init(token: token, userId: _currentUser?.id);

      _syncUserInBackground();
      
      return true; // Still active
    }
    return false;
  }

  Future<bool> login(String email, String password) async {
    _setLoading(true);
    _errorMessage = null;

    try {
      final response = await _apiClient.client.post('/login', data: {
        'email': email,
        'password': password,
      });

      if (response.statusCode == 200 && response.data['success'] == true) {
        final token = response.data['data']['token'];
        final userMap = response.data['data']['user'];
        // Also capture exact role
        userMap['role'] = response.data['data']['role'];

        _currentUser = UserModel.fromJson(userMap);

        // Initialize WebSocket
        WebSocketService().init(token: token, userId: _currentUser?.id);

        // Store into preferences
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(AppConstants.tokenKey, token);
        await prefs.setString(AppConstants.userKey, json.encode(userMap));

        _setLoading(false);
        return true;
      } else {
        _errorMessage = response.data['message'] ?? 'Login failed';
        _setLoading(false);
        return false;
      }
    } on DioException catch (e) {
      _errorMessage = e.response?.data['message'] ?? 'Internet terputus atau server offline.';
      _setLoading(false);
      return false;
    } catch (e) {
      _errorMessage = 'Kesalahan sistem: ${e.toString()}';
      _setLoading(false);
      return false;
    }
  }

  Future<void> logout() async {
    _setLoading(true);
    try {
      WebSocketService().disconnect();
      await _apiClient.client.post('/logout');
    } catch (e) {
      // Ignored: Force kill local session anyway even if server errors out
    } finally {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(AppConstants.tokenKey);
      await prefs.remove(AppConstants.userKey);
      
      _currentUser = null;
      _setLoading(false);
    }
  }

  void _setLoading(bool value) {
    _isLoading = value;
    notifyListeners();
  }

  Future<void> _syncUserInBackground() async {
    try {
      final response = await _apiClient.client.get('/user');
      if (response.statusCode == 200 && response.data['success'] == true) {
        final userData = response.data['data'];
        userData['role'] = userData['role_name'] ?? 'user';
        
        _currentUser = UserModel.fromJson(userData);

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString(AppConstants.userKey, json.encode(userData));

        notifyListeners();
      }
    } catch (e) {
      // Ignore background errors, let it keep using the cached user.
    }
  }
}
