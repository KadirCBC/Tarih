import 'dart:io';
import 'package:dio/dio.dart';
import '../core/api_client.dart';
import '../models/coin.dart';

class CoinService {
  final Dio _dio = ApiClient().dio;

  /// Sikke kataloğu listeleme ve filtreleme
  Future<List<Coin>> fetchCoins({
    String? query,
    String? period,
    String? metal,
    int page = 1,
  }) async {
    final response = await _dio.get('/coins', queryParameters: {
      if (query != null && query.isNotEmpty) 'q': query,
      if (period != null && period.isNotEmpty) 'period': period,
      if (metal != null && metal.isNotEmpty) 'metal': metal,
      'page': page,
    });

    if (response.data['success'] == true) {
      final items = response.data['data']['items'] as List<dynamic>;
      return items.map((json) => Coin.fromJson(json)).toList();
    }
    return [];
  }

  /// Sikke detayını getirme
  Future<CoinDetail?> fetchCoinDetail(String slug) async {
    final response = await _dio.get('/coins/$slug');
    if (response.data['success'] == true) {
      return CoinDetail.fromJson(response.data['data']);
    }
    return null;
  }

  /// Mobil kamera ön ve arka yüz fotoğraflarını tek seferde API'ye gönderme
  Future<Map<String, dynamic>> identifyCoin({
    required String obversePath,
    required String reversePath,
  }) async {
    final formData = FormData.fromMap({
      'obverse_image': await MultipartFile.fromFile(
        obversePath,
        filename: 'obverse_${DateTime.now().millisecondsSinceEpoch}.jpg',
      ),
      'reverse_image': await MultipartFile.fromFile(
        reversePath,
        filename: 'reverse_${DateTime.now().millisecondsSinceEpoch}.jpg',
      ),
      'top_k': 3,
    });

    final response = await _dio.post('/coins/identify', data: formData);

    if (response.data['success'] == true) {
      return response.data['data'];
    }
    throw Exception(response.data['message'] ?? 'Eşleştirme yapılamadı.');
  }

  /// Dönem listesini çekme
  Future<List<Period>> fetchPeriods() async {
    final response = await _dio.get('/periods');
    if (response.data['success'] == true) {
      final items = response.data['data'] as List<dynamic>;
      return items.map((json) => Period.fromJson(json)).toList();
    }
    return [];
  }
}

