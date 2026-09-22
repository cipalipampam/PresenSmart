import 'package:flutter/material.dart';
import 'package:flutter_animate/flutter_animate.dart';
import 'package:provider/provider.dart';
import '../../../core/constants/app_constants.dart';
import '../../../core/widgets/app_notice.dart';
import '../providers/auth_provider.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

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
      AppNotice.show(
        context,
        authProvider.errorMessage ?? 'Login gagal. Periksa email dan password.',
        type: AppNoticeType.error,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppConstants.colorBackground,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 32.0),
            child: ConstrainedBox(
              constraints: const BoxConstraints(maxWidth: 420),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 28, vertical: 36),
                decoration: BoxDecoration(
                  color: AppConstants.colorSurface,
                  borderRadius: BorderRadius.circular(24),
                  border: Border.all(color: AppConstants.colorBorder),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF0F172A).withValues(alpha: 0.05),
                      blurRadius: 16,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Form(
                  key: _formKey,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Brand Logo & Header
                      Hero(
                        tag: 'app-logo',
                        child: Container(
                          width: 64,
                          height: 64,
                          decoration: BoxDecoration(
                            color: AppConstants.colorPrimaryLight,
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(color: AppConstants.colorPrimaryBase.withValues(alpha: 0.2)),
                          ),
                          child: const Icon(
                            Icons.fingerprint_rounded,
                            size: 36,
                            color: AppConstants.colorPrimaryBase,
                          ),
                        ),
                      ).animate().fadeIn(duration: 400.ms).scale(begin: const Offset(0.8, 0.8)),
                      const SizedBox(height: 20),

                      const Text(
                        'Kelasentra',
                        style: TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: AppConstants.colorTextPrimary,
                          letterSpacing: -0.5,
                        ),
                      ).animate().fadeIn(duration: 450.ms).slideY(begin: 0.2, end: 0),
                      const SizedBox(height: 6),
                      const Text(
                        'Masuk untuk mencatat kehadiran harian Anda',
                        style: TextStyle(
                          color: AppConstants.colorTextSecondary,
                          fontSize: 13,
                        ),
                        textAlign: TextAlign.center,
                      ).animate().fadeIn(duration: 450.ms).slideY(begin: 0.2, end: 0),
                      const SizedBox(height: 32),

                      // Email Field
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Alamat Email',
                            style: TextStyle(
                              color: AppConstants.colorTextPrimary,
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(height: 6),
                          TextFormField(
                            controller: _emailController,
                            style: const TextStyle(color: AppConstants.colorTextPrimary, fontSize: 14),
                            decoration: InputDecoration(
                              hintText: 'nama@sekolah.sch.id',
                              hintStyle: const TextStyle(color: AppConstants.colorTextMuted, fontSize: 13),
                              prefixIcon: const Icon(Icons.mail_outline_rounded, color: AppConstants.colorTextSecondary, size: 20),
                              filled: true,
                              fillColor: AppConstants.colorBackground,
                              contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: const BorderSide(color: AppConstants.colorBorder),
                              ),
                              enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: const BorderSide(color: AppConstants.colorBorder),
                              ),
                              focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: const BorderSide(color: AppConstants.colorPrimaryBase, width: 2),
                              ),
                            ),
                            validator: (value) => (value == null || value.isEmpty) ? 'Email wajib diisi' : null,
                            keyboardType: TextInputType.emailAddress,
                          ),
                        ],
                      ).animate().fadeIn(duration: 500.ms).slideX(begin: -0.05, end: 0),
                      const SizedBox(height: 18),

                      // Password Field
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Kata Sandi',
                            style: TextStyle(
                              color: AppConstants.colorTextPrimary,
                              fontSize: 13,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(height: 6),
                          TextFormField(
                            controller: _passwordController,
                            style: const TextStyle(color: AppConstants.colorTextPrimary, fontSize: 14),
                            decoration: InputDecoration(
                              hintText: '••••••••',
                              hintStyle: const TextStyle(color: AppConstants.colorTextMuted, fontSize: 13),
                              prefixIcon: const Icon(Icons.lock_outline_rounded, color: AppConstants.colorTextSecondary, size: 20),
                              suffixIcon: IconButton(
                                icon: Icon(
                                  _obscurePassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                                  color: AppConstants.colorTextSecondary,
                                  size: 20,
                                ),
                                onPressed: () {
                                  setState(() {
                                    _obscurePassword = !_obscurePassword;
                                  });
                                },
                              ),
                              filled: true,
                              fillColor: AppConstants.colorBackground,
                              contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: const BorderSide(color: AppConstants.colorBorder),
                              ),
                              enabledBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: const BorderSide(color: AppConstants.colorBorder),
                              ),
                              focusedBorder: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(14),
                                borderSide: const BorderSide(color: AppConstants.colorPrimaryBase, width: 2),
                              ),
                            ),
                            obscureText: _obscurePassword,
                            validator: (value) => (value == null || value.isEmpty) ? 'Kata sandi wajib diisi' : null,
                          ),
                        ],
                      ).animate().fadeIn(duration: 500.ms).slideX(begin: 0.05, end: 0),
                      const SizedBox(height: 28),

                      // Login Button
                      Consumer<AuthProvider>(
                        builder: (context, authProvider, child) {
                          return SizedBox(
                            width: double.infinity,
                            height: 50,
                            child: ElevatedButton(
                              style: ElevatedButton.styleFrom(
                                backgroundColor: AppConstants.colorPrimaryBase,
                                foregroundColor: Colors.white,
                                elevation: 0,
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                              ),
                              onPressed: authProvider.isLoading
                                  ? null
                                  : () {
                                      if (_formKey.currentState!.validate()) {
                                        _login();
                                      }
                                    },
                              child: authProvider.isLoading
                                  ? const SizedBox(
                                      height: 20,
                                      width: 20,
                                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                    )
                                  : const Text(
                                      'Masuk ke Akun',
                                      style: TextStyle(
                                        fontSize: 15,
                                        fontWeight: FontWeight.bold,
                                      ),
                                    ),
                            ),
                          );
                        },
                      ).animate().fadeIn(duration: 500.ms).scale(begin: const Offset(0.95, 0.95)),
                      const SizedBox(height: 24),

                      // Footer Demo / Info
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                        decoration: BoxDecoration(
                          color: AppConstants.colorPrimaryLight,
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: AppConstants.colorPrimaryBase.withValues(alpha: 0.2)),
                        ),
                        child: Row(
                          children: const [
                            Icon(Icons.info_outline_rounded, color: AppConstants.colorPrimaryBase, size: 16),
                            SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                'Gunakan kredensial akun siswa atau pegawai terdaftar.',
                                style: TextStyle(color: AppConstants.colorPrimaryBase, fontSize: 11, fontWeight: FontWeight.w500),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ),
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
