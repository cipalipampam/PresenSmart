import 'package:flutter/material.dart';
import '../constants/app_constants.dart';
import 'glass_container.dart';

class FloatingNavBar extends StatelessWidget {
  final int currentIndex;
  final Function(int) onTap;

  const FloatingNavBar({
    Key? key,
    required this.currentIndex,
    required this.onTap,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Positioned(
      bottom: 24,
      left: 24,
      right: 24,
      child: GlassContainer(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        borderRadius: BorderRadius.circular(36),
        backgroundColor: Colors.white.withOpacity(0.05),
        blur: 25.0,
        border: Border.all(color: Colors.white.withOpacity(0.1), width: 1),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceAround,
          children: [
            _buildNavItem(0, Icons.dashboard_rounded, 'Home'),
            _buildNavItem(1, Icons.fingerprint_rounded, 'Presensi'),
            _buildNavItem(2, Icons.history_rounded, 'Riwayat'),
            _buildNavItem(3, Icons.person_rounded, 'Profil'),
          ],
        ),
      ),
    );
  }

  Widget _buildNavItem(int index, IconData icon, String label) {
    final isSelected = currentIndex == index;

    return GestureDetector(
      onTap: () => onTap(index),
      behavior: HitTestBehavior.opaque,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeOutCubic,
        padding: EdgeInsets.symmetric(
          horizontal: isSelected ? 16 : 12,
          vertical: 8,
        ),
        decoration: BoxDecoration(
          color: isSelected
              ? AppConstants.colorPrimaryBase.withOpacity(0.15)
              : Colors.transparent,
          borderRadius: BorderRadius.circular(24),
          border: isSelected
              ? Border.all(
                  color: AppConstants.colorPrimaryBase.withOpacity(0.5),
                  width: 1,
                )
              : Border.all(color: Colors.transparent, width: 1),
          boxShadow: isSelected
              ? [
                  BoxShadow(
                    color: AppConstants.colorPrimaryBase.withOpacity(0.3),
                    blurRadius: 16,
                    spreadRadius: 1,
                  )
                ]
              : null,
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(
              icon,
              size: 24,
              color: isSelected
                  ? AppConstants.colorPrimaryBase
                  : AppConstants.colorTextSecondary,
            ),
            if (isSelected) ...[
              const SizedBox(width: 8),
              Text(
                label,
                style: const TextStyle(
                  color: AppConstants.colorPrimaryBase,
                  fontWeight: FontWeight.bold,
                  fontSize: 13,
                ),
              ),
            ]
          ],
        ),
      ),
    );
  }
}
