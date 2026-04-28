import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'core/network/api_client.dart';
import 'core/constants/app_constants.dart';
import 'features/auth/providers/auth_provider.dart';
import 'features/attendance/providers/attendance_provider.dart';
import 'features/dashboard/providers/dashboard_provider.dart';
import 'features/auth/screens/splash_screen.dart';
import 'features/auth/screens/login_screen.dart';
import 'features/dashboard/screens/dashboard_screen.dart';

/// Global navigator key — allows navigation from non-widget classes
/// (e.g. ApiClient 401 interceptor) without needing BuildContext.
final GlobalKey<NavigatorState> navigatorKey = GlobalKey<NavigatorState>();

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await initializeDateFormatting('id_ID', null);

  // Wire 401 auto-logout: when ApiClient receives 401, navigate to /login
  ApiClient.onUnauthorized = () {
    navigatorKey.currentState?.pushNamedAndRemoveUntil(
      '/login',
      (route) => false, // clear entire back stack
    );
  };

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => AttendanceProvider()),
        ChangeNotifierProvider(create: (_) => DashboardProvider()),
      ],
      child: MaterialApp(
        title: 'PresenSmart',
        debugShowCheckedModeBanner: false,
        navigatorKey: navigatorKey, // ← enables ApiClient to navigate
        theme: ThemeData(
          brightness: Brightness.dark,
          primaryColor: AppConstants.colorPrimaryBase,
          scaffoldBackgroundColor: AppConstants.colorBackgroundDark,
          colorScheme: const ColorScheme.dark(
            primary: AppConstants.colorPrimaryBase,
            secondary: AppConstants.colorSecondaryBase,
            surface: AppConstants.colorCardDark,
          ),
          appBarTheme: const AppBarTheme(
            backgroundColor: AppConstants.colorBackgroundDark,
            foregroundColor: AppConstants.colorTextPrimary,
            elevation: 0,
            centerTitle: true,
          ),
          elevatedButtonTheme: ElevatedButtonThemeData(
            style: ElevatedButton.styleFrom(
              backgroundColor: AppConstants.colorPrimaryBase,
              foregroundColor: AppConstants.colorTextPrimary,
              shape: const RoundedRectangleBorder(
                borderRadius: BorderRadius.all(Radius.circular(16)),
              ),
            ),
          ),
        ),
        initialRoute: '/',
        routes: {
          '/': (context) => const SplashScreen(),
          '/login': (context) => const LoginScreen(),
          '/dashboard': (context) => const DashboardScreen(),
        },
      ),
    );
  }
}
