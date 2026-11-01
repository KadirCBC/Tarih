import 'offline_sync_service.dart';

class CollectionService {
  final OfflineSyncService _offlineSync = OfflineSyncService();

  /// Sikkeyi koleksiyona çevrimdışı öncelikli (Offline-First) SQLite veritabanına ekler.
  /// İnternet bağlantısı varsa eşzamanlı olarak sunucuya iletir.
  Future<bool> addToCollection({
    required int coinId,
    required String condition,
    String? coinTitle,
    String? coinImage,
    String? notes,
  }) async {
    final result = await _offlineSync.addToCollectionOfflineFirst(
      coinId: coinId,
      condition: condition,
      coinTitle: coinTitle,
      coinImage: coinImage,
      notes: notes,
    );

    return result['is_synced'] == true;
  }

  /// Sunucuyla iki yönlü senkronizasyonu manuel tetikler.
  Future<bool> syncOfflineQueue() async {
    return await _offlineSync.syncBidirectional();
  }
}
