import 'dart:ui';
import 'package:shared_preferences/shared_preferences.dart';
import '../core/api_client.dart';

class CurrencyService {
  static const String prefKeyCurrency = 'selected_currency';

  static const List<String> supportedCurrencies = ['USD', 'EUR', 'TRY', 'GBP'];

  /// Kullanıcının cihaz konumuna/bölgesine göre varsayılan para birimini tahmin eder.
  /// Türkiye (TR) -> TRY
  /// Avrupa Birliği ülkeleri (DE, FR, IT, ES, GR, AT vb.) -> EUR
  /// Diğer tüm küresel nümizmatlar -> USD
  static String detectDefaultCurrency() {
    final locale = PlatformDispatcher.instance.locale;
    final countryCode = locale.countryCode?.toUpperCase() ?? '';

    if (countryCode == 'TR' || locale.languageCode == 'tr') {
      return 'TRY';
    }

    const euroCountries = [
      'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'GR', 'PT', 'AT', 'FI', 'IE', 'SK', 'SI', 'CY', 'EE', 'LV', 'LT', 'LU', 'MT'
    ];

    if (euroCountries.contains(countryCode) || locale.languageCode == 'de') {
      return 'EUR';
    }

    if (countryCode == 'GB') {
      return 'GBP';
    }

    return 'USD';
  }

  /// Aktif para birimini döndürür (önce hafızadaki kullanıcı tercihi, yoksa konuma göre otomatik tespit).
  static Future<String> getActiveCurrency() async {
    final prefs = await SharedPreferences.getInstance();
    final saved = prefs.getString(prefKeyCurrency);

    if (saved != null && supportedCurrencies.contains(saved)) {
      return saved;
    }

    final detected = detectDefaultCurrency();
    await prefs.setString(prefKeyCurrency, detected);
    return detected;
  }

  /// Kullanıcının para birimi tercihini kaydeder.
  static Future<void> setActiveCurrency(String currencyCode) async {
    if (supportedCurrencies.contains(currencyCode)) {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(prefKeyCurrency, currencyCode);
    }
  }

  /// Backend'den güncel kurları ve sembolleri çeker.
  static Future<Map<String, dynamic>?> fetchRates() async {
    try {
      final response = await ApiClient().dio.get('/currencies');
      if (response.statusCode == 200 && response.data['success'] == true) {
        return response.data['data']['rates'];
      }
    } catch (_) {
      // Offline fallback
    }
    return null;
  }
}

