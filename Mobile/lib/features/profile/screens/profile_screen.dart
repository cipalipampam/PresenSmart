import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../auth/providers/auth_provider.dart';
import '../../auth/models/user_model.dart';
import '../../../core/constants/app_constants.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).currentUser;
    final userName = user?.name ?? '-';
    final userRole = user?.role ?? 'User';
    final userEmail = user?.email ?? '-';
    final initialName = userName.isNotEmpty ? userName[0].toUpperCase() : '-';

    return Scaffold(
      backgroundColor: AppConstants.colorBackground,
      appBar: AppBar(
        title: const Text(
          'Profil Akun',
          style: TextStyle(
            fontWeight: FontWeight.bold,
            color: AppConstants.colorTextPrimary,
            fontSize: 18,
          ),
        ),
        centerTitle: true,
        elevation: 0,
        backgroundColor: AppConstants.colorSurface,
        surfaceTintColor: Colors.transparent,
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(1),
          child: Container(height: 1, color: AppConstants.colorBorder),
        ),
      ),
      body: user == null
          ? const Center(child: CircularProgressIndicator(color: AppConstants.colorPrimaryBase))
          : SingleChildScrollView(
              padding: const EdgeInsets.fromLTRB(20, 20, 20, 120),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Header Profile Card
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(vertical: 28, horizontal: 20),
                    decoration: BoxDecoration(
                      color: AppConstants.colorSurface,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: AppConstants.colorBorder),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF0F172A).withValues(alpha: 0.04),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        _buildAvatar(user, initialName),
                        const SizedBox(height: 16),
                        Text(
                          userName,
                          style: const TextStyle(
                            fontSize: 20,
                            color: AppConstants.colorTextPrimary,
                            fontWeight: FontWeight.bold,
                          ),
                          textAlign: TextAlign.center,
                        ),
                        const SizedBox(height: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 5),
                          decoration: BoxDecoration(
                            color: AppConstants.colorPrimaryLight,
                            border: Border.all(color: AppConstants.colorPrimaryBase.withValues(alpha: 0.3)),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(
                            userRole.toUpperCase(),
                            style: const TextStyle(
                              color: AppConstants.colorPrimaryBase,
                              fontWeight: FontWeight.w700,
                              letterSpacing: 1.0,
                              fontSize: 12,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  
                  const Padding(
                    padding: EdgeInsets.symmetric(horizontal: 4),
                    child: Text(
                      'INFORMASI IDENTITAS & AKUN',
                      style: TextStyle(
                        color: AppConstants.colorTextSecondary,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1.2,
                        fontSize: 12,
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),
                  
                  // Informasi Detail
                  Container(
                    padding: const EdgeInsets.all(18),
                    decoration: BoxDecoration(
                      color: AppConstants.colorSurface,
                      borderRadius: BorderRadius.circular(20),
                      border: Border.all(color: AppConstants.colorBorder),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF0F172A).withValues(alpha: 0.04),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        _buildProfileItem(Icons.alternate_email_rounded, 'Alamat Email', userEmail),
                        const Divider(color: AppConstants.colorBorderSubtle, height: 24),
                        
                        // Dynamic Render Switch
                        if (userRole == 'siswa') ..._buildStudentFields(user),
                        if (userRole == 'guru' || userRole == 'staff') ..._buildEmployeeFields(user),
                        
                        // General Profile Extension
                        ..._buildGeneralFields(user),
                      ],
                    ),
                  ),
                  
                  const SizedBox(height: 32),
                  
                  // Logout Button
                  SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton.icon(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppConstants.colorAbsentBg,
                        foregroundColor: AppConstants.colorAbsent,
                        elevation: 0,
                        side: BorderSide(color: AppConstants.colorAbsent.withValues(alpha: 0.3)),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                      ),
                      onPressed: () async {
                        final auth = Provider.of<AuthProvider>(context, listen: false);
                        await auth.logout();
                        if (context.mounted) {
                          Navigator.pushReplacementNamed(context, '/login');
                        }
                      },
                      icon: const Icon(Icons.logout_rounded, size: 20),
                      label: const Text(
                        'Keluar Sesi (Logout)',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                      ),
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildAvatar(UserModel user, String initial) {
    if (user.profilePicture != null && user.profilePicture!.isNotEmpty) {
      final imgUrl = '${AppConstants.storageBaseUrl}/${user.profilePicture}';
      return CircleAvatar(
        radius: 46,
        backgroundColor: AppConstants.colorPrimaryLight,
        child: CircleAvatar(
          radius: 42,
          backgroundColor: AppConstants.colorBackground,
          backgroundImage: CachedNetworkImageProvider(imgUrl),
        ),
      );
    }
    return CircleAvatar(
      radius: 46,
      backgroundColor: AppConstants.colorPrimaryLight,
      child: CircleAvatar(
        radius: 42,
        backgroundColor: AppConstants.colorPrimaryBase,
        child: Text(
          initial,
          style: const TextStyle(
            fontSize: 32,
            color: Colors.white,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    );
  }

  List<Widget> _buildStudentFields(UserModel user) {
    return [
      _buildProfileItem(Icons.badge_outlined, 'NISN', user.nisn ?? '-'),
      const Divider(color: AppConstants.colorBorderSubtle, height: 24),
      _buildProfileItem(Icons.pin_outlined, 'NIS (ID Siswa)', user.nis ?? '-'),
      const Divider(color: AppConstants.colorBorderSubtle, height: 24),
      _buildProfileItem(Icons.class_outlined, 'Kelas / Tingkatan', user.grade ?? '-'),
      const Divider(color: AppConstants.colorBorderSubtle, height: 24),
    ];
  }

  List<Widget> _buildEmployeeFields(UserModel user) {
    return [
      _buildProfileItem(Icons.badge_outlined, 'NIP (Nomor Pegawai)', user.nip ?? '-'),
      const Divider(color: AppConstants.colorBorderSubtle, height: 24),
      _buildProfileItem(Icons.work_outline, 'Jabatan / Tugas', user.position ?? '-'),
      const Divider(color: AppConstants.colorBorderSubtle, height: 24),
    ];
  }

  List<Widget> _buildGeneralFields(UserModel user) {
    final gender = user.gender == 'male' ? 'Laki-Laki' : (user.gender == 'female' ? 'Perempuan' : '-');
    final ttl = '${user.placeOfBirth ?? '-'}, ${user.dateOfBirth ?? '-'}';
    
    return [
      _buildProfileItem(Icons.person_outline, 'Jenis Kelamin', gender),
      const Divider(color: AppConstants.colorBorderSubtle, height: 24),
      _buildProfileItem(Icons.cake_outlined, 'Tempat, Tanggal Lahir', ttl),
      const Divider(color: AppConstants.colorBorderSubtle, height: 24),
      _buildProfileItem(Icons.phone_outlined, 'Nomor Telepon', user.phoneNumber ?? '-'),
      const Divider(color: AppConstants.colorBorderSubtle, height: 24),
      _buildProfileItem(Icons.home_outlined, 'Alamat Lengkap', user.address ?? '-'),
    ];
  }

  Widget _buildProfileItem(IconData icon, String title, String value) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(10),
          decoration: BoxDecoration(
            color: AppConstants.colorPrimaryLight,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Icon(icon, color: AppConstants.colorPrimaryBase, size: 20),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 12,
                  color: AppConstants.colorTextSecondary,
                ),
              ),
              const SizedBox(height: 2),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                  color: AppConstants.colorTextPrimary,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
