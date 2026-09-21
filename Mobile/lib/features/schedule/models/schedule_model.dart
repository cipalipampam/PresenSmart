class ScheduleItemModel {
  final int id;
  final int dayOfWeek;
  final String dayName;
  final String startTime;
  final String endTime;
  final String? room;
  final ScheduleClassroom? classroom;
  final ScheduleSubject? subject;
  final ScheduleTeacher? teacher;

  ScheduleItemModel({
    required this.id,
    required this.dayOfWeek,
    required this.dayName,
    required this.startTime,
    required this.endTime,
    this.room,
    this.classroom,
    this.subject,
    this.teacher,
  });

  factory ScheduleItemModel.fromJson(Map<String, dynamic> json) {
    return ScheduleItemModel(
      id: json['id'] as int,
      dayOfWeek: json['day_of_week'] as int? ?? 1,
      dayName: json['day_name'] as String? ?? 'Hari',
      startTime: json['start_time'] as String? ?? '00:00',
      endTime: json['end_time'] as String? ?? '00:00',
      room: json['room'] as String?,
      classroom: json['classroom'] is Map<String, dynamic>
          ? ScheduleClassroom.fromJson(json['classroom'] as Map<String, dynamic>)
          : null,
      subject: json['subject'] is Map<String, dynamic>
          ? ScheduleSubject.fromJson(json['subject'] as Map<String, dynamic>)
          : null,
      teacher: json['teacher'] is Map<String, dynamic>
          ? ScheduleTeacher.fromJson(json['teacher'] as Map<String, dynamic>)
          : null,
    );
  }

  String get timeFormatted {
    final start = startTime.length >= 5 ? startTime.substring(0, 5) : startTime;
    final end = endTime.length >= 5 ? endTime.substring(0, 5) : endTime;
    return '$start - $end WIB';
  }
}

class ScheduleClassroom {
  final int id;
  final String name;
  final String? level;
  final String? major;
  final String? section;

  ScheduleClassroom({
    required this.id,
    required this.name,
    this.level,
    this.major,
    this.section,
  });

  factory ScheduleClassroom.fromJson(Map<String, dynamic> json) {
    return ScheduleClassroom(
      id: json['id'] as int,
      name: json['name'] as String? ?? 'Kelas',
      level: json['level']?.toString(),
      major: json['major'] as String?,
      section: json['section']?.toString(),
    );
  }
}

class ScheduleSubject {
  final int id;
  final String code;
  final String name;
  final String colorCode;

  ScheduleSubject({
    required this.id,
    required this.code,
    required this.name,
    required this.colorCode,
  });

  factory ScheduleSubject.fromJson(Map<String, dynamic> json) {
    return ScheduleSubject(
      id: json['id'] as int,
      code: json['code'] as String? ?? '',
      name: json['name'] as String? ?? 'Mata Pelajaran',
      colorCode: json['color_code'] as String? ?? '#2563EB',
    );
  }
}

class ScheduleTeacher {
  final int id;
  final String name;

  ScheduleTeacher({
    required this.id,
    required this.name,
  });

  factory ScheduleTeacher.fromJson(Map<String, dynamic> json) {
    return ScheduleTeacher(
      id: json['id'] as int,
      name: json['name'] as String? ?? 'Guru Pengajar',
    );
  }
}

