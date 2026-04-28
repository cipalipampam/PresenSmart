import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/widgets/glass_container.dart';
import '../providers/attendance_provider.dart';

class HistoryScreen extends StatefulWidget {
  const HistoryScreen({Key? key}) : super(key: key);

  @override
  State<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends State<HistoryScreen>
    with WidgetsBindingObserver {
  // Filter state
  int _selectedMonth = DateTime.now().month;
  int _selectedYear = DateTime.now().year;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _fetchHistory();
    });
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  /// Fix #9: Refresh history when app resumes from background.
  /// This ensures hasCheckedInToday is never stale after the user switches away
  /// and comes back (e.g. after checking time, using WhatsApp, etc.)
  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      _fetchHistory();
    }
  }

  void _fetchHistory() {
    Provider.of<AttendanceProvider>(context, listen: false)
        .fetchHistory(month: _selectedMonth, year: _selectedYear);
  }

  /// Returns badge color and label based on full attendance status logic
  ({Color color, String label}) _resolveBadge(attendance) {
    if (attendance.status == 'present') {
      return (
        color: attendance.isLate ? Colors.orange : Colors.green,
        label: attendance.isLate ? 'TERLAMBAT' : 'HADIR',
      );
    }
    if (attendance.status == 'permission' || attendance.status == 'sick') {
      final label = attendance.status == 'sick' ? 'SAKIT' : 'IZIN';
      if (attendance.isApproved == null) {
        return (color: Colors.amber.shade700, label: 'MENUNGGU — $label');
      }
      if (attendance.isApproved == true) {
        return (color: Colors.blue, label: 'DISETUJUI — $label');
      }
      return (color: Colors.red, label: 'DITOLAK');
    }
    return (color: Colors.red.shade700, label: 'ALFA');
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppConstants.colorBackgroundDark,
      appBar: AppBar(
        title: const Text('Riwayat Presensi',
            style: TextStyle(fontWeight: FontWeight.bold, color: Colors.white, letterSpacing: 1.2)),
        centerTitle: true,
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: Stack(
        children: [
          // Background Glow
          Positioned(
            bottom: -50,
            left: -50,
            child: Container(
              width: 300,
              height: 300,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppConstants.colorSecondaryBase.withOpacity(0.1),
                boxShadow: [BoxShadow(color: AppConstants.colorSecondaryBase.withOpacity(0.2), blurRadius: 100)],
              ),
            ),
          ),
          Column(
            children: [
              // ── Month/Year Filter bar ─────────────────────────────────────────
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 16),
                child: GlassContainer(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                  backgroundColor: AppConstants.colorCardDark,
                  borderRadius: BorderRadius.circular(24),
                  child: Row(
                    children: [
                      Expanded(
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<int>(
                            value: _selectedMonth,
                            dropdownColor: AppConstants.colorBackgroundDark,
                            icon: const Icon(Icons.arrow_drop_down, color: AppConstants.colorPrimaryBase),
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                            items: List.generate(12, (i) {
                              final month = i + 1;
                              return DropdownMenuItem(
                                value: month,
                                child: Text(
                                  DateFormat('MMMM', 'id_ID').format(DateTime(0, month)),
                                ),
                              );
                            }),
                            onChanged: (val) {
                              if (val != null) {
                                setState(() => _selectedMonth = val);
                                _fetchHistory();
                              }
                            },
                          ),
                        ),
                      ),
                      Container(width: 1, height: 30, color: Colors.white24, margin: const EdgeInsets.symmetric(horizontal: 12)),
                      Expanded(
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<int>(
                            value: _selectedYear,
                            dropdownColor: AppConstants.colorBackgroundDark,
                            icon: const Icon(Icons.arrow_drop_down, color: AppConstants.colorPrimaryBase),
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold),
                            items: List.generate(5, (i) {
                              final year = DateTime.now().year - i;
                              return DropdownMenuItem(
                                  value: year, child: Text('$year'));
                            }),
                            onChanged: (val) {
                              if (val != null) {
                                setState(() => _selectedYear = val);
                                _fetchHistory();
                              }
                            },
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              // ── List ─────────────────────────────────────────────────────────
              Expanded(
                child: Consumer<AttendanceProvider>(
                  builder: (context, provider, child) {
                    if (provider.isLoading) {
                      return const Center(
                          child: CircularProgressIndicator(
                              color: AppConstants.colorPrimaryBase));
                    }
                    if (provider.errorMessage != null &&
                        provider.historyList.isEmpty) {
                      return Center(
                        child: Text(provider.errorMessage!,
                            style: const TextStyle(color: Colors.redAccent)),
                      );
                    }
                    if (provider.historyList.isEmpty) {
                      return const Center(
                        child: Text('Belum ada riwayat kehadiran bulan ini.',
                            style: TextStyle(color: AppConstants.colorTextSecondary)),
                      );
                    }

                    return ListView.builder(
                      padding: const EdgeInsets.fromLTRB(16, 0, 16, 120),
                      itemCount: provider.historyList.length,
                      itemBuilder: (context, index) {
                        final data = provider.historyList[index];
                        final badge = _resolveBadge(data);
                        final dateStr = DateFormat('dd MMM yy', 'id_ID')
                            .format(data.recordedAt);
                        final timeIn =
                            DateFormat('HH:mm').format(data.recordedAt);
                        final timeOut = data.checkOutTime != null
                            ? DateFormat('HH:mm').format(data.checkOutTime!)
                            : null;

                        return Padding(
                          padding: const EdgeInsets.only(bottom: 12),
                          child: GlassContainer(
                            padding: const EdgeInsets.all(16.0),
                            backgroundColor: AppConstants.colorCardDark,
                            child: Column(
                              children: [
                                // Top row: Date and Badge
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                  children: [
                                    Text(
                                      dateStr,
                                      style: const TextStyle(
                                          color: Colors.white,
                                          fontWeight: FontWeight.bold,
                                          fontSize: 22), // Bigger text like mockup
                                    ),
                                    Container(
                                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
                                      decoration: BoxDecoration(
                                        color: badge.color,
                                        borderRadius: BorderRadius.circular(16),
                                      ),
                                      child: Text(
                                        badge.label.toUpperCase(),
                                        style: const TextStyle(
                                            color: Colors.white,
                                            fontWeight: FontWeight.bold,
                                            fontSize: 11,
                                            letterSpacing: 0.5),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 20),
                                // Bottom section: Info and Proof
                                Row(
                                  crossAxisAlignment: CrossAxisAlignment.end,
                                  children: [
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Row(
                                            children: [
                                              const Icon(Icons.login_rounded, size: 20, color: Colors.tealAccent),
                                              const SizedBox(width: 8),
                                              const SizedBox(width: 70, child: Text('Masuk', style: TextStyle(color: Colors.white70, fontSize: 15))),
                                              Text(timeIn, style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.normal)),
                                            ]
                                          ),
                                          const SizedBox(height: 12),
                                          Row(
                                            children: [
                                              const Icon(Icons.logout_rounded, size: 20, color: Colors.redAccent),
                                              const SizedBox(width: 8),
                                              const SizedBox(width: 70, child: Text('Pulang', style: TextStyle(color: Colors.white70, fontSize: 15))),
                                              Text(timeOut ?? '--:--', style: const TextStyle(color: Colors.white, fontSize: 16, fontWeight: FontWeight.normal)),
                                            ]
                                          ),
                                        ],
                                      ),
                                    ),
                                    if (data.proofImage != null)
                                      GestureDetector(
                                        onTap: () => _showProofImage(context, data.proofImage!),
                                        child: Container(
                                          width: 60,
                                          height: 60,
                                          margin: const EdgeInsets.only(left: 16),
                                          decoration: BoxDecoration(
                                            color: Colors.white10,
                                            borderRadius: BorderRadius.circular(12),
                                            image: DecorationImage(
                                                image: NetworkImage('${AppConstants.storageBaseUrl}/${data.proofImage!}'),
                                                fit: BoxFit.cover,
                                            ),
                                          ),
                                        ),
                                      )
                                    else
                                      Container(
                                        width: 60,
                                        height: 60,
                                        margin: const EdgeInsets.only(left: 16),
                                        decoration: BoxDecoration(
                                          color: Colors.white10,
                                          borderRadius: BorderRadius.circular(12),
                                        ),
                                        child: const Icon(Icons.image_not_supported_outlined, color: Colors.white30, size: 24),
                                      ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    );
                  },
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  void _showProofImage(BuildContext context, String proofImage) {
    // Fix storage URL — now uses AppConstants.storageBaseUrl
    final url = '${AppConstants.storageBaseUrl}/$proofImage';
    showDialog(
      context: context,
      builder: (_) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.all(16),
        child: Stack(
          alignment: Alignment.center,
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(16),
              child: Image.network(
                url,
                fit: BoxFit.contain,
                errorBuilder: (context, error, stackTrace) => Container(
                  color: AppConstants.colorCardDark,
                  padding: const EdgeInsets.all(32),
                  child: const Text('Gagal memuat gambar',
                      style: TextStyle(color: AppConstants.colorTextSecondary),
                      textAlign: TextAlign.center),
                ),
              ),
            ),
            Positioned(
              top: 8,
              right: 8,
              child: CircleAvatar(
                backgroundColor: Colors.black54,
                child: IconButton(
                  icon: const Icon(Icons.close, color: Colors.white),
                  onPressed: () => Navigator.pop(context),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
