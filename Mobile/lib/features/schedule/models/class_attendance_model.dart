import 'schedule_model.dart';

class ClassAttendanceRosterModel {
  final ScheduleItemModel schedule;
  final String attendanceDate;
  final List<ClassStudentAttendanceModel> students;

  ClassAttendanceRosterModel({
    required this.schedule,
    required this.attendanceDate,
    required this.students,
  });

  factory ClassAttendanceRosterModel.fromJson(Map<String, dynamic> json) {
    final scheduleJson = json['schedule'] is Map<String, dynamic>
        ? json['schedule'] as Map<String, dynamic>
        : <String, dynamic>{};

    final rawStudents = json['students'] as List<dynamic>? ?? [];

    return ClassAttendanceRosterModel(
      schedule: ScheduleItemModel.fromJson(scheduleJson),
      attendanceDate: json['attendance_date'] as String? ?? '',
      students: rawStudents
          .whereType<Map<String, dynamic>>()
          .map((e) => ClassStudentAttendanceModel.fromJson(e))
          .toList(),
    );
  }
}

class ClassStudentAttendanceModel {
  final int id; // students.id
  final String? nis;
  final String name;
  String status; // 'present', 'late', 'sick', 'permission', 'absent'
  String? notes;
  final bool isSaved;
  final bool isPrefilledFromDailyAttendance;

  ClassStudentAttendanceModel({
    required this.id,
    this.nis,
    required this.name,
    required this.status,
    this.notes,
    required this.isSaved,
    required this.isPrefilledFromDailyAttendance,
  });

  factory ClassStudentAttendanceModel.fromJson(Map<String, dynamic> json) {
    return ClassStudentAttendanceModel(
      id: json['id'] as int,
      nis: json['nis']?.toString(),
      name: json['name'] as String? ?? 'Siswa',
      status: json['status'] as String? ?? 'present',
      notes: json['notes'] as String?,
      isSaved: json['is_saved'] == true || json['is_saved'] == 1,
      isPrefilledFromDailyAttendance:
          json['is_prefilled_from_daily_attendance'] == true ||
          json['is_prefilled_from_daily_attendance'] == 1,
    );
  }

  Map<String, dynamic> toPayload() {
    return {
      'student_id': id,
      'status': status,
      if (notes != null && notes!.isNotEmpty) 'notes': notes,
    };
  }
}

