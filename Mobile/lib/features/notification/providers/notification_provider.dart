import 'package:flutter/foundation.dart';
import '../../../core/network/api_client.dart';
import '../../../core/services/websocket_service.dart';
import '../models/notification_model.dart';

class NotificationProvider with ChangeNotifier {
  final ApiClient _apiClient = ApiClient();

  List<AppNotificationModel> _notifications = [];
  bool _isLoading = false;
  String? _errorMessage;
  String _activeFilter = 'all'; // 'all' or 'unread'
  int _unreadCount = 0;

  List<AppNotificationModel> get notifications => _notifications;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  String get activeFilter => _activeFilter;
  int get unreadCount => _unreadCount;

  NotificationProvider() {
    WebSocketService().addNotificationListener((data) {
      debugPrint('NotificationProvider: incoming realtime notification: $data');
      try {
        final item = AppNotificationModel.fromJson(data);
        if (!_notifications.any((n) => n.id == item.id)) {
          _notifications.insert(0, item);
          if (!item.isRead) {
            _unreadCount++;
          }
          notifyListeners();
        }
      } catch (e) {
        debugPrint('NotificationProvider: error parsing realtime notification: $e');
      }
    });
  }

  Future<void> fetchNotifications({String? filter, bool showLoading = true}) async {
    if (filter != null) {
      _activeFilter = filter;
    }

    if (showLoading) {
      _isLoading = true;
      _errorMessage = null;
      notifyListeners();
    }

    try {
      final queryParams = <String, dynamic>{
        'filter': _activeFilter,
        'per_page': 30,
      };

      final response = await _apiClient.client.get(
        '/notifications',
        queryParameters: queryParams,
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        final List<dynamic> items = data['data'] ?? [];
        _notifications = items
            .whereType<Map<String, dynamic>>()
            .map((e) => AppNotificationModel.fromJson(e))
            .toList();

        // Calculate or update unread count
        if (_activeFilter == 'unread') {
          _unreadCount = data['total'] as int? ?? _notifications.length;
        } else {
          _unreadCount = _notifications.where((n) => !n.isRead).length;
        }
        _errorMessage = null;
      } else {
        _errorMessage = response.data['message'] as String? ?? 'Gagal memuat notifikasi.';
      }
    } catch (e) {
      _errorMessage = 'Gagal terhubung ke server.';
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> markAsRead(int notificationId) async {
    try {
      final response = await _apiClient.client.patch('/notifications/$notificationId/read');
      if (response.statusCode == 200 && response.data['success'] == true) {
        final index = _notifications.indexWhere((n) => n.id == notificationId);
        if (index != -1 && !_notifications[index].isRead) {
          _notifications[index] = _notifications[index].copyWith(
            isRead: true,
            readAt: DateTime.now(),
          );
          if (_unreadCount > 0) {
            _unreadCount--;
          }
          notifyListeners();
        }
        return true;
      }
    } catch (e) {
      debugPrint('Error markAsRead: $e');
    }
    return false;
  }

  Future<bool> markAllAsRead() async {
    try {
      final response = await _apiClient.client.post('/notifications/mark-all-read');
      if (response.statusCode == 200 && response.data['success'] == true) {
        _notifications = _notifications
            .map((n) => n.copyWith(isRead: true, readAt: DateTime.now()))
            .toList();
        _unreadCount = 0;
        notifyListeners();
        return true;
      }
    } catch (e) {
      debugPrint('Error markAllAsRead: $e');
    }
    return false;
  }
}

