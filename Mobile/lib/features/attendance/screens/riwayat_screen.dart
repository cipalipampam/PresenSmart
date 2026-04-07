import 'package:flutter/material.dart';

class RiwayatScreen extends StatelessWidget {
  const RiwayatScreen({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    // Dummy Data History (Will be linked to API in future scope)
    final List<Map<String, String>> riwayatPresensi = [
      {
        'tanggal': '19 Jul 2024',
        'masuk': '07:01',
        'pulang': '15:02',
        'status': 'Hadir',
      },
      {
        'tanggal': '18 Jul 2024',
        'masuk': '07:03',
        'pulang': '15:01',
        'status': 'Hadir',
      },
      {
        'tanggal': '17 Jul 2024',
        'masuk': '07:10',
        'pulang': '-',
        'status': 'Izin'
      },
    ];

    return Scaffold(
      appBar: AppBar(
        title: const Text('Riwayat Presensi', style: TextStyle(fontWeight: FontWeight.bold)),
        centerTitle: true,
        backgroundColor: const Color(0xFF43C59E),
        elevation: 0,
      ),
      body: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: riwayatPresensi.length,
        itemBuilder: (context, index) {
          final data = riwayatPresensi[index];
          final isHadir = data['status'] == 'Hadir';

          return Card(
            elevation: 4,
            margin: const EdgeInsets.only(bottom: 16),
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Row(
                children: [
                  Container(
                    width: 60,
                    height: 60,
                    decoration: BoxDecoration(
                      color: isHadir ? Colors.green.shade50 : Colors.orange.shade50,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Center(
                      child: Icon(
                        isHadir ? Icons.check_circle : Icons.warning_amber_rounded,
                        color: isHadir ? Colors.green : Colors.orange,
                        size: 32,
                      ),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          data['tanggal']!,
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          children: [
                            const Icon(Icons.login, size: 16, color: Colors.grey),
                            const SizedBox(width: 4),
                            Text(data['masuk']!, style: const TextStyle(color: Colors.grey)),
                            const SizedBox(width: 16),
                            const Icon(Icons.logout, size: 16, color: Colors.grey),
                            const SizedBox(width: 4),
                            Text(data['pulang']!, style: const TextStyle(color: Colors.grey)),
                          ],
                        ),
                      ],
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: isHadir ? Colors.green : Colors.orange,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      data['status']!,
                      style: const TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
