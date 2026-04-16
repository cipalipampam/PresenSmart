import 'package:flutter/foundation.dart';
import '../../../core/network/api_client.dart';

class DashboardProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();

  bool _isLoading = true;
  String? _errorMessage;

  String _scheduleMasuk = '--:--';
  String _schedulePulang = '--:--';
  String _statusHariIni = 'Memuat...';
  List<String> _announcements = [];

  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  String get scheduleMasuk => _scheduleMasuk;
  String get schedulePulang => _schedulePulang;
  String get statusHariIni => _statusHariIni;
  List<String> get announcements => _announcements;

  Future<void> fetchDashboardData() async {
    _setLoading(true);
    _errorMessage = null;

    try {
      final response = await _apiClient.client.get('/dashboard');
      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        
        final schedule = data['schedule'] ?? {};
        _scheduleMasuk = schedule['masuk'] ?? '--:--';
        _schedulePulang = schedule['pulang'] ?? '--:--';
        _statusHariIni = schedule['status'] ?? 'Belum Absen';

        final List<dynamic> ancmtDynamic = data['announcements'] ?? [];
        _announcements = ancmtDynamic.map((e) => e.toString()).toList();
      } else {
        _errorMessage = response.data['message'] ?? 'Gagal memuat dashboard.';
      }
    } catch (e) {
      _errorMessage = 'Gagal terhubung ke server.';
    } finally {
      _setLoading(false);
    }
  }

  void _setLoading(bool value) {
    _isLoading = value;
    notifyListeners();
  }
}
