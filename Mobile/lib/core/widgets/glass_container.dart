import 'dart:ui';
import 'package:flutter/material.dart';

class GlassContainer extends StatelessWidget {
  final Widget child;
  final double? width;
  final double? height;
  final EdgeInsetsGeometry? padding;
  final EdgeInsetsGeometry? margin;
  final BorderRadiusGeometry? borderRadius;
  final Color backgroundColor;
  final double blur;
  final Border? border;
  final List<BoxShadow>? boxShadow;

  const GlassContainer({
    Key? key,
    required this.child,
    this.width,
    this.height,
    this.padding,
    this.margin,
    this.borderRadius,
    this.backgroundColor = const Color(0x1AFFFFFF), // 10% white by default
    this.blur = 15.0,
    this.border,
    this.boxShadow,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final defaultRadius = borderRadius ?? BorderRadius.circular(24.0);

    return Container(
      width: width,
      height: height,
      margin: margin,
      decoration: BoxDecoration(
        borderRadius: defaultRadius,
        border: border ??
            Border.all(
              color: Colors.white.withOpacity(0.08),
              width: 1.0,
            ),
        boxShadow: boxShadow,
      ),
      child: ClipRRect(
        borderRadius: defaultRadius,
        child: BackdropFilter(
          filter: ImageFilter.blur(sigmaX: blur, sigmaY: blur),
          child: Container(
            padding: padding,
            decoration: BoxDecoration(
              color: backgroundColor,
              borderRadius: defaultRadius,
            ),
            child: child,
          ),
        ),
      ),
    );
  }
}
