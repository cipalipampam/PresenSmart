import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:provider/provider.dart';
import '../../auth/providers/auth_provider.dart';
import '../providers/dashboard_provider.dart';

import '../../attendance/screens/attendance_screen.dart';
import '../../attendance/screens/history_screen.dart';
import '../../profile/screens/profile_screen.dart';
import '../../../core/widgets/floating_nav_bar.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/widgets/app_notice.dart';
import 'package:intl/intl.dart';


class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _selectedIndex = 0;
  final Set<int> _visitedTabs = {0};

  void _selectTab(int index) {
    setState(() {
      _visitedTabs.add(index);
      _selectedIndex = index;
    });
  }

  Widget _buildTab(int index) {
    if (!_visitedTabs.contains(index)) return const SizedBox.shrink();

    return switch (index) {
      0 => const DashboardHomeTab(key: PageStorageKey('dashboard-home')),
      1 => AttendanceScreen(
          key: const PageStorageKey('attendance'),
          onNavigateToHistory: () => _selectTab(2),
        ),
      2 => const HistoryScreen(key: PageStorageKey('attendance-history')),
      3 => const ProfileScreen(key: PageStorageKey('profile')),
      _ => const SizedBox.shrink(),
    };
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, auth, child) {
        if (auth.currentUser == null) {
          WidgetsBinding.instance.addPostFrameCallback((_) {
            Navigator.of(context).pushReplacementNamed('/login');
          });
          return const Scaffold(
            backgroundColor: AppConstants.colorBackground,
            body: Center(child: CircularProgressIndicator(color: AppConstants.colorPrimaryBase)),
          );
        }

        return Scaffold(
          extendBody: true,
          backgroundColor: AppConstants.colorBackground,
          body: Stack(
            children: [
              IndexedStack(
                index: _selectedIndex,
                children: List.generate(4, _buildTab),
              ),
              FloatingNavBar(
                currentIndex: _selectedIndex,
                onTap: _selectTab,
              ),
            ],
          ),
        );
      },
    );
  }
}

class DashboardHomeTab extends StatefulWidget {
  const DashboardHomeTab({super.key});

  @override
  State<DashboardHomeTab> createState() => _DashboardHomeTabState();
}

class _DashboardHomeTabState extends State<DashboardHomeTab>
    with WidgetsBindingObserver {
  int _shownApprovalNoticeVersion = 0;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      Provider.of<DashboardProvider>(context, listen: false).fetchDashboardData();
    });
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      Provider.of<DashboardProvider>(context, listen: false).fetchDashboardData();
    }
  }

  // ── Stat Card ─────────────────────────────────────────────────────────────
  Widget _buildStatCard(IconData icon, String value, String label, Color color, Color bgColor) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 8),
        decoration: BoxDecoration(
          color: AppConstants.colorSurface,
          borderRadius: BorderRadius.circular(16),
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
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: bgColor,
                borderRadius: BorderRadius.circular(10),
              ),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(height: 10),
            Text(
              value,
              style: TextStyle(
                color: AppConstants.colorTextPrimary,
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: const TextStyle(
                color: AppConstants.colorTextSecondary,
                fontSize: 11,
              ),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      ),
    );
  }

  // ── Schedule Item ──────────────────────────────────────────────────────────
  Widget _buildScheduleItem(IconData icon, String title, String time, Color iconColor, Color bgColor) {
    return Expanded(
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(10),
            decoration: BoxDecoration(
              color: bgColor,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: iconColor, size: 22),
          ),
          const SizedBox(height: 8),
          Text(title, style: const TextStyle(color: AppConstants.colorTextSecondary, fontSize: 12)),
          const SizedBox(height: 4),
          Text(
            time,
            style: const TextStyle(
              color: AppConstants.colorTextPrimary,
              fontSize: 16,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final user = Provider.of<AuthProvider>(context).currentUser;
    final userName = user?.name ?? '-';

    return Consumer<DashboardProvider>(
      builder: (context, dashboard, child) {
        if (dashboard.approvalNoticeVersion > _shownApprovalNoticeVersion) {
          _shownApprovalNoticeVersion = dashboard.approvalNoticeVersion;
          final approved = dashboard.approvalAccepted == true;
          final message = dashboard.approvalMessage ??
              'Status pengajuan presensi telah diperbarui.';

          WidgetsBinding.instance.addPostFrameCallback((_) {
            if (!mounted) return;
            AppNotice.show(
              context,
              message,
              type: approved ? AppNoticeType.success : AppNoticeType.error,
            );
          });
        }

        return Scaffold(
          backgroundColor: AppConstants.colorBackground,
          body: RefreshIndicator(
            color: AppConstants.colorPrimaryBase,
            backgroundColor: AppConstants.colorSurface,
            onRefresh: () async {
              await dashboard.fetchDashboardData();
            },
            child: CustomScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              slivers: [
                // ── App Bar ────────────────────────────────────────────────
                SliverAppBar(
                  backgroundColor: AppConstants.colorSurface,
                  surfaceTintColor: Colors.transparent,
                  elevation: 0,
                  pinned: true,
                  expandedHeight: 0,
                  bottom: PreferredSize(
                    preferredSize: const Size.fromHeight(1),
                    child: Container(height: 1, color: AppConstants.colorBorder),
                  ),
                  title: Row(
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Halo, ${userName.split(' ').first} 👋',
                            style: const TextStyle(
                              color: AppConstants.colorTextPrimary,
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Text(
                            DateFormat('EEEE, dd MMM yyyy', 'id_ID').format(DateTime.now()),
                            style: const TextStyle(
                              color: AppConstants.colorTextSecondary,
                              fontSize: 12,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                  actions: [
                    Container(
                      margin: const EdgeInsets.only(right: 16),
                      decoration: BoxDecoration(
                        color: AppConstants.colorPrimaryLight,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: AppConstants.colorBorder),
                      ),
                      child: IconButton(
                        icon: const Icon(
                          Icons.notifications_outlined,
                          color: AppConstants.colorPrimaryBase,
                          size: 22,
                        ),
                        onPressed: () {},
                      ),
                    ),
                  ],
                ),

                // ── Content ────────────────────────────────────────────────
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(20, 20, 20, 120),
                  sliver: SliverList(
                    delegate: SliverChildListDelegate([

                      // HERO ATTENDANCE CARD
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(24),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(24),
                          gradient: const LinearGradient(
                            colors: [Color(0xFF1E40AF), Color(0xFF2563EB), Color(0xFF3B82F6)],
                            begin: Alignment.topLeft,
                            end: Alignment.bottomRight,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF2563EB).withValues(alpha: 0.3),
                              blurRadius: 20,
                              spreadRadius: -4,
                              offset: const Offset(0, 8),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Status label
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: Colors.white.withValues(alpha: 0.2),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: const Row(
                                    mainAxisSize: MainAxisSize.min,
                                    children: [
                                      Icon(Icons.circle, color: Color(0xFF4ADE80), size: 8),
                                      SizedBox(width: 6),
                                      Text(
                                        'Status Hari Ini',
                                        style: TextStyle(color: Colors.white70, fontSize: 12, fontWeight: FontWeight.w600),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 16),
                            Text(
                              dashboard.statusHariIni,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 28,
                                fontWeight: FontWeight.w900,
                                height: 1.1,
                              ),
                            ),
                            const SizedBox(height: 6),
                            Text(
                              DateFormat('HH:mm', 'id_ID').format(DateTime.now()),
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.7),
                                fontSize: 14,
                              ),
                            ),
                            const SizedBox(height: 24),
                            // Action button
                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton.icon(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.white,
                                  foregroundColor: AppConstants.colorPrimaryBase,
                                  elevation: 0,
                                  padding: const EdgeInsets.symmetric(vertical: 14),
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                                ),
                                icon: const Icon(Icons.fingerprint_rounded, size: 20),
                                label: const Text(
                                  'Presensi Sekarang',
                                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                                ),
                                onPressed: () {},
                              ),
                            ),
                          ],
                        ),
                      ).animate().fadeIn(duration: 500.ms).slideY(begin: 0.1),

                      const SizedBox(height: 24),

                      // STAT CARDS ROW
                      Row(
                        children: [
                          _buildStatCard(
                            Icons.check_circle_outline_rounded,
                            dashboard.hadirCount.toString(),
                            'Hadir',
                            AppConstants.colorPresent,
                            AppConstants.colorPresentBg,
                          ),
                          const SizedBox(width: 10),
                          _buildStatCard(
                            Icons.medical_services_outlined,
                            dashboard.alfaCount.toString(),
                            'Alfa',
                            AppConstants.colorAbsent,
                            AppConstants.colorAbsentBg,
                          ),
                          const SizedBox(width: 10),
                          _buildStatCard(
                            Icons.assignment_late_outlined,
                            dashboard.izinCount.toString(),
                            'Izin/Sakit',
                            AppConstants.colorPermission,
                            AppConstants.colorPermissionBg,
                          ),
                        ],
                      ).animate().fadeIn(duration: 600.ms, delay: 100.ms).slideY(begin: 0.1),

                      const SizedBox(height: 24),

                      // JADWAL HARI INI CARD
                      Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: AppConstants.colorSurface,
                          borderRadius: BorderRadius.circular(16),
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
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Jadwal Hari Ini',
                              style: TextStyle(
                                color: AppConstants.colorTextPrimary,
                                fontSize: 15,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 16),
                            Row(
                              children: [
                                _buildScheduleItem(
                                  Icons.login_rounded,
                                  'Masuk',
                                  dashboard.scheduleMasuk,
                                  AppConstants.colorPresent,
                                  AppConstants.colorPresentBg,
                                ),
                                Container(width: 1, height: 52, color: AppConstants.colorBorder),
                                _buildScheduleItem(
                                  Icons.logout_rounded,
                                  'Pulang',
                                  dashboard.schedulePulang,
                                  AppConstants.colorAbsent,
                                  AppConstants.colorAbsentBg,
                                ),
                              ],
                            ),
                          ],
                        ),
                      ).animate().fadeIn(duration: 600.ms, delay: 200.ms),

                      const SizedBox(height: 28),

                      // SECTION HEADER — Pengumuman
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'Pengumuman Terbaru',
                            style: TextStyle(
                              color: AppConstants.colorTextPrimary,
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          Text(
                            'Lihat Semua',
                            style: TextStyle(
                              color: AppConstants.colorPrimaryBase,
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ).animate().fadeIn(duration: 500.ms, delay: 200.ms),
                      const SizedBox(height: 12),

                      // ANNOUNCEMENTS
                      if (dashboard.isLoading)
                        const Center(
                          child: Padding(
                            padding: EdgeInsets.all(24.0),
                            child: CircularProgressIndicator(color: AppConstants.colorPrimaryBase),
                          ),
                        )
                      else if (dashboard.announcements.isEmpty)
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(
                            color: AppConstants.colorSurface,
                            borderRadius: BorderRadius.circular(16),
                            border: Border.all(color: AppConstants.colorBorder),
                          ),
                          child: const Center(
                            child: Text(
                              'Belum ada pengumuman',
                              style: TextStyle(color: AppConstants.colorTextSecondary),
                            ),
                          ),
                        )
                      else
                        AnimatedSwitcher(
                          duration: const Duration(milliseconds: 250),
                          child: Column(
                            key: ValueKey(
                              dashboard.announcements
                                  .map((item) => '${item.id}:${item.title}:${item.content}')
                                  .join(','),
                            ),
                            children: dashboard.announcements.map((item) {
                              return Padding(
                                key: ValueKey(item.id),
                                padding: const EdgeInsets.only(bottom: 10.0),
                                child: Container(
                                  padding: const EdgeInsets.all(16),
                                  decoration: BoxDecoration(
                                    color: AppConstants.colorSurface,
                                    borderRadius: BorderRadius.circular(16),
                                    border: Border.all(color: AppConstants.colorBorder),
                                    boxShadow: [
                                      BoxShadow(
                                        color: const Color(0xFF0F172A).withValues(alpha: 0.04),
                                        blurRadius: 6,
                                        offset: const Offset(0, 2),
                                      ),
                                    ],
                                  ),
                                  child: Row(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Container(
                                        width: 4,
                                        height: 44,
                                        decoration: BoxDecoration(
                                          color: AppConstants.colorPermission,
                                          borderRadius: BorderRadius.circular(4),
                                        ),
                                      ),
                                      const SizedBox(width: 14),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(
                                              item.title,
                                              style: const TextStyle(
                                                color: AppConstants.colorTextPrimary,
                                                fontWeight: FontWeight.bold,
                                                fontSize: 14,
                                              ),
                                            ),
                                            if (item.content != null && item.content!.isNotEmpty) ...[
                                              const SizedBox(height: 4),
                                              Text(
                                                item.content!,
                                                style: const TextStyle(
                                                  color: AppConstants.colorTextSecondary,
                                                  fontSize: 13,
                                                ),
                                                maxLines: 2,
                                                overflow: TextOverflow.ellipsis,
                                              ),
                                            ],
                                          ],
                                        ),
                                      ),
                                      const Icon(Icons.chevron_right_rounded, color: AppConstants.colorTextMuted, size: 20),
                                    ],
                                  ),
                                ),
                              );
                            }).toList(),
                          ),
                        ).animate().fadeIn(duration: 600.ms, delay: 300.ms),
                    ]),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}
