import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({Key? key}) : super(key: key);

  @override
  _SplashScreenState createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  late AnimationController _animationController;
  late Animation<double> _animation;

  @override
  void initState() {
    super.initState();

    _animationController = AnimationController(
      duration: const Duration(seconds: 2),
      vsync: this,
    );

    _animation = Tween<double>(begin: 0.5, end: 1.0).animate(
      CurvedAnimation(parent: _animationController, curve: Curves.easeInOut),
    );

    _animationController.forward();
    _checkSession();
  }

  Future<void> _checkSession() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    
    // Animate for at least 2 seconds before checking
    await Future.delayed(const Duration(seconds: 2));
    
    // Check if Secure Storage has token
    final isActive = await authProvider.initializeAuth();

    if (!mounted) return;

    if (isActive) {
      Navigator.of(context).pushReplacementNamed('/dashboard');
    } else {
      Navigator.of(context).pushReplacementNamed('/login');
    }
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0D0E1C), // Deep Navy
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            FadeTransition(
              opacity: _animation,
              child: ScaleTransition(
                scale: _animation,
                child: Container(
                  width: 140,
                  height: 140,
                  decoration: BoxDecoration(
                    color: const Color(0xFF00D9B5).withOpacity(0.1),
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFF00D9B5).withOpacity(0.3),
                        spreadRadius: 10,
                        blurRadius: 30,
                      ),
                    ],
                  ),
                  child: Center(
                    child: Icon(
                      Icons.fingerprint_rounded,
                      size: 80,
                      color: const Color(0xFF00D9B5),
                    ),
                  ),
                ),
              ),
            ),
            const SizedBox(height: 40),
            FadeTransition(
              opacity: _animation,
              child: const Text(
                'PresenSmart',
                style: TextStyle(
                  fontSize: 32,
                  fontWeight: FontWeight.bold,
                  color: Colors.white,
                  letterSpacing: 2.0,
                ),
              ),
            ),
            const SizedBox(height: 8),
            FadeTransition(
              opacity: _animation,
              child: Text(
                'Sistem Presensi Cerdas',
                style: TextStyle(
                  fontSize: 14,
                  color: const Color(0xFFA0A0AB),
                  letterSpacing: 1.2,
                ),
              ),
            ),
          ],
        ),
      ),
      bottomNavigationBar: Container(
        height: 6,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF6C63FF), Color(0xFF00D9B5)],
          ),
        ),
      ),
    );
  }
}
