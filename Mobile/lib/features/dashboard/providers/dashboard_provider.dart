import 'dart:async';

import 'package:flutter/foundation.dart';
import '../../../core/network/api_client.dart';
import '../../../core/services/websocket_service.dart';
import '../models/announcement_model.dart';

class DashboardProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();

  bool _isLoading = true;
  String? _errorMessage;

  String _scheduleMasuk = '--:--';
  String _schedulePulang = '--:--';
  String _statusHariIni = 'Memuat...';
  
  int _hadirCount = 0;
  int _izinCount = 0;
  int _alfaCount = 0;
  String? _approvalMessage;
  bool? _approvalAccepted;
  int _approvalNoticeVersion = 0;

  // Fix #2: Changed from List<String> to List<AnnouncementModel>
  List<AnnouncementModel> _announcements = [];

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  String get scheduleMasuk => _scheduleMasuk;
  String get schedulePulang => _schedulePulang;
  String get statusHariIni => _statusHariIni;
  int get hadirCount => _hadirCount;
  int get izinCount => _izinCount;
  int get alfaCount => _alfaCount;
  List<AnnouncementModel> get announcements => _announcements;
  String? get approvalMessage => _approvalMessage;
  bool? get approvalAccepted => _approvalAccepted;
  int get approvalNoticeVersion => _approvalNoticeVersion;

  DashboardProvider() {
    // Fix #3: WebSocket listeners registered ONCE in constructor, not inside fetchDashboardData().
    // This prevents callback overwrite and stale-closure bugs.
    WebSocketService().addAnnouncementListener((data) {
      debugPrint('Dashboard: announcement event received; fetching latest data.');
      unawaited(fetchDashboardData(showLoading: false));
    });
    WebSocketService().addSettingsListener((data) {
      debugPrint('Dashboard: settings event received; fetching latest data.');
      unawaited(fetchDashboardData(showLoading: false));
    });
    WebSocketService().addAttendanceApprovalListener((data) {
      _approvalMessage = data['message'] as String? ?? 'Status pengajuan presensi telah diperbarui.';
      _approvalAccepted = data['is_approved'] as bool?;
      _approvalNoticeVersion++;
      notifyListeners();
      unawaited(fetchDashboardData(showLoading: false));
    });
  }

  Future<void> fetchDashboardData({bool showLoading = true}) async {
    if (showLoading) _setLoading(true);
    _errorMessage = null;

    try {
      final response = await _apiClient.client.get('/dashboard');
      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];

        final schedule = data['schedule'] ?? {};
        _scheduleMasuk = schedule['masuk'] ?? '--:--';
        _schedulePulang = schedule['pulang'] ?? '--:--';
        _statusHariIni = schedule['status'] ?? 'Belum Absen';

        final stats = data['stats'] ?? {};
        _hadirCount = stats['hadir'] ?? 0;
        _izinCount = stats['izin'] ?? 0;
        _alfaCount = stats['alfa'] ?? 0;

        // Fix #2: Parse announcements as structured objects, not toString()
        final List<dynamic> rawAnnouncements = data['announcements'] ?? [];
        _announcements = rawAnnouncements
            .whereType<Map<String, dynamic>>()
            .map((e) => AnnouncementModel.fromJson(e))
            .toList();
      } else {
        _errorMessage = response.data['message'] ?? 'Gagal memuat dashboard.';
      }
    } catch (e) {
      _errorMessage = 'Gagal terhubung ke server.';
    } finally {
      if (showLoading) {
        _setLoading(false);
      } else {
        notifyListeners();
      }
    }
  }

  void _setLoading(bool value) {
    _isLoading = value;
    notifyListeners();
  }
}
