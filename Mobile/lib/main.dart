import 'package:flutter/material.dart';
import 'splash_page.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'E-Presensi SMK',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        primaryColor: const Color(0xFF7ED6A8),
        scaffoldBackgroundColor: Colors.white,
        colorScheme: ColorScheme.fromSwatch(
          primarySwatch: MaterialColor(
            0xFF7ED6A8,
            <int, Color>{
              50: Color(0xFFE3F8F0),
              100: Color(0xFFB9EDD8),
              200: Color(0xFF8EE2C0),
              300: Color(0xFF63D6A8),
              400: Color(0xFF43C59E),
              500: Color(0xFF7ED6A8),
              600: Color(0xFF43C59E),
              700: Color(0xFF2E9C7A),
              800: Color(0xFF21725A),
              900: Color(0xFF14483A),
            },
          ),
          accentColor: Color(0xFF43C59E),
        ).copyWith(
          secondary: Color(0xFF43C59E),
        ),
        appBarTheme: const AppBarTheme(
          backgroundColor: Color(0xFF7ED6A8),
          foregroundColor: Colors.white,
          elevation: 0,
        ),
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: Color(0xFF43C59E),
            foregroundColor: Colors.white,
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.all(Radius.circular(16)),
            ),
          ),
        ),
        floatingActionButtonTheme: const FloatingActionButtonThemeData(
          backgroundColor: Color(0xFF43C59E),
        ),
        visualDensity: VisualDensity.adaptivePlatformDensity,
            ),
      home: const SplashPage(), // Ganti menjadi SplashPage
    );
  }
}
