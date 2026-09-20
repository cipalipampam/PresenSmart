import 'dart:ui';
import 'package:flutter/material.dart';
import '../constants/app_constants.dart';

class GlassContainer extends StatelessWidget {
  final Widget child;
  final double? width;
  final double? height;
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final BorderRadiusGeometry? borderRadius;
  final Color backgroundColor;
  final double blur;
  final bool enableBlur;
  final Border? border;
  final List<BoxShadow>? boxShadow;

  const GlassContainer({
    super.key,
    required this.child,
    this.width,
    this.height,
    this.padding,
    this.margin,
    this.borderRadius,
    this.backgroundColor = Colors.white,
    this.blur = 15.0,
    this.enableBlur = false,
    this.border,
    this.boxShadow,
  });

  @override
  Widget build(BuildContext context) {
    final defaultRadius = borderRadius ?? BorderRadius.circular(16.0);

    return Container(
      width: width,
      height: height,
      margin: margin,
      decoration: BoxDecoration(
        color: backgroundColor == const Color(0x1AFFFFFF) ? Colors.white : backgroundColor,
        borderRadius: defaultRadius,
        border: border ??
            Border.all(
              color: AppConstants.colorBorder,
              width: 1.0,
            ),
        boxShadow: boxShadow ??
            [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.03),
                blurRadius: 10,
                offset: const Offset(0, 2),
              ),
            ],
      ),
      child: ClipRRect(
        borderRadius: defaultRadius,
        child: enableBlur
            ? BackdropFilter(
                filter: ImageFilter.blur(sigmaX: blur, sigmaY: blur),
                child: _content(defaultRadius),
              )
            : _content(defaultRadius),
      ),
    );
  }

  Widget _content(BorderRadiusGeometry radius) {
    return Padding(
      padding: padding ?? EdgeInsets.zero,
      child: child,
    );
  }
}
