class Period {
  final int id;
  final String name;
  final String slug;
  final String? formattedDateRange;

  Period({
    required this.id,
    required this.name,
    required this.slug,
    this.formattedDateRange,
  });

  factory Period.fromJson(Map<String, dynamic> json) {
    return Period(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      formattedDateRange: json['formatted_date_range'],
    );
  }
}

class Ruler {
  final int id;
  final String name;
  final String? title;
  final String slug;

  Ruler({
    required this.id,
    required this.name,
    this.title,
    required this.slug,
  });

  factory Ruler.fromJson(Map<String, dynamic> json) {
    return Ruler(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      title: json['title'],
      slug: json['slug'] ?? '',
    );
  }
}

class Valuation {
  final int id;
  final String conditionGrade;
  final String? conditionLabel;
  final double minValueUsd;
  final double maxValueUsd;
  final String priceRangeFormatted;

  Valuation({
    required this.id,
    required this.conditionGrade,
    this.conditionLabel,
    required this.minValueUsd,
    required this.maxValueUsd,
    required this.priceRangeFormatted,
  });

  factory Valuation.fromJson(Map<String, dynamic> json) {
    return Valuation(
      id: json['id'] ?? 0,
      conditionGrade: json['condition_grade'] ?? 'VF',
      conditionLabel: json['condition_label'],
      minValueUsd: (json['min_value_usd'] ?? 0).toDouble(),
      maxValueUsd: (json['max_value_usd'] ?? 0).toDouble(),
      priceRangeFormatted: json['price_range_formatted'] ?? '',
    );
  }
}

class CoinImageItem {
  final int id;
  final String imageType;
  final String imageUrl;
  final bool isPrimary;

  CoinImageItem({
    required this.id,
    required this.imageType,
    required this.imageUrl,
    required this.isPrimary,
  });

  factory CoinImageItem.fromJson(Map<String, dynamic> json) {
    return CoinImageItem(
      id: json['id'] ?? 0,
      imageType: json['image_type'] ?? 'both',
      imageUrl: json['image_url'] ?? '',
      isPrimary: json['is_primary'] ?? false,
    );
  }
}

class Coin {
  final int id;
  final String title;
  final String slug;
  final String catalogReference;
  final String denomination;
  final String metal;
  final String? metalLabel;
  final double? weightG;
  final double? diameterMm;
  final String rarityGrade;
  final Period? period;
  final Ruler? ruler;
  final String? primaryImageUrl;

  Coin({
    required this.id,
    required this.title,
    required this.slug,
    required this.catalogReference,
    required this.denomination,
    required this.metal,
    this.metalLabel,
    this.weightG,
    this.diameterMm,
    required this.rarityGrade,
    this.period,
    this.ruler,
    this.primaryImageUrl,
  });

  factory Coin.fromJson(Map<String, dynamic> json) {
    return Coin(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      catalogReference: json['catalog_reference'] ?? '',
      denomination: json['denomination'] ?? '',
      metal: json['metal'] ?? 'silver',
      metalLabel: json['metal_label'],
      weightG: json['weight_g'] != null ? (json['weight_g'] as num).toDouble() : null,
      diameterMm: json['diameter_mm'] != null ? (json['diameter_mm'] as num).toDouble() : null,
      rarityGrade: json['rarity_grade'] ?? 'C',
      period: json['period'] != null ? Period.fromJson(json['period']) : null,
      ruler: json['ruler'] != null ? Ruler.fromJson(json['ruler']) : null,
      primaryImageUrl: json['primary_image']?['image_url'],
    );
  }
}

class CoinDetail extends Coin {
  final String? obverseDesc;
  final String? obverseLegend;
  final String? obverseLegendTrans;
  final String? reverseDesc;
  final String? reverseLegend;
  final String? reverseLegendTrans;
  final String? historicalNotes;
  final List<CoinImageItem> images;
  final List<Valuation> valuations;

  CoinDetail({
    required super.id,
    required super.title,
    required super.slug,
    required super.catalogReference,
    required super.denomination,
    required super.metal,
    super.metalLabel,
    super.weightG,
    super.diameterMm,
    required super.rarityGrade,
    super.period,
    super.ruler,
    super.primaryImageUrl,
    this.obverseDesc,
    this.obverseLegend,
    this.obverseLegendTrans,
    this.reverseDesc,
    this.reverseLegend,
    this.reverseLegendTrans,
    this.historicalNotes,
    required this.images,
    required this.valuations,
  });

  factory CoinDetail.fromJson(Map<String, dynamic> json) {
    final obverse = json['obverse'] as Map<String, dynamic>? ?? {};
    final reverse = json['reverse'] as Map<String, dynamic>? ?? {};

    return CoinDetail(
      id: json['id'] ?? 0,
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      catalogReference: json['catalog_reference'] ?? '',
      denomination: json['denomination'] ?? '',
      metal: json['metal'] ?? 'silver',
      metalLabel: json['metal_label'],
      weightG: json['weight_g'] != null ? (json['weight_g'] as num).toDouble() : null,
      diameterMm: json['diameter_mm'] != null ? (json['diameter_mm'] as num).toDouble() : null,
      rarityGrade: json['rarity_grade'] ?? 'C',
      period: json['period'] != null ? Period.fromJson(json['period']) : null,
      ruler: json['ruler'] != null ? Ruler.fromJson(json['ruler']) : null,
      primaryImageUrl: json['primary_image']?['image_url'],
      obverseDesc: obverse['desc'],
      obverseLegend: obverse['legend'],
      obverseLegendTrans: obverse['legend_translation'],
      reverseDesc: reverse['desc'],
      reverseLegend: reverse['legend'],
      reverseLegendTrans: reverse['legend_translation'],
      historicalNotes: json['historical_notes'],
      images: (json['images'] as List<dynamic>? ?? [])
          .map((item) => CoinImageItem.fromJson(item))
          .toList(),
      valuations: (json['valuations'] as List<dynamic>? ?? [])
          .map((item) => Valuation.fromJson(item))
          .toList(),
    );
  }
}

