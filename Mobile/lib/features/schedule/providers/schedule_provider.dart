import 'package:flutter/foundation.dart';
import '../../../core/network/api_client.dart';
import '../models/class_attendance_model.dart';
import '../models/schedule_model.dart';

class ScheduleProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();

  String? _userRole;
  int _selectedDay = _currentDayOfWeek();
  List<ScheduleItemModel> _schedules = [];
  List<ScheduleItemModel> _todaySchedules = [];
  bool _isLoading = false;
  String? _errorMessage;

  // Class Attendance state (Teacher)
  ClassAttendanceRosterModel? _rosterData;
  bool _isRosterLoading = false;
  bool _isSavingAttendance = false;
  String? _rosterErrorMessage;

  String? get userRole => _userRole;
  int get selectedDay => _selectedDay;
  List<ScheduleItemModel> get schedules => _schedules;
  List<ScheduleItemModel> get todaySchedules => _todaySchedules;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  ClassAttendanceRosterModel? get rosterData => _rosterData;
  bool get isRosterLoading => _isRosterLoading;
  bool get isSavingAttendance => _isSavingAttendance;
  String? get rosterErrorMessage => _rosterErrorMessage;

  static int _currentDayOfWeek() {
    final weekday = DateTime.now().weekday;
    return (weekday >= 1 && weekday <= 6) ? weekday : 1;
  }

  void selectDay(int day) {
    if (_selectedDay != day) {
      _selectedDay = day;
      fetchSchedules(dayOfWeek: day);
    }
  }

  Future<void> fetchSchedules({int? dayOfWeek, bool showLoading = true}) async {
    final day = dayOfWeek ?? _selectedDay;
    _selectedDay = day;

    if (showLoading) {
      _isLoading = true;
      _errorMessage = null;
      notifyListeners();
    }

    try {
      final response = await _apiClient.client.get(
        '/schedules',
        queryParameters: {'day_of_week': day},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        _userRole = data['role'] as String?;
        final List<dynamic> rawList = data['schedules'] ?? [];
        _schedules = rawList
            .whereType<Map<String, dynamic>>()
            .map((e) => ScheduleItemModel.fromJson(e))
            .toList();
        _errorMessage = null;
      } else {
        _errorMessage = response.data['message'] as String? ?? 'Gagal memuat jadwal pelajaran.';
      }
    } catch (e) {
      _errorMessage = 'Gagal terhubung ke server.';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> fetchTodaySchedules() async {
    final today = _currentDayOfWeek();
    try {
      final response = await _apiClient.client.get(
        '/schedules',
        queryParameters: {'day_of_week': today},
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        _userRole = data['role'] as String?;
        final List<dynamic> rawList = data['schedules'] ?? [];
        _todaySchedules = rawList
            .whereType<Map<String, dynamic>>()
            .map((e) => ScheduleItemModel.fromJson(e))
            .toList();
        notifyListeners();
      }
    } catch (e) {
      debugPrint('Error fetchTodaySchedules: $e');
    }
  }

  Future<void> fetchClassRoster(int scheduleId, {String? date}) async {
    _isRosterLoading = true;
    _rosterErrorMessage = null;
    notifyListeners();

    try {
      final queryParams = <String, dynamic>{};
      if (date != null && date.isNotEmpty) {
        queryParams['attendance_date'] = date;
      }

      final response = await _apiClient.client.get(
        '/schedules/$scheduleId/class-attendance',
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        _rosterData = ClassAttendanceRosterModel.fromJson(data);
        _rosterErrorMessage = null;
      } else {
        _rosterErrorMessage =
            response.data['message'] as String? ?? 'Gagal mengambil daftar siswa kelas.';
      }
    } catch (e) {
      _rosterErrorMessage = 'Gagal terhubung ke server.';
    } finally {
      _isRosterLoading = false;
      notifyListeners();
    }
  }

  void updateStudentStatus(int studentId, String status) {
    if (_rosterData == null) return;
    final index = _rosterData!.students.indexWhere((s) => s.id == studentId);
    if (index != -1) {
      _rosterData!.students[index].status = status;
      notifyListeners();
    }
  }

  void updateStudentNotes(int studentId, String notes) {
    if (_rosterData == null) return;
    final index = _rosterData!.students.indexWhere((s) => s.id == studentId);
    if (index != -1) {
      _rosterData!.students[index].notes = notes;
      notifyListeners();
    }
  }

  void setAllPresent() {
    if (_rosterData == null) return;
    for (var student in _rosterData!.students) {
      // Don't override if prefilled from daily permission/sick leave
      if (!student.isPrefilledFromDailyAttendance) {
        student.status = 'present';
      }
    }
    notifyListeners();
  }

  Future<({bool success, String message})> saveClassAttendance(
    int scheduleId,
    String? date,
  ) async {
    if (_rosterData == null || _rosterData!.students.isEmpty) {
      return (success: false, message: 'Daftar siswa kosong.');
    }

    _isSavingAttendance = true;
    notifyListeners();

    try {
      final payload = {
        if (date != null && date.isNotEmpty) 'attendance_date': date,
        'attendances': _rosterData!.students.map((s) => s.toPayload()).toList(),
      };

      final response = await _apiClient.client.post(
        '/schedules/$scheduleId/class-attendance',
        data: payload,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        _isSavingAttendance = false;
        notifyListeners();
        return (
          success: true,
          message: response.data['message'] as String? ?? 'Presensi berhasil disimpan.',
        );
      } else {
        _isSavingAttendance = false;
        notifyListeners();
        return (
          success: false,
          message: response.data['message'] as String? ?? 'Gagal menyimpan presensi.',
        );
      }
    } catch (e) {
      _isSavingAttendance = false;
      notifyListeners();
      return (success: false, message: 'Gagal terhubung ke server.');
    }
  }
}

