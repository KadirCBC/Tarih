import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../core/api_client.dart';
import '../core/local_database.dart';

class OfflineSyncService {
  final Dio _dio = ApiClient().dio;
  final LocalDatabase _localDb = LocalDatabase();
  static const String prefKeyLastSync = 'last_inventory_sync_at';

  static final OfflineSyncService _instance = OfflineSyncService._internal();
  factory OfflineSyncService() => _instance;
  OfflineSyncService._internal();

  /// Envantere çevrimdışı öncelikli (Offline-First) sikke ekleme.
  /// İnternet olmasa dahi anında yerel SQLite veritabanına yazar.
  /// İnternet varsa arka planda anında sunucuyla eşitler.
  Future<Map<String, dynamic>> addToCollectionOfflineFirst({
    required int coinId,
    required String condition,
    String? coinTitle,
    String? coinImage,
    String? notes,
  }) async {
    final localId = 'loc_${DateTime.now().millisecondsSinceEpoch}_${coinId}';
    final nowIso = DateTime.now().toIso8601String();

    final localItem = {
      'local_id': localId,
      'server_id': null,
      'coin_id': coinId,
      'coin_title': coinTitle ?? 'Antik Sikke #$coinId',
      'coin_image': coinImage,
      'user_condition': condition,
      'purchase_date': nowIso.substring(0, 10),
      'notes': notes ?? '',
      'sync_status': 'created_offline',
      'updated_at': nowIso,
    };

    // 1. Yerel SQLite'a kaydet (0 gecikme)
    await _localDb.insertLocalItem(localItem);

    // 2. İnternet bağlantısı varsa hemen iki yönlü senkronizasyonu tetikle
    final isOnline = await checkConnectivity();
    if (isOnline) {
      syncBidirectional().catchError((_) {});
    }

    return {
      'local_id': localId,
      'is_synced': isOnline,
      'message': isOnline 
          ? 'Sikke koleksiyonunuza eklendi ve sunucuyla eşitlendi.' 
          : 'İnternet yok: Sikke çevrimdışı envanterinize kaydedildi. Bağlantı sağlandığında eşitlenecektir.',
    };
  }

  /// Sunucuyla iki yönlü (Bidirectional) senkronizasyon yürütür.
  Future<bool> syncBidirectional() async {
    final isOnline = await checkConnectivity();
    if (!isOnline) {
      return false;
    }

    try {
      final prefs = await SharedPreferences.getInstance();
      final lastSyncAt = prefs.getString(prefKeyLastSync);

      // 1. Yereldeki senkronize edilmemiş kayıtları topla
      final unsynced = await _localDb.getUnsyncedItems();

      final createdPayload = <Map<String, dynamic>>[];
      final updatedPayload = <Map<String, dynamic>>[];
      final deletedServerIds = <int>[];

      for (final row in unsynced) {
        final status = row['sync_status'];
        if (status == 'created_offline') {
          createdPayload.add({
            'local_id': row['local_id'],
            'coin_id': row['coin_id'],
            'user_condition': row['user_condition'],
            'purchase_date': row['purchase_date'],
            'notes': row['notes'],
          });
        } else if (status == 'updated_offline' && row['server_id'] != null) {
          updatedPayload.add({
            'server_id': row['server_id'],
            'user_condition': row['user_condition'],
            'purchase_date': row['purchase_date'],
            'notes': row['notes'],
          });
        } else if (status == 'deleted_offline' && row['server_id'] != null) {
          deletedServerIds.add(row['server_id'] as int);
        }
      }

      // Senkronizasyon isteği paketi
      final syncData = {
        'last_synced_at': lastSyncAt,
        'created': createdPayload,
        'updated': updatedPayload,
        'deleted': deletedServerIds,
      };

      // 2. /api/v1/my-collection/sync Uç Noktasına Gönder
      final response = await _dio.post('/my-collection/sync', data: syncData);

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];

        // A. Oluşturulan mapping'leri yerel veritabanında güncelle
        final createdMapping = data['created_mapping'] as Map<String, dynamic>? ?? {};
        for (final entry in createdMapping.entries) {
          await _localDb.markAsSynced(entry.key, entry.value as int);
        }

        // B. İstemcide silinmiş olanları SQLite'tan tamamen kaldır
        if (deletedServerIds.isNotEmpty) {
          await _localDb.cleanDeletedSyncedItems(deletedServerIds);
        }

        // C. Sunucudan gelen yeni/değişen kayıtları yerel SQLite'a işle
        final serverChanges = data['server_changes'] as List<dynamic>? ?? [];
        for (final item in serverChanges) {
          final serverId = item['id'];
          final coin = item['coin'] ?? {};
          await _localDb.insertLocalItem({
            'local_id': 'srv_$serverId',
            'server_id': serverId,
            'coin_id': item['coin_id'] ?? coin['id'] ?? 0,
            'coin_title': coin['title'] ?? 'Antik Sikke',
            'coin_image': coin['primary_image']?['image_url'],
            'user_condition': item['user_condition'] ?? 'VF',
            'purchase_date': item['purchase_date'],
            'notes': item['notes'] ?? '',
            'sync_status': 'synced',
            'updated_at': DateTime.now().toIso8601String(),
          });
        }

        // D. Son senkronizasyon zamanını kaydet
        final newSyncTime = data['synced_at'] ?? DateTime.now().toIso8601String();
        await prefs.setString(prefKeyLastSync, newSyncTime);

        return true;
      }
    } catch (_) {
      // Hata durumunda yerel veri korunur, sonraki bağlantıda tekrar denenir
    }

    return false;
  }

  /// İnternet bağlantı denetimi
  Future<bool> checkConnectivity() async {
    final connectivityResult = await Connectivity().checkConnectivity();
    return connectivityResult != ConnectivityResult.none;
  }
}

