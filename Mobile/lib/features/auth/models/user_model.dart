class UserModel {
  final int id;
  final String name;
  final String email;
  final String role;
  
  // Eager-loaded profile relations
  final String? nisn;
  final String? nis;
  final String? nip;
  final String? grade;
  final String? position;
  final String? gender;
  final String? placeOfBirth;
  final String? dateOfBirth;
  final String? religion;
  final String? address;
  final String? phoneNumber;
  final String? profilePicture;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    this.nisn,
    this.nis,
    this.nip,
    this.grade,
    this.position,
    this.gender,
    this.placeOfBirth,
    this.dateOfBirth,
    this.religion,
    this.address,
    this.phoneNumber,
    this.profilePicture,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    Map<String, dynamic>? extraData;
    if (json['student'] != null) {
      extraData = json['student'];
    } else if (json['employee'] != null) {
      extraData = json['employee'];
    }

    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      role: json['role'] ?? 'user',
      nisn: extraData?['nisn']?.toString(),
      nis: extraData?['nis']?.toString(),
      nip: extraData?['nip']?.toString(),
      grade: extraData?['grade']?.toString(),
      position: extraData?['position']?.toString(),
      gender: extraData?['gender']?.toString(),
      placeOfBirth: extraData?['place_of_birth']?.toString(),
      dateOfBirth: extraData?['date_of_birth']?.toString(),
      religion: extraData?['religion']?.toString(),
      address: extraData?['address']?.toString(),
      phoneNumber: extraData?['phone_number']?.toString(),
      profilePicture: extraData?['profile_picture']?.toString(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'role': role,
      'student': role == 'siswa' ? _buildExtraJson() : null,
      'employee': (role == 'guru' || role == 'staff') ? _buildExtraJson() : null,
    };
  }

  Map<String, dynamic> _buildExtraJson() {
    return {
      'nisn': nisn,
      'nis': nis,
      'nip': nip,
      'grade': grade,
      'position': position,
      'gender': gender,
      'place_of_birth': placeOfBirth,
      'date_of_birth': dateOfBirth,
      'religion': religion,
      'address': address,
      'phone_number': phoneNumber,
      'profile_picture': profilePicture,
    };
  }
}
