import 'dart:async';
import 'package:path/path.dart';
import 'package:sqflite/sqflite.dart';

class LocalDatabase {
  static final LocalDatabase _instance = LocalDatabase._internal();
  factory LocalDatabase() => _instance;
  LocalDatabase._internal();

  static Database? _database;

  Future<Database> get database async {
    if (_database != null) return _database!;
    _database = await _initDb();
    return _database!;
  }

  Future<Database> _initDb() async {
    final dbPath = await getDatabasesPath();
    final path = join(dbPath, 'numismatic_offline.db');

    return await openDatabase(
      path,
      version: 1,
      onCreate: _onCreate,
    );
  }

  Future<void> _onCreate(Database db, int version) async {
    // 1. Yerel Çevrimdışı Sanal Envanter Tablosu
    await db.execute('''
      CREATE TABLE local_collections (
        local_id TEXT PRIMARY KEY,
        server_id INTEGER,
        coin_id INTEGER NOT NULL,
        coin_title TEXT,
        coin_image TEXT,
        user_condition TEXT NOT NULL,
        purchase_date TEXT,
        notes TEXT,
        sync_status TEXT NOT NULL,
        updated_at TEXT NOT NULL
      )
    ''');

    // 2. Çevrimdışı Sikke Kataloğu Önbellek Tablosu (Müze & Arazi Erişimi)
    await db.execute('''
      CREATE TABLE cached_coins (
        id INTEGER PRIMARY KEY,
        slug TEXT UNIQUE,
        title TEXT NOT NULL,
        denomination TEXT,
        metal TEXT,
        period_name TEXT,
        ruler_name TEXT,
        primary_image_url TEXT,
        data_json TEXT NOT NULL,
        cached_at TEXT NOT NULL
      )
    ''');
  }

  // Envanter İşlemleri (DAO)

  Future<void> insertLocalItem(Map<String, dynamic> item) async {
    final db = await database;
    await db.insert('local_collections', item, conflictAlgorithm: ConflictAlgorithm.replace);
  }

  Future<List<Map<String, dynamic>>> getUnsyncedItems() async {
    final db = await database;
    return await db.query(
      'local_collections',
      where: 'sync_status != ?',
      whereArgs: ['synced'],
    );
  }

  Future<List<Map<String, dynamic>>> getAllCollections() async {
    final db = await database;
    return await db.query(
      'local_collections',
      where: 'sync_status != ?',
      whereArgs: ['deleted_offline'],
      orderBy: 'updated_at DESC',
    );
  }

  Future<void> markAsSynced(String localId, int serverId) async {
    final db = await database;
    await db.update(
      'local_collections',
      {
        'server_id': serverId,
        'sync_status': 'synced',
      },
      where: 'local_id = ?',
      whereArgs: [localId],
    );
  }

  Future<void> deleteLocalItem(String localId) async {
    final db = await database;
    final item = await db.query('local_collections', where: 'local_id = ?', whereArgs: [localId]);
    if (item.isNotEmpty && item.first['server_id'] != null) {
      // Sunucuya silme bilgisini iletmek için işaretle
      await db.update(
        'local_collections',
        {'sync_status': 'deleted_offline'},
        where: 'local_id = ?',
        whereArgs: [localId],
      );
    } else {
      // Henüz sunucuya gitmediyse doğrudan yerelden sil
      await db.delete('local_collections', where: 'local_id = ?', whereArgs: [localId]);
    }
  }

  Future<void> cleanDeletedSyncedItems(List<int> serverIds) async {
    final db = await database;
    for (final sId in serverIds) {
      await db.delete('local_collections', where: 'server_id = ?', whereArgs: [sId]);
    }
  }
}

