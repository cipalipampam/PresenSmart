import 'dart:io';
import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import '../../../core/network/api_client.dart';
import '../models/attendance_model.dart';

class AttendanceProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();
  
  bool _isLoading = false;
  String? _errorMessage;
  List<AttendanceModel> _historyList = [];

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  List<AttendanceModel> get historyList => _historyList;

  bool get hasCheckedInToday {
    if (_historyList.isEmpty) return false;
    final now = DateTime.now();
    return _historyList.any((a) => 
        a.recordedAt.year == now.year && 
        a.recordedAt.month == now.month && 
        a.recordedAt.day == now.day);
  }

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
        await fetchHistory(); // auto-sync history
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
        await fetchHistory(); // auto-sync history
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

  Future<void> fetchHistory({int? month, int? year}) async {
    _setLoading(true);
    _errorMessage = null;

    try {
      final response = await _apiClient.client.get(
        '/attendances',
        queryParameters: {
          if (month != null) 'month': month,
          if (year != null) 'year': year,
        },
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        // Karena Laravel menggunakan paginate(), datanya berlapis di ['data']['data']
        final List<dynamic> rawData = response.data['data']['data'];
        _historyList = rawData.map((e) => AttendanceModel.fromJson(e)).toList();
      } else {
        _errorMessage = response.data['message'] ?? 'Gagal memuat riwayat.';
      }
    } catch (e) {
      _errorMessage = 'Kesalahan sistem: ${e.toString()}';
    } finally {
      _setLoading(false);
    }
  }

  void _setLoading(bool value) {
    _isLoading = value;
    notifyListeners();
  }
}
