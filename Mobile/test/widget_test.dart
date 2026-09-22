// This is a basic Flutter widget test.
//
// To perform an interaction with a widget in your test, use the WidgetTester
// utility in the flutter_test package. For example, you can send tap and scroll
// gestures. You can also use WidgetTester to find child widgets in the widget
// tree, read text, and verify that the values of widget properties are correct.

import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:provider/provider.dart';
import 'package:kelasentra/features/auth/providers/auth_provider.dart';
import 'package:kelasentra/features/auth/screens/splash_screen.dart';

void main() {
  testWidgets('renders the Kelasentra splash screen', (WidgetTester tester) async {
    await tester.pumpWidget(
      ChangeNotifierProvider(
        create: (_) => AuthProvider(),
        child: const MaterialApp(
          home: SplashScreen(),
        ),
      ),
    );

    expect(find.text('Kelasentra'), findsOneWidget);
    expect(find.text('Sistem Presensi Cerdas'), findsOneWidget);
    expect(find.byIcon(Icons.fingerprint_rounded), findsOneWidget);

    await tester.pump(const Duration(seconds: 2));
    await tester.pump();
    await tester.pumpWidget(const SizedBox.shrink());
  });
}
