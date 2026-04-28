import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/widgets/glass_container.dart';
import '../providers/auth_provider.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({Key? key}) : super(key: key);

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  bool _obscurePassword = true;

  Future<void> _login() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);

    final success = await authProvider.login(
      _emailController.text.trim(),
      _passwordController.text,
    );

    if (success) {
      if (!mounted) return;
      Navigator.pushReplacementNamed(context, '/dashboard');
    } else {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(authProvider.errorMessage ?? 'Login gagal'),
          backgroundColor: Colors.red.shade700,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppConstants.colorBackgroundDark,
      extendBodyBehindAppBar: true,
      body: Stack(
        children: [
          // Background abstract shapes
          Positioned(
            top: -100,
            right: -100,
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
            bottom: -50,
            left: -50,
            child: Container(
              width: 250,
              height: 250,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: AppConstants.colorPrimaryBase.withOpacity(0.1),
                boxShadow: [
                  BoxShadow(color: AppConstants.colorPrimaryBase.withOpacity(0.2), blurRadius: 100),
                ],
              ),
            ),
          ),
          Center(
            child: SingleChildScrollView(
              child: Padding(
                padding: const EdgeInsets.all(24.0),
                child: GlassContainer(
                  padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 40),
                  borderRadius: BorderRadius.circular(32),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Padding(
                          padding: const EdgeInsets.only(bottom: 24.0),
                          child: Hero(
                            tag: 'app-logo',
                            child: Container(
                              width: 80,
                              height: 80,
                              decoration: BoxDecoration(
                                shape: BoxShape.circle,
                                color: AppConstants.colorPrimaryBase.withOpacity(0.1),
                              ),
                              child: const Icon(
                                Icons.fingerprint_rounded,
                                size: 40,
                                color: AppConstants.colorPrimaryBase,
                              ),
                            ),
                          ),
                        ),
                        Text(
                          'Selamat Datang',
                          style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                              ),
                        ).animate().fadeIn(duration: 500.ms).slideY(begin: 0.5, end: 0),
                        const SizedBox(height: 8),
                        Text(
                          'Silakan login untuk absensi',
                          style: const TextStyle(color: AppConstants.colorTextSecondary),
                        ).animate().fadeIn(duration: 500.ms).slideY(begin: 0.5, end: 0),
                        const SizedBox(height: 32),
                        TextFormField(
                          controller: _emailController,
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Email',
                            labelStyle: const TextStyle(color: AppConstants.colorTextSecondary),
                            prefixIcon: const Icon(Icons.email_outlined, color: AppConstants.colorTextSecondary),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.03),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(color: Colors.white.withOpacity(0.1)),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(color: Colors.white.withOpacity(0.1)),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: const BorderSide(color: AppConstants.colorPrimaryBase, width: 2),
                            ),
                          ),
                          validator: (value) => (value == null || value.isEmpty) ? 'Email wajib diisi' : null,
                          keyboardType: TextInputType.emailAddress,
                        ).animate().fadeIn(duration: 500.ms).slideX(begin: -0.1, end: 0),
                        const SizedBox(height: 16),
                        TextFormField(
                          controller: _passwordController,
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Password',
                            labelStyle: const TextStyle(color: AppConstants.colorTextSecondary),
                            prefixIcon: const Icon(Icons.lock_outline, color: AppConstants.colorTextSecondary),
                            suffixIcon: IconButton(
                              icon: Icon(
                                _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                                color: AppConstants.colorTextSecondary,
                              ),
                              onPressed: () {
                                setState(() {
                                  _obscurePassword = !_obscurePassword;
                                });
                              },
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.03),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(color: Colors.white.withOpacity(0.1)),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: BorderSide(color: Colors.white.withOpacity(0.1)),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(16),
                              borderSide: const BorderSide(color: AppConstants.colorPrimaryBase, width: 2),
                            ),
                          ),
                          obscureText: _obscurePassword,
                          validator: (value) => (value == null || value.isEmpty) ? 'Password wajib diisi' : null,
                        ).animate().fadeIn(duration: 500.ms).slideX(begin: 0.1, end: 0),
                        const SizedBox(height: 32),
                        Consumer<AuthProvider>(
                          builder: (context, authProvider, child) {
                            return SizedBox(
                              width: double.infinity,
                              height: 56,
                              child: ElevatedButton(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: Colors.transparent,
                                  shadowColor: Colors.transparent,
                                  padding: EdgeInsets.zero,
                                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                                ),
                                onPressed: authProvider.isLoading
                                    ? null
                                    : () {
                                        if (_formKey.currentState!.validate()) {
                                          _login();
                                        }
                                      },
                                child: Ink(
                                  decoration: BoxDecoration(
                                    gradient: const LinearGradient(
                                      colors: [AppConstants.colorSecondaryBase, AppConstants.colorPrimaryBase],
                                    ),
                                    borderRadius: BorderRadius.circular(16),
                                  ),
                                  child: Container(
                                    alignment: Alignment.center,
                                    child: authProvider.isLoading
                                        ? const SizedBox(
                                            height: 24,
                                            width: 24,
                                            child: CircularProgressIndicator(strokeWidth: 3, color: Colors.white),
                                          )
                                        : const Text(
                                            'MASUK',
                                            style: TextStyle(
                                              fontSize: 16,
                                              fontWeight: FontWeight.bold,
                                              letterSpacing: 1.5,
                                              color: Colors.white,
                                            ),
                                          ),
                                  ),
                                ),
                              ),
                            ).animate().fadeIn(duration: 500.ms).scale(begin: const Offset(0.9, 0.9));
                          },
                        ),
                      ],
                    ),
                  ),
                ).animate().fadeIn(duration: 600.ms).slideY(begin: 0.2),
              ),
            ),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }
}
