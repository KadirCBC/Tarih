import 'dart:convert';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../core/api_client.dart';

class CollectionService {
  final Dio _dio = ApiClient().dio;
  static const String _offlineQueueKey = 'offline_collection_queue';

  /// Sikkeyi koleksiyona ekleme (Offline kuyruk destekli)
  Future<bool> addToCollection({
    required int coinId,
    required String condition,
    String? notes,
  }) async {
    final payload = {
      'coin_id': coinId,
      'user_condition': condition,
      'purchase_date': DateTime.now().toIso8601String().substring(0, 10),
      'notes': notes ?? '',
    };

    try {
      final response = await _dio.post('/my-collection/add', data: payload);
      if (response.data['success'] == true) {
        return true;
      }
    } catch (_) {
      // Çevrimdışı ise yerel kuyruğa ekle
      await _enqueueOfflineItem(payload);
      return false; // Yerel kuyruğa alındı bildirimi için
    }

    return false;
  }

  /// Çevrimdışı kaydedilen kuyruğu sunucuya senkronize etme
  Future<void> syncOfflineQueue() async {
    final prefs = await SharedPreferences.getInstance();
    final queueJson = prefs.getStringList(_offlineQueueKey) ?? [];

    if (queueJson.isEmpty) return;

    final remaining = <String>[];

    for (final itemStr in queueJson) {
      try {
        final payload = jsonDecode(itemStr);
        final response = await _dio.post('/my-collection/add', data: payload);
        if (response.data['success'] != true) {
          remaining.add(itemStr);
        }
      } catch (_) {
        remaining.add(itemStr);
      }
    }

    await prefs.setStringList(_offlineQueueKey, remaining);
  }

  Future<void> _enqueueOfflineItem(Map<String, dynamic> item) async {
    final prefs = await SharedPreferences.getInstance();
    final queue = prefs.getStringList(_offlineQueueKey) ?? [];
    queue.add(jsonEncode(item));
    await prefs.setStringList(_offlineQueueKey, queue);
  }
}

