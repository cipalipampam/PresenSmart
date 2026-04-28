import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:provider/provider.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/dashboard_provider.dart';
import '../models/announcement_model.dart';

import '../../attendance/screens/attendance_screen.dart';
import '../../attendance/screens/history_screen.dart';
import '../../attendance/screens/history_screen.dart';
import '../../profile/screens/profile_screen.dart';
import '../../../core/widgets/floating_nav_bar.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/widgets/glass_container.dart';
import 'package:intl/intl.dart';


class DashboardScreen extends StatefulWidget {
  const DashboardScreen({Key? key}) : super(key: key);

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _selectedIndex = 0;

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, auth, child) {
        // [WEB GUARD] Jika user null (misal karena diketik via URL atau ter-logout)
        // Maka langsung block akses dan tampilkan tampilan Login.
        if (auth.currentUser == null) {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            Navigator.of(context).pushReplacementNamed('/login');
          });
          return const Scaffold(
            body: Center(child: CircularProgressIndicator(color: Color(0xFF43C59E))),
          );
        }

        final List<Widget> screens = [
          const DashboardHomeTab(),
          AttendanceScreen(
            onNavigateToHistory: () => setState(() => _selectedIndex = 2),
          ),
          HistoryScreen(),
          ProfileScreen(),
        ];

        return Scaffold(
          extendBody: true,
          body: Stack(
            children: [
              screens[_selectedIndex],
              FloatingNavBar(
                currentIndex: _selectedIndex,
                onTap: (index) {
                  setState(() {
                    _selectedIndex = index;
                  });
                },
              ),
            ],
          ),
        );
      },
    );
  }
}

class DashboardHomeTab extends StatefulWidget {
  const DashboardHomeTab({Key? key}) : super(key: key);

  @override
  State<DashboardHomeTab> createState() => _DashboardHomeTabState();
}

class _DashboardHomeTabState extends State<DashboardHomeTab> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<DashboardProvider>(context, listen: false).fetchDashboardData();
    });
  }

  Widget _buildStatCard(IconData icon, String value, String label, Color color) {
    return Expanded(
      child: GlassContainer(
        padding: const EdgeInsets.symmetric(vertical: 16),
        backgroundColor: AppConstants.colorCardDark,
        borderRadius: BorderRadius.circular(20),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: color.withOpacity(0.15),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(icon, color: color, size: 24),
            ),
            const SizedBox(height: 12),
            Text(value, style: const TextStyle(color: Colors.white, fontSize: 18, fontWeight: FontWeight.bold)),
            const SizedBox(height: 4),
            Text(label, style: const TextStyle(color: AppConstants.colorTextSecondary, fontSize: 12)),
          ],
        ),
      ),
    );
  }

  Widget _buildScheduleItem(IconData icon, String title, String time, Color iconColor) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: iconColor.withOpacity(0.15),
            borderRadius: BorderRadius.circular(10),
          ),
          child: Icon(icon, color: iconColor, size: 20),
        ),
        const SizedBox(width: 12),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(title, style: const TextStyle(color: AppConstants.colorTextSecondary, fontSize: 12)),
            const SizedBox(height: 4),
            Text(time, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.bold)),
          ],
        ),
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    // Consume User Data automatically from AuthProvider
    final user = Provider.of<AuthProvider>(context).currentUser;
    final userName = user?.name ?? '-';

    return Stack(
      children: [
        // Abstract Background elements
        Positioned(
          top: -150,
          right: -50,
          child: Container(
            width: 300,
            height: 300,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppConstants.colorSecondaryBase.withOpacity(0.15),
              boxShadow: [
                BoxShadow(color: AppConstants.colorSecondaryBase.withOpacity(0.2), blurRadius: 100),
              ],
            ),
          ),
        ),
        Positioned(
          top: 150,
          left: -100,
          child: Container(
            width: 200,
            height: 200,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppConstants.colorPrimaryBase.withOpacity(0.1),
              boxShadow: [
                BoxShadow(color: AppConstants.colorPrimaryBase.withOpacity(0.2), blurRadius: 80),
              ],
            ),
          ),
        ),
        
        Consumer<DashboardProvider>(
          builder: (context, dashboard, child) {
            return RefreshIndicator(
              color: AppConstants.colorPrimaryBase,
              backgroundColor: AppConstants.colorCardDark,
              onRefresh: () async {
                await dashboard.fetchDashboardData();
              },
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(24, 60, 24, 120), // Padding bottom for FloatingNav
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // HEADER: Greeting
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Haii, ${userName.split(' ').first} 👋',
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 24,
                                  fontWeight: FontWeight.bold,
                                ),
                              ).animate().fadeIn(duration: 500.ms).slideX(begin: -0.2),
                              const SizedBox(height: 4),
                              Text(
                                'Date, ${DateFormat("dd MMM yyyy", "id_ID").format(DateTime.now())}',
                                style: const TextStyle(
                                  color: AppConstants.colorTextSecondary,
                                  fontSize: 14,
                                ),
                              ).animate().fadeIn(duration: 500.ms).slideX(begin: -0.2, delay: 100.ms),
                            ],
                          ),
                          Container(
                            padding: const EdgeInsets.all(10),
                            decoration: BoxDecoration(
                              color: AppConstants.colorCardDark,
                              shape: BoxShape.circle,
                              border: Border.all(color: Colors.white.withOpacity(0.1)),
                            ),
                            child: const Icon(
                              Icons.notifications_none_rounded,
                              color: Colors.white,
                            ),
                          ).animate().fadeIn(duration: 500.ms).scale(begin: const Offset(0.8, 0.8)),
                        ],
                      ),
                      
                      // HERO CARD
                      Container(
                        width: double.infinity,
                        margin: const EdgeInsets.symmetric(vertical: 24),
                        padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 24),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(32),
                          gradient: const LinearGradient(
                            colors: [Color(0xFF6C63FF), Color(0xFF00D9B5)],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF00D9B5).withOpacity(0.3),
                              blurRadius: 20,
                              spreadRadius: -5,
                              offset: const Offset(0, 10),
                            )
                          ],
                        ),
                        child: Column(
                          children: [
                            const Text(
                              "Today's Check In",
                              style: TextStyle(color: Colors.white70, fontSize: 13, fontWeight: FontWeight.bold, letterSpacing: 1.2),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              dashboard.statusHariIni,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 32,
                                fontWeight: FontWeight.w900,
                              ),
                            ),
                            const SizedBox(height: 32),
                            // Button pulsing
                            Stack(
                              alignment: Alignment.center,
                              children: [
                                // Pulse effect layers
                                Container(
                                  width: 180,
                                  height: 52,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(26),
                                    border: Border.all(color: Colors.white30, width: 2),
                                  ),
                                ).animate(onPlay: (controller) => controller.repeat())
                                 .scaleXY(begin: 1.0, end: 1.25, duration: 1500.ms, curve: Curves.easeOut)
                                 .fadeOut(duration: 1500.ms, curve: Curves.easeOut),
                                 
                                Container(
                                  width: 170,
                                  height: 48,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(24),
                                    border: Border.all(color: Colors.white54, width: 2),
                                  ),
                                ).animate(onPlay: (controller) => controller.repeat())
                                 .scaleXY(begin: 1.0, end: 1.15, duration: 1500.ms, delay: 300.ms, curve: Curves.easeOut)
                                 .fadeOut(duration: 1500.ms, delay: 300.ms, curve: Curves.easeOut),

                                // Main button
                                Container(
                                  width: 160,
                                  height: 44,
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(22),
                                    boxShadow: const [
                                      BoxShadow(color: Colors.black26, blurRadius: 8, offset: Offset(0, 4))
                                    ]
                                  ),
                                  child: const Row(
                                    mainAxisAlignment: MainAxisAlignment.center,
                                    children: [
                                      Icon(Icons.fingerprint, color: Color(0xFF6C63FF), size: 20),
                                      SizedBox(width: 8),
                                      Text(
                                        'Attendance',
                                        style: TextStyle(
                                          color: Color(0xFF6C63FF),
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                    ],
                                  ),
                                )
                              ],
                            ),
                          ],
                        ),
                      ).animate().fadeIn(duration: 600.ms).slideY(begin: 0.2),

                      // 3 Summary Cards
                      Row(
                        children: [
                          _buildStatCard(Icons.person_pin_rounded, dashboard.hadirCount.toString(), 'Hadir', Colors.teal),
                          const SizedBox(width: 12),
                          _buildStatCard(Icons.warning_amber_rounded, dashboard.izinCount.toString(), 'Izin', Colors.orange),
                          const SizedBox(width: 12),
                          _buildStatCard(Icons.gpp_bad_rounded, dashboard.alfaCount.toString(), 'Alfa', Colors.redAccent),
                        ],
                      ).animate().fadeIn(duration: 700.ms).slideY(begin: 0.2),
                      
                      const SizedBox(height: 32),

                      // Jadwal Hari Ini
                      const Text(
                        'Jadwal Hari Ini',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ).animate().fadeIn(duration: 500.ms),
                      const SizedBox(height: 16),
                      GlassContainer(
                        padding: const EdgeInsets.symmetric(vertical: 20),
                        backgroundColor: AppConstants.colorCardDark,
                        child: Row(
                          children: [
                            Expanded(child: _buildScheduleItem(Icons.check_circle, 'Check-in', dashboard.scheduleMasuk, Colors.teal)),
                            Container(width: 1, height: 40, color: Colors.white10),
                            Expanded(child: _buildScheduleItem(Icons.exit_to_app, 'Check-out', dashboard.schedulePulang, Colors.redAccent)),
                          ],
                        ),
                      ).animate().fadeIn(duration: 800.ms).slideY(begin: 0.2),
                      
                      const SizedBox(height: 48),

                      const Text(
                        'PENGUMUMAN TERBARU',
                        style: TextStyle(
                          color: AppConstants.colorTextSecondary,
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1.5,
                        ),
                      ).animate().fadeIn(duration: 500.ms),
                      const SizedBox(height: 16),
                      
                      if (dashboard.isLoading)
                        const Center(
                            child: Padding(
                                padding: EdgeInsets.all(24.0),
                                child: CircularProgressIndicator(color: AppConstants.colorPrimaryBase)))
                      else if (dashboard.announcements.isEmpty)
                        GlassContainer(
                          padding: const EdgeInsets.all(20),
                          backgroundColor: Colors.white.withOpacity(0.02),
                          child: const Center(
                            child: Text(
                              'Belum ada pengumuman',
                              style: TextStyle(color: AppConstants.colorTextSecondary),
                            ),
                          ),
                        )
                      else
                        ...dashboard.announcements.map((item) {
                          return Padding(
                            padding: const EdgeInsets.only(bottom: 12.0),
                            child: GlassContainer(
                              padding: const EdgeInsets.all(16),
                              backgroundColor: AppConstants.colorCardDark,
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Container(
                                    width: 4,
                                    height: 40,
                                    decoration: BoxDecoration(
                                      color: Colors.orangeAccent,
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  const SizedBox(width: 16),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          item.title,
                                          style: const TextStyle(
                                            color: Colors.white,
                                            fontWeight: FontWeight.bold,
                                            fontSize: 15,
                                          ),
                                        ),
                                        if (item.content != null && item.content!.isNotEmpty) ...[
                                          const SizedBox(height: 6),
                                          Text(
                                            item.content!,
                                            style: const TextStyle(
                                              color: AppConstants.colorTextSecondary,
                                              fontSize: 13,
                                            ),
                                          ),
                                        ]
                                      ],
                                    ),
                                  )
                                ],
                              ),
                            ),
                          ).animate().fadeIn(duration: 500.ms).slideX(begin: 0.2);
                        }).toList(),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ],
    );
  }
}
