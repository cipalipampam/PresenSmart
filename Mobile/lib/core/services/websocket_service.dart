import 'package:dart_pusher_channels/dart_pusher_channels.dart';
import '../constants/app_constants.dart';

class WebSocketService {
  static final WebSocketService _instance = WebSocketService._internal();
  factory WebSocketService() => _instance;
  WebSocketService._internal();

  PusherChannelsClient? _client;

  // Callback functions for UI/Providers to listen to
  Function(Map<String, dynamic>)? onAnnouncementCreated;
  Function(Map<String, dynamic>)? onAnnouncementUpdated;
  Function(Map<String, dynamic>)? onAttendanceLogged;
  Function(Map<String, dynamic>)? onAttendanceApproved;
  Function(Map<String, dynamic>)? onSettingsUpdated;
  Function(Map<String, dynamic>)? onStatsUpdated;

  Future<void> init({required String token, int? userId}) async {
    print("WebSocket: Initializing for user $userId...");

    try {
      final hostOptions = PusherChannelsOptions.fromHost(
        scheme: 'ws',
        host: AppConstants.reverbHost,
        port: AppConstants.reverbPort,
        key: AppConstants.reverbKey,
      );

      _client = PusherChannelsClient.websocket(
        options: hostOptions,
        connectionErrorHandler: (exception, trace, client) {
          print("WebSocket Connection Error: $exception");
        },
      );

      _client!.lifecycleStream.listen((state) {
        print("WebSocket Connection State: $state");
      });

      // Subscribe to Public Channels
      _subscribeToPublicChannel("announcements");
      _subscribeToPublicChannel("attendance-channel");
      _subscribeToPublicChannel("system-settings");
      _subscribeToPublicChannel("dashboard-stats");

      // Subscribe to Private User Channel for personal notifications
      if (userId != null) {
        _subscribeToPrivateChannel(
          "private-App.Models.User.$userId",
          token: token,
        );
      }

      await _client!.connect();
      print("WebSocket: Connect request sent!");
    } catch (e) {
      print("WebSocket Init Error: $e");
    }
  }

  void _subscribeToPublicChannel(String channelName) {
    if (_client == null) return;
    final channel = _client!.publicChannel(channelName);
    _bindEvents(channel);
    channel.subscribe();
  }

  void _subscribeToPrivateChannel(String channelName, {required String token}) {
    if (_client == null) return;

    // Fix #5: Auth endpoint should be /broadcasting/auth (Laravel default),
    // NOT /api/broadcasting/auth (which doesn't exist).
    // The baseUrl includes /api/v1 so we build the auth URL explicitly from root.
    final rootUrl = AppConstants.baseUrl.replaceFirst(RegExp(r'/api/v1$'), '');

    final channel = _client!.privateChannel(
      channelName,
      authorizationDelegate:
          EndpointAuthorizableChannelTokenAuthorizationDelegate
              .forPrivateChannel(
        authorizationEndpoint: Uri.parse("$rootUrl/broadcasting/auth"),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/x-www-form-urlencoded',
        },
      ),
    );

    _bindEvents(channel);
    channel.subscribe();
  }

  void _bindEvents(Channel channel) {
    channel.bindToAll().listen((event) {
      print("WebSocket Raw Event: ${event.name} on ${event.channelName}");
      _handleEvent(event);
    });
  }

  void _handleEvent(ChannelReadEvent event) {
    if (event.data == null) return;

    try {
      final dataMap = event.tryGetDataAsMap();
      if (dataMap == null) return;

      // Laravel wraps event data in a nested 'data' key when using broadcastWith()
      final raw = dataMap.containsKey('data') ? dataMap['data'] : dataMap;
      if (raw is! Map) return;

      final data = Map<String, dynamic>.from(raw);

      // Normalize event name: strip namespace prefix (e.g. "App\Events\AttendanceLogged" → "AttendanceLogged")
      final eventName = event.name.split('.').last.split('\\').last;

      switch (eventName) {
        case 'AnnouncementCreated':
          onAnnouncementCreated?.call(data);
          break;
        // Fix #7 (from Sprint 2 list, added here): Handle AnnouncementUpdated
        case 'AnnouncementUpdated':
          onAnnouncementUpdated?.call(data);
          break;
        case 'AttendanceLogged':
          onAttendanceLogged?.call(data);
          break;
        case 'AttendanceApproved':
          onAttendanceApproved?.call(data);
          break;
        case 'SystemSettingsUpdated':
          onSettingsUpdated?.call(data);
          break;
        case 'DashboardStatsUpdated':
          onStatsUpdated?.call(data);
          break;
        default:
          print("WebSocket: Unhandled event: ${event.name}");
      }
    } catch (e) {
      print("Error parsing WebSocket event: $e");
    }
  }

  Future<void> disconnect() async {
    await _client?.disconnect();
    _client = null;
  }
}
