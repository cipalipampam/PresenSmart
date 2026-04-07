class AttendanceModel {
  final int id;
  final DateTime recordedAt;
  final double? latitude;
  final double? longitude;
  final String status; // 'present', 'sakit', 'izin'
  final String? notes;
  final String? proofImage;
  final bool isLate;

  AttendanceModel({
    required this.id,
    required this.recordedAt,
    this.latitude,
    this.longitude,
    required this.status,
    this.notes,
    this.proofImage,
    this.isLate = false,
  });

  factory AttendanceModel.fromJson(Map<String, dynamic> json) {
    return AttendanceModel(
      id: json['id'] ?? 0,
      recordedAt: DateTime.parse(json['recorded_at']).toLocal(),
      latitude: json['latitude'] != null ? double.tryParse(json['latitude'].toString()) : null,
      longitude: json['longitude'] != null ? double.tryParse(json['longitude'].toString()) : null,
      status: json['status'] ?? 'unknown',
      notes: json['notes'],
      proofImage: json['proof_image'],
      isLate: (json['is_late'] == 1 || json['is_late'] == true),
    );
  }
}
