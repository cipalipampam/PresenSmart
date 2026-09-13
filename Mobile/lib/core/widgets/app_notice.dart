import 'dart:async';

import 'package:flutter/material.dart';

enum AppNoticeType { success, error, info }

/// A compact in-app notification for short feedback. It deliberately avoids
/// the default bottom SnackBar so it does not collide with the floating nav.
class AppNotice {
  static OverlayEntry? _currentEntry;
  static Timer? _dismissTimer;

  static void show(
    BuildContext context,
    String message, {
    AppNoticeType type = AppNoticeType.info,
  }) {
    _dismissTimer?.cancel();
    _currentEntry?.remove();

    final overlay = Overlay.of(context, rootOverlay: true);
    late final OverlayEntry entry;
    entry = OverlayEntry(
      builder: (context) => _NoticeOverlay(
        message: message,
        type: type,
        onDismiss: () => _remove(entry),
      ),
    );

    _currentEntry = entry;
    overlay.insert(entry);
    _dismissTimer = Timer(const Duration(seconds: 4), () => _remove(entry));
  }

  static void _remove(OverlayEntry entry) {
    _dismissTimer?.cancel();
    if (_currentEntry == entry) _currentEntry = null;
    entry.remove();
  }
}

class _NoticeOverlay extends StatelessWidget {
  const _NoticeOverlay({
    required this.message,
    required this.type,
    required this.onDismiss,
  });

  final String message;
  final AppNoticeType type;
  final VoidCallback onDismiss;

  @override
  Widget build(BuildContext context) {
    final color = switch (type) {
      AppNoticeType.success => const Color(0xFF10B981),
      AppNoticeType.error => const Color(0xFFEF4444),
      AppNoticeType.info => const Color(0xFF06B6D4),
    };
    final icon = switch (type) {
      AppNoticeType.success => Icons.check_circle_rounded,
      AppNoticeType.error => Icons.error_rounded,
      AppNoticeType.info => Icons.info_rounded,
    };

    return SafeArea(
      child: Align(
        alignment: Alignment.topCenter,
        child: TweenAnimationBuilder<double>(
          duration: const Duration(milliseconds: 260),
          curve: Curves.easeOutCubic,
          tween: Tween(begin: 0, end: 1),
          builder: (context, value, child) => Opacity(
            opacity: value,
            child: Transform.translate(
              offset: Offset(0, -18 * (1 - value)),
              child: child,
            ),
          ),
          child: Material(
            color: Colors.transparent,
            child: Dismissible(
              key: ValueKey('$message-$type'),
              direction: DismissDirection.up,
              onDismissed: (_) => onDismiss(),
              child: Container(
                width: double.infinity,
                margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 13),
                decoration: BoxDecoration(
                  color: const Color(0xFF182337),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: color.withValues(alpha: 0.55)),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.25),
                      blurRadius: 20,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Icon(icon, color: color, size: 22),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        message,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                    IconButton(
                      onPressed: onDismiss,
                      icon: const Icon(Icons.close_rounded, color: Colors.white54, size: 18),
                      visualDensity: VisualDensity.compact,
                    ),
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
