import 'dart:ui';
import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../services/currency_service.dart';

class ApiClient {
  static const String baseUrl = 'https://api.numizmatik.local/api/v1'; // veya 10.0.2.2 Android emülatör için
  late final Dio dio;

  static final ApiClient _instance = ApiClient._internal();
  factory ApiClient() => _instance;

  ApiClient._internal() {
    dio = Dio(
      BaseOptions(
        baseUrl: baseUrl,
        connectTimeout: const Duration(seconds: 15),
        receiveTimeout: const Duration(seconds: 15),
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
      ),
    );

    // Oturum (Sanctum), Çoklu Dil (i18n) ve Para Birimi Interceptor'ı
    dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final prefs = await SharedPreferences.getInstance();

          // 1. Auth Bearer Token
          final token = prefs.getString('sanctum_token');
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
          }

          // 2. Çoklu Dil (tr, en, de) Başlığı
          final savedLocale = prefs.getString('app_locale');
          final deviceLanguage = PlatformDispatcher.instance.locale.languageCode;
          final activeLocale = savedLocale ?? (['tr', 'de'].contains(deviceLanguage) ? deviceLanguage : 'en');
          options.headers['X-Locale'] = activeLocale;
          options.headers['Accept-Language'] = activeLocale;

          // 3. Konum / Tercihe Göre Otomatik Para Birimi (USD, EUR, TRY)
          final activeCurrency = await CurrencyService.getActiveCurrency();
          options.headers['X-Currency'] = activeCurrency;

          return handler.next(options);
        },
        onError: (DioException error, handler) {
          // 401 Unauthorized durumunda oturumu sıfırlama
          if (error.response?.statusCode == 401) {
            SharedPreferences.getInstance().then((prefs) => prefs.remove('sanctum_token'));
          }
          return handler.next(error);
        },
      ),
    );
  }
}
