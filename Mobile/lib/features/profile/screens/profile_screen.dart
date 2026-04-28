import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../auth/providers/auth_provider.dart';
import '../../auth/models/user_model.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/widgets/glass_container.dart';

class ProfileScreen extends StatelessWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    // Consume User Data
    final user = Provider.of<AuthProvider>(context).currentUser;
    final userName = user?.name ?? '-';
    final userRole = user?.role ?? 'User';
    final userEmail = user?.email ?? '-';
    final initialName = userName.isNotEmpty ? userName[0].toUpperCase() : '-';

    return Scaffold(
      backgroundColor: AppConstants.colorBackgroundDark,
      appBar: AppBar(
        title: const Text('Profil Pengguna', style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, letterSpacing: 1.2)),
        centerTitle: true,
        elevation: 0,
        backgroundColor: Colors.transparent,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: user == null
          ? const Center(child: CircularProgressIndicator(color: AppConstants.colorPrimaryBase))
          : Stack(
              children: [
                Positioned(
                  top: -80,
                  right: -50,
                  child: Container(
                    width: 250,
                    height: 250,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: AppConstants.colorSecondaryBase.withOpacity(0.15),
                      boxShadow: [BoxShadow(color: AppConstants.colorSecondaryBase.withOpacity(0.2), blurRadius: 100)],
                    ),
                  ),
                ),
                SingleChildScrollView(
                  padding: const EdgeInsets.fromLTRB(24, 16, 24, 120),
                  child: Column(
                    children: [
                      // Header Profile
                      GlassContainer(
                        width: double.infinity,
                        padding: const EdgeInsets.symmetric(vertical: 32),
                        backgroundColor: AppConstants.colorCardDark,
                        borderRadius: BorderRadius.circular(32),
                        child: Column(
                          children: [
                            _buildAvatar(user, initialName),
                            const SizedBox(height: 20),
                            Text(
                              userName,
                              style: const TextStyle(
                                fontSize: 24,
                                color: Colors.white,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                              decoration: BoxDecoration(
                                color: AppConstants.colorPrimaryBase.withOpacity(0.2),
                                border: Border.all(color: AppConstants.colorPrimaryBase.withOpacity(0.5)),
                                borderRadius: BorderRadius.circular(20),
                              ),
                              child: Text(
                                userRole.toUpperCase(),
                                style: const TextStyle(
                                  color: AppConstants.colorPrimaryBase,
                                  fontWeight: FontWeight.w800,
                                  letterSpacing: 1.5,
                                  fontSize: 12,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 32),
                      
                      const Align(
                        alignment: Alignment.centerLeft,
                        child: Text(
                          'INFORMASI DETAIL',
                          style: TextStyle(
                            color: AppConstants.colorTextSecondary,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1.5,
                            fontSize: 12,
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      
                      // Informasi Detail
                      GlassContainer(
                        padding: const EdgeInsets.all(20),
                        backgroundColor: AppConstants.colorCardDark,
                        child: Column(
                          children: [
                            _buildProfileItem(Icons.email_outlined, 'Email', userEmail),
                            const Divider(color: Colors.white10, height: 32),
                            
                            // Dynamic Render Switch
                            if (userRole == 'siswa') ..._buildStudentFields(user),
                            if (userRole == 'guru' || userRole == 'staff') ..._buildEmployeeFields(user),
                            
                            // General Profile Extension
                            ..._buildGeneralFields(user),
                          ],
                        ),
                      ),
                      
                      const SizedBox(height: 48),
                      
                      SizedBox(
                        width: double.infinity,
                        height: 56,
                        child: ElevatedButton.icon(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.redAccent.withOpacity(0.1),
                            foregroundColor: Colors.redAccent,
                            elevation: 0,
                            side: BorderSide(color: Colors.redAccent.withOpacity(0.5)),
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
                          icon: const Icon(Icons.logout_rounded),
                          label: const Text(
                            'KELUAR SESI (LOGOUT)',
                            style: TextStyle(fontWeight: FontWeight.bold, letterSpacing: 1.2),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
    );
  }

  Widget _buildAvatar(UserModel user, String initial) {
    if (user.profilePicture != null && user.profilePicture!.isNotEmpty) {
      final imgUrl = '${AppConstants.storageBaseUrl}/${user.profilePicture}';
      return CircleAvatar(
        radius: 54,
        backgroundColor: AppConstants.colorPrimaryBase,
        child: CircleAvatar(
          radius: 50,
          backgroundColor: AppConstants.colorCardDark,
          backgroundImage: CachedNetworkImageProvider(imgUrl),
        ),
      );
    }
    return CircleAvatar(
      radius: 54,
      backgroundColor: AppConstants.colorPrimaryBase,
      child: CircleAvatar(
        radius: 50,
        backgroundColor: AppConstants.colorCardDark,
        child: Text(
          initial,
          style: const TextStyle(
            fontSize: 40,
            color: AppConstants.colorPrimaryBase,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    );
  }

  List<Widget> _buildStudentFields(UserModel user) {
    return [
      _buildProfileItem(Icons.verified_user_outlined, 'NISN', user.nisn ?? '-'),
      const Divider(color: Colors.white10, height: 32),
      _buildProfileItem(Icons.format_list_numbered_outlined, 'NIS (ID Siswa)', user.nis ?? '-'),
      const Divider(color: Colors.white10, height: 32),
      _buildProfileItem(Icons.class_outlined, 'Kelas / Tingkatan', user.grade ?? '-'),
      const Divider(color: Colors.white10, height: 32),
    ];
  }

  List<Widget> _buildEmployeeFields(UserModel user) {
    return [
      _buildProfileItem(Icons.badge_outlined, 'NIP (Nomor Pegawai)', user.nip ?? '-'),
      const Divider(color: Colors.white10, height: 32),
      _buildProfileItem(Icons.work_outline, 'Jabatan / Mapel', user.position ?? '-'),
      const Divider(color: Colors.white10, height: 32),
    ];
  }

  List<Widget> _buildGeneralFields(UserModel user) {
    final gender = user.gender == 'male' ? 'Laki-Laki' : (user.gender == 'female' ? 'Perempuan' : '-');
    final ttl = '${user.placeOfBirth ?? '-'}, ${user.dateOfBirth ?? '-'}';
    
    return [
      _buildProfileItem(Icons.person_outline, 'Jenis Kelamin', gender),
      const Divider(color: Colors.white10, height: 32),
      _buildProfileItem(Icons.location_city_outlined, 'Tempat, Tanggal Lahir', ttl),
      const Divider(color: Colors.white10, height: 32),
      _buildProfileItem(Icons.phone_outlined, 'No. Telepon', user.phoneNumber ?? '-'),
      const Divider(color: Colors.white10, height: 32),
      _buildProfileItem(Icons.home_outlined, 'Alamat Lengkap', user.address ?? '-'),
    ];
  }

  Widget _buildProfileItem(IconData icon, String title, String value) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: AppConstants.colorSecondaryBase.withOpacity(0.1),
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: AppConstants.colorSecondaryBase.withOpacity(0.3)),
          ),
          child: Icon(icon, color: AppConstants.colorSecondaryBase, size: 24),
        ),
        const SizedBox(width: 16),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: const TextStyle(
                  fontSize: 13,
                  color: AppConstants.colorTextSecondary,
                ),
              ),
              const SizedBox(height: 6),
              Text(
                value,
                style: const TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w600,
                  color: Colors.white,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }
}
