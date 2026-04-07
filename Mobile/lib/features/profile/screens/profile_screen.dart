import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../auth/providers/auth_provider.dart';
import '../../auth/models/user_model.dart';
import '../../../core/constants/app_constants.dart';

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
      backgroundColor: Colors.white,
      appBar: AppBar(
        title: const Text('Profil Pengguna', style: TextStyle(fontWeight: FontWeight.bold)),
        centerTitle: true,
        elevation: 0,
        backgroundColor: const Color(0xFF43C59E),
      ),
      body: user == null
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              child: Column(
                children: [
                  // Header Profile
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.only(bottom: 40, top: 20),
                    decoration: const BoxDecoration(
                      color: Color(0xFF43C59E),
                      borderRadius: BorderRadius.only(
                        bottomLeft: Radius.circular(32),
                        bottomRight: Radius.circular(32),
                      ),
                    ),
                    child: Column(
                      children: [
                        _buildAvatar(user, initialName),
                        const SizedBox(height: 16),
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
                            color: Colors.white.withOpacity(0.2),
                            borderRadius: BorderRadius.circular(20),
                          ),
                          child: Text(
                            userRole.toUpperCase(),
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w600,
                              letterSpacing: 1.2,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 24),
                  // Informasi Detail
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 24),
                    child: Column(
                      children: [
                        _buildProfileItem(Icons.email, 'Email', userEmail),
                        const Divider(),
                        
                        // Dynamic Render Switch
                        if (userRole == 'siswa') ..._buildStudentFields(user),
                        if (userRole == 'guru' || userRole == 'staff') ..._buildEmployeeFields(user),
                        
                        // General Profile Extension
                        _buildGeneralFields(user),
                        
                        const SizedBox(height: 48),
                        
                        SizedBox(
                          width: double.infinity,
                          height: 50,
                          child: ElevatedButton.icon(
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.red.shade400,
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
                            icon: const Icon(Icons.logout, color: Colors.white),
                            label: const Text(
                              'Keluar Sesi (Logout)',
                              style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white),
                            ),
                          ),
                        ),
                        const SizedBox(height: 40),
                      ],
                    ),
                  ),
                ],
              ),
            ),
    );
  }

  Widget _buildAvatar(UserModel user, String initial) {
    if (user.profilePicture != null && user.profilePicture!.isNotEmpty) {
      final baseStorageUrl = AppConstants.baseUrl.replaceAll('/api', '');
      final imgUrl = '$baseStorageUrl/storage/${user.profilePicture}';
      return CircleAvatar(
        radius: 50,
        backgroundColor: Colors.white,
        backgroundImage: CachedNetworkImageProvider(imgUrl),
      );
    }
    return CircleAvatar(
      radius: 50,
      backgroundColor: Colors.white,
      child: Text(
        initial,
        style: const TextStyle(
          fontSize: 40,
          color: Color(0xFF43C59E),
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  List<Widget> _buildStudentFields(UserModel user) {
    return [
      _buildProfileItem(Icons.verified_user, 'NISN', user.nisn ?? '-'),
      const Divider(),
      _buildProfileItem(Icons.format_list_numbered, 'NIS (ID Siswa)', user.nis ?? '-'),
      const Divider(),
      _buildProfileItem(Icons.class_, 'Kelas / Tingkatan', user.grade ?? '-'),
      const Divider(),
    ];
  }

  List<Widget> _buildEmployeeFields(UserModel user) {
    return [
      _buildProfileItem(Icons.badge, 'NIP (Nomor Pegawai)', user.nip ?? '-'),
      const Divider(),
      _buildProfileItem(Icons.work, 'Jabatan / Mapel', user.position ?? '-'),
      const Divider(),
    ];
  }

  Widget _buildGeneralFields(UserModel user) {
    final gender = user.gender == 'male' ? 'Laki-Laki' : (user.gender == 'female' ? 'Perempuan' : '-');
    final ttl = '${user.placeOfBirth ?? '-'}, ${user.dateOfBirth ?? '-'}';
    
    return Column(
      children: [
        _buildProfileItem(Icons.person, 'Jenis Kelamin', gender),
        const Divider(),
        _buildProfileItem(Icons.location_city, 'Tempat, Tanggal Lahir', ttl),
        const Divider(),
        _buildProfileItem(Icons.phone, 'No. Telepon', user.phoneNumber ?? '-'),
        const Divider(),
        _buildProfileItem(Icons.home, 'Alamat Lengkap', user.address ?? '-'),
        const Divider(),
      ],
    );
  }

  Widget _buildProfileItem(IconData icon, String title, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 12),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: const Color(0xFF43C59E).withOpacity(0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: const Color(0xFF43C59E)),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 14,
                    color: Colors.grey,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  value,
                  style: const TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: Color(0xFF2D3A4A),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
