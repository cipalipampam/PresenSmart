import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:epresensi/login_page.dart';
import 'session_manager.dart';

class ProfilPage extends StatelessWidget {
  final Map<String, dynamic> user;

  static final Color _primaryColor = Color(0xFF7ED6A8);
  static final Color _secondaryColor = Color(0xFF43C59E);
  static final Color _accentColor = Color(0xFF2D9C7A);

  const ProfilPage({Key? key, required this.user}) : super(key: key);

  Widget _buildProfileItem({
    required IconData icon,
    required String title,
    required String subtitle,
    Color? iconColor,
  }) {
    return Column(
      children: [
        Row(
          children: [
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: (iconColor ?? _primaryColor).withOpacity(0.12),
                shape: BoxShape.circle,
              ),
              child: Icon(icon, color: iconColor ?? _primaryColor, size: 24),
            ),
            const SizedBox(width: 16),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: TextStyle(
                      color: Colors.grey[700],
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    subtitle,
                    style: TextStyle(
                      color: Colors.black87,
                      fontSize: 16,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
        const SizedBox(height: 8),
        Divider(height: 1, color: Colors.grey.shade200, thickness: 1),
        const SizedBox(height: 8),
      ],
    );
  }

  String _translateEnum(String? value) {
    if (value == null) return '-';
    switch (value) {
      case 'laki_laki':
        return 'Laki-laki';
      case 'perempuan':
        return 'Perempuan';
      case 'islam':
        return 'Islam';
      case 'kristen':
        return 'Kristen';
      case 'katholik':
        return 'Katholik';
      case 'hindu':
        return 'Hindu';
      case 'buddha':
        return 'Buddha';
      case 'konghucu':
        return 'Konghucu';
      default:
        return value;
    }
  }

  String _safeToString(dynamic value) {
    if (value == null) return '-';
    if (value is String) return value;
    return value.toString();
  }

  String _generateFotoUrl(dynamic fotoPath) {
    if (fotoPath == null) return '';
    if (fotoPath.toString().startsWith('http://') ||
        fotoPath.toString().startsWith('https://')) {
      return fotoPath.toString();
    }
    return 'http://192.168.1.20:8080/storage/$fotoPath';
  }

  Widget _buildCroppedCircleAvatar(String imageUrl) {
    return Material(
      elevation: 8,
      shape: const CircleBorder(),
      child: ClipOval(
        child: SizedBox(
          width: 130,
          height: 130,
          child: LayoutBuilder(
            builder: (context, constraints) {
              return CachedNetworkImage(
                imageUrl: imageUrl,
                width: constraints.maxWidth,
                height: constraints.maxHeight,
                fit: BoxFit.cover,
                alignment: const Alignment(0, -0.2),
                placeholder: (context, url) => const CircularProgressIndicator(),
                errorWidget: (context, url, error) =>
                    const Icon(Icons.person, size: 60, color: Colors.grey),
              );
            },
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Stack(
        children: [
          // Header background dengan curve
          ClipPath(
            clipper: _HeaderClipper(),
            child: Container(
              height: 260,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [_primaryColor, _secondaryColor],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                ),
              ),
            ),
          ),
          SingleChildScrollView(
            physics: const BouncingScrollPhysics(),
            child: Column(
              children: [
                const SizedBox(height: 60),
                // Foto profil dengan efek shadow
                Center(
                  child: Hero(
                    tag: 'profile-avatar',
                    child: _buildCroppedCircleAvatar(
                      _generateFotoUrl(user['foto']),
                    ),
                  ),
                ).animate().fadeIn(duration: 600.ms, delay: 100.ms),
                const SizedBox(height: 18),
                // Nama
                Text(
                  _safeToString(user['name']),
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: _accentColor,
                        fontSize: 24,
                      ),
                  textAlign: TextAlign.center,
                ).animate().fadeIn(duration: 500.ms, delay: 200.ms),
                const SizedBox(height: 6),
                // Email
                Text(
                  _safeToString(user['email']),
                  style: TextStyle(
                    color: Colors.grey[700],
                    fontSize: 15,
                    fontWeight: FontWeight.w500,
                  ),
                  textAlign: TextAlign.center,
                ).animate().fadeIn(duration: 500.ms, delay: 300.ms),
                const SizedBox(height: 28),
                // Kartu Identitas
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 18),
                  child: _buildInfoCard(
                    context,
                    title: 'Identitas Siswa',
                    icon: Icons.account_circle,
                    children: [
                      _buildProfileItem(
                        icon: Icons.credit_card,
                        title: 'NISN',
                        subtitle: _safeToString(user['nisn']),
                        iconColor: _primaryColor,
                      ),
                      _buildProfileItem(
                        icon: Icons.school,
                        title: 'NIS',
                        subtitle: _safeToString(user['nis']),
                        iconColor: _primaryColor,
                      ),
                      _buildProfileItem(
                        icon: Icons.class_outlined,
                        title: 'Kelas',
                        subtitle: _safeToString(user['kelas']),
                        iconColor: _primaryColor,
                      ),
                      _buildProfileItem(
                        icon: Icons.wc,
                        title: 'Jenis Kelamin',
                        subtitle: _translateEnum(_safeToString(user['jenis_kelamin'])),
                        iconColor: _primaryColor,
                      ),
                    ],
                  ).animate().slideX(begin: 0.2, duration: 500.ms, delay: 350.ms),
                ),
                const SizedBox(height: 18),
                // Kartu Informasi Tambahan
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 18),
                  child: _buildInfoCard(
                    context,
                    title: 'Informasi Tambahan',
                    icon: Icons.info,
                    children: [
                      _buildProfileItem(
                        icon: Icons.cake,
                        title: 'Tempat, Tanggal Lahir',
                        subtitle: user['tempat_lahir'] != null && user['tanggal_lahir'] != null
                            ? '${_safeToString(user['tempat_lahir'])}, ${_safeToString(user['tanggal_lahir'])}'
                            : '-',
                        iconColor: _primaryColor,
                      ),
                      _buildProfileItem(
                        icon: Icons.mosque,
                        title: 'Agama',
                        subtitle: _translateEnum(_safeToString(user['agama'])),
                        iconColor: _primaryColor,
                      ),
                      _buildProfileItem(
                        icon: Icons.home,
                        title: 'Alamat',
                        subtitle: _safeToString(user['alamat']),
                        iconColor: _primaryColor,
                      ),
                      _buildProfileItem(
                        icon: Icons.phone,
                        title: 'Nomor Telepon',
                        subtitle: _safeToString(user['no_telp']),
                        iconColor: _primaryColor,
                      ),
                    ],
                  ).animate().slideX(begin: -0.2, duration: 500.ms, delay: 400.ms),
                ),
                const SizedBox(height: 32),
                // Tombol Logout
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 12),
                  child: SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      icon: const Icon(Icons.logout, color: Colors.white),
                      label: const Text('Keluar', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.redAccent,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(16),
                        ),
                        elevation: 4,
                      ),
                      onPressed: () {
                        showDialog(
                          context: context,
                          builder: (context) => AlertDialog(
                            title: const Text('Konfirmasi Logout'),
                            content: const Text('Apakah Anda yakin ingin keluar?'),
                            actions: [
                              TextButton(
                                onPressed: () => Navigator.of(context).pop(),
                                child: const Text('Batal'),
                              ),
                              ElevatedButton(
                                onPressed: () async {
                                  await SessionManager.clearSession();
                                  Navigator.of(context).pushAndRemoveUntil(
                                    MaterialPageRoute(
                                      builder: (context) => const LoginPage(),
                                    ),
                                    (route) => false,
                                  );
                                },
                                child: const Text('Logout'),
                              ),
                            ],
                          ),
                        );
                      },
                    ),
                  ),
                ),
                const SizedBox(height: 24),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoCard(
    BuildContext context, {
    required String title,
    required IconData icon,
    required List<Widget> children,
  }) {
    return Card(
      elevation: 6,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
      child: Container(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(18),
          gradient: LinearGradient(
            colors: [
              _primaryColor.withOpacity(0.10),
              _secondaryColor.withOpacity(0.07),
              Colors.white,
            ],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 18),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: _secondaryColor.withOpacity(0.15),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(
                      icon,
                      color: _secondaryColor,
                      size: 24,
                    ),
                  ),
                  const SizedBox(width: 16),
                  Text(
                    title,
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                          fontWeight: FontWeight.bold,
                          color: _secondaryColor,
                        ),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              ...children,
            ],
          ),
        ),
      ),
    );
  }
}

// Custom clipper untuk header curve
class _HeaderClipper extends CustomClipper<Path> {
  @override
  Path getClip(Size size) {
    Path path = Path();
    path.lineTo(0, size.height * 0.75);
    path.quadraticBezierTo(
      size.width / 2, size.height,
      size.width, size.height * 0.75,
    );
    path.lineTo(size.width, 0);
    path.close();
    return path;
  }

  @override
  bool shouldReclip(CustomClipper<Path> oldClipper) => false;
}
