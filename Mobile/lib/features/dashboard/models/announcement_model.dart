class AnnouncementModel {
  final int id;
  final String title;
  final String? content;
  final bool isActive;
  final DateTime? createdAt;

  AnnouncementModel({
    required this.id,
    required this.title,
    this.content,
    this.isActive = true,
    this.createdAt,
  });

  factory AnnouncementModel.fromJson(Map<String, dynamic> json) {
    return AnnouncementModel(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      content: json['content'],
      isActive: json['is_active'] == 1 || json['is_active'] == true,
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'].toString())?.toLocal()
          : null,
    );
  }
}
