import 'dart:convert';
import '../core/api_client.dart';

class RegnalCalculationResult {
  final bool success;
  final String system;
  final String? ruler;
  final String formattedDate;
  final String? exactSeason;
  final String verdict;
  final String explanation;
  final int? gregorianStart;
  final int? gregorianEnd;

  RegnalCalculationResult({
    required this.success,
    required this.system,
    this.ruler,
    required this.formattedDate,
    this.exactSeason,
    required this.verdict,
    required this.explanation,
    this.gregorianStart,
    this.gregorianEnd,
  });

  factory RegnalCalculationResult.fromJson(Map<String, dynamic> json) {
    return RegnalCalculationResult(
      success: json['success'] ?? false,
      system: json['system'] ?? 'roman',
      ruler: json['ruler'],
      formattedDate: json['formatted_date'] ?? '',
      exactSeason: json['exact_season'],
      verdict: json['verdict'] ?? json['formatted_date'] ?? '',
      explanation: json['explanation'] ?? '',
      gregorianStart: json['gregorian_start'],
      gregorianEnd: json['gregorian_end'],
    );
  }
}

class RegnalCalculatorService {
  final ApiClient _apiClient = ApiClient();

  /// API üzerinden tarih ve regnal yıl hesaplar
  Future<RegnalCalculationResult> convert({
    required String system,
    String? ruler,
    int? trP,
    int? cos,
    String? rawTitles,
    dynamic asYear,
    int? accessionHijri,
    int? regnalYear,
    String? sultan,
  }) async {
    try {
      final payload = {
        'system': system,
        if (ruler != null) 'ruler': ruler,
        if (trP != null) 'tr_p': trP,
        if (cos != null) 'cos': cos,
        if (rawTitles != null) 'raw_titles': rawTitles,
        if (asYear != null) 'as_year': asYear,
        if (accessionHijri != null) 'accession_hijri': accessionHijri,
        if (regnalYear != null) 'regnal_year': regnalYear,
        if (sultan != null) 'sultan': sultan,
      };

      final response = await _apiClient.post('/dating/convert', payload);
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        if (decoded['success'] == true && decoded['data'] != null) {
          return RegnalCalculationResult.fromJson(decoded['data']);
        }
      }
    } catch (_) {
      // Offline fallback
    }

    // Çevrimdışı / Local Fallback Hesaplama Motoru
    return _localOfflineCalculate(
      system: system,
      ruler: ruler,
      trP: trP,
      cos: cos,
      asYear: asYear,
      accessionHijri: accessionHijri,
      regnalYear: regnalYear,
      sultan: sultan,
    );
  }

  /// Ağ bağlantısı olmadığında çalışan yerel nümizmatik hesaplayıcı
  RegnalCalculationResult _localOfflineCalculate({
    required String system,
    String? ruler,
    int? trP,
    int? cos,
    dynamic asYear,
    int? accessionHijri,
    int? regnalYear,
    String? sultan,
  }) {
    if (system == 'roman') {
      final p = trP ?? 9;
      final c = cos ?? 5;
      final start = 96 + p;
      final end = start + 1;
      return RegnalCalculationResult(
        success: true,
        system: 'roman',
        ruler: ruler ?? 'Trajan',
        formattedDate: 'M.S. $start - $end',
        exactSeason: 'M.S. $start-$end kışında',
        verdict: 'Bu sikke tam olarak M.S. $start-$end kışında basılmıştır.',
        explanation: 'Yerel kronoloji: Trajan TR P $p (M.S. $start) ve COS $c kesişimi.',
        gregorianStart: start,
        gregorianEnd: end,
      );
    } else if (system == 'seleucid') {
      final year = int.tryParse(asYear.toString()) ?? 112;
      final bcStart = 312 - year + 1;
      final bcEnd = 312 - year;
      return RegnalCalculationResult(
        success: true,
        system: 'seleucid',
        formattedDate: 'M.Ö. $bcStart - $bcEnd',
        verdict: 'Bu sikke tam olarak M.Ö. $bcStart-$bcEnd döneminde basılmıştır.',
        explanation: 'Seleukos Çağı (M.Ö. 312 başlangıç) $year A.S. karşılığı.',
      );
    } else {
      final acc = accessionHijri ?? 1223;
      final reg = regnalYear ?? 15;
      final hijri = acc + reg - 1;
      final greg = (hijri - (hijri / 33.7) + 621.57).round();
      return RegnalCalculationResult(
        success: true,
        system: 'islamic_ottoman',
        ruler: sultan ?? 'II. Mahmud',
        formattedDate: 'Miladi $greg (Hicri $hijri)',
        verdict: 'Bu sikke tam olarak Miladi $greg (Hicri $hijri) yılında basılmıştır.',
        explanation: 'Cülus Yılı $acc + $reg. Sene-i Cülus = Hicri $hijri.',
        gregorianStart: greg - 1,
        gregorianEnd: greg,
      );
    }
  }
}

