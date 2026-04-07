import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../../../core/network/api_client.dart';

class AttendanceProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();
  
  bool _isLoading = false;
  String? _errorMessage;

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<bool> checkIn({
    required double latitude,
    required double longitude,
    File? proofImage,
    String? notes,
  }) async {
    _setLoading(true);
    _errorMessage = null;

    try {
      // Build FormData for multipart request
      FormData formData = FormData.fromMap({
        'latitude': latitude,
        'longitude': longitude,
        if (notes != null && notes.isNotEmpty) 'notes': notes,
        if (proofImage != null)
          'proof_image': await MultipartFile.fromFile(
            proofImage.path,
            filename: proofImage.path.split('/').last,
          ),
      });

      final response = await _apiClient.client.post('/attendances/check-in', data: formData);

      if (response.statusCode == 200 && response.data['success'] == true) {
        _setLoading(false);
        return true;
      } else {
        _errorMessage = response.data['message'] ?? 'Gagal melakukan check-in';
        _setLoading(false);
        return false;
      }
    } on DioException catch (e) {
      _errorMessage = e.response?.data['message'] ?? 'Koneksi terputus.';
      _setLoading(false);
      return false;
    } catch (e) {
      _errorMessage = 'Kesalahan sistem: ${e.toString()}';
      _setLoading(false);
      return false;
    }
  }

  Future<bool> submitPermission({
    required String status,
    required String notes,
    File? proofImage,
  }) async {
    _setLoading(true);
    _errorMessage = null;

    try {
      FormData formData = FormData.fromMap({
        'status': status,
        'notes': notes,
        if (proofImage != null)
          'proof_image': await MultipartFile.fromFile(
            proofImage.path,
            filename: proofImage.path.split('/').last,
          ),
      });

      final response = await _apiClient.client.post('/attendances/permission', data: formData);

      if (response.statusCode == 200 && response.data['success'] == true) {
        _setLoading(false);
        return true;
      } else {
        _errorMessage = response.data['message'] ?? 'Gagal mengirim izin';
        _setLoading(false);
        return false;
      }
    } on DioException catch (e) {
      _errorMessage = e.response?.data['message'] ?? 'Koneksi terputus.';
      _setLoading(false);
      return false;
    } catch (e) {
      _errorMessage = 'Kesalahan sistem: ${e.toString()}';
      _setLoading(false);
      return false;
    }
  }

  void _setLoading(bool value) {
    _isLoading = value;
    notifyListeners();
  }
}
