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

class MintInfo {
  final int id;
  final String name;
  final String slug;
  final String? location;
  final String? ancientName;
  final String? modernName;
  final String? activePeriod;
  final String? historicalSignificance;
  final double? latitude;
  final double? longitude;
  final bool hasCoordinates;

  MintInfo({
    required this.id,
    required this.name,
    required this.slug,
    this.location,
    this.ancientName,
    this.modernName,
    this.activePeriod,
    this.historicalSignificance,
    this.latitude,
    this.longitude,
    required this.hasCoordinates,
  });

  factory MintInfo.fromJson(Map<String, dynamic> json) {
    final lat = json['latitude'] != null ? (json['latitude'] as num).toDouble() : null;
    final lng = json['longitude'] != null ? (json['longitude'] as num).toDouble() : null;
    return MintInfo(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      location: json['location'],
      ancientName: json['ancient_name'],
      modernName: json['modern_name'],
      activePeriod: json['active_period'],
      historicalSignificance: json['historical_significance'],
      latitude: lat,
      longitude: lng,
      hasCoordinates: json['has_coordinates'] ?? (lat != null && lng != null),
    );
  }
}

class Valuation {
  final int id;
  final String conditionGrade;
  final String? conditionLabel;
  final String currency;
  final String currencySymbol;
  final double minValue;
  final double maxValue;
  final String priceRangeFormatted;
  final bool isLocked;

  Valuation({
    required this.id,
    required this.conditionGrade,
    this.conditionLabel,
    this.currency = 'USD',
    this.currencySymbol = '\$',
    required this.minValue,
    required this.maxValue,
    required this.priceRangeFormatted,
    this.isLocked = false,
  });

  factory Valuation.fromJson(Map<String, dynamic> json) {
    return Valuation(
      id: json['id'] ?? 0,
      conditionGrade: json['condition_grade'] ?? 'VF',
      conditionLabel: json['condition_label'],
      currency: json['currency'] ?? 'USD',
      currencySymbol: json['currency_symbol'] ?? '\$',
      minValue: (json['min_value'] ?? json['min_value_usd'] ?? 0).toDouble(),
      maxValue: (json['max_value'] ?? json['max_value_usd'] ?? 0).toDouble(),
      priceRangeFormatted: json['price_range_formatted'] ?? '',
      isLocked: json['is_locked'] ?? false,
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
  final MintInfo? mint;
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
    this.mint,
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
      mint: json['mint'] != null ? MintInfo.fromJson(json['mint']) : null,
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
    super.mint,
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
      mint: json['mint'] != null ? MintInfo.fromJson(json['mint']) : null,
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

