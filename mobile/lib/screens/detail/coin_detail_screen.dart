import 'package:flutter/material.dart';
import '../../models/coin.dart';
import '../../services/coin_service.dart';
import '../../services/collection_service.dart';

class CoinDetailScreen extends StatefulWidget {
  final String slug;

  const CoinDetailScreen({super.key, required this.slug});

  @override
  State<CoinDetailScreen> createState() => _CoinDetailScreenState();
}

class _CoinDetailScreenState extends State<CoinDetailScreen> with SingleTickerProviderStateMixin {
  final CoinService _coinService = CoinService();
  final CollectionService _collectionService = CollectionService();

  CoinDetail? _coin;
  bool _isLoading = true;
  String? _errorMessage;

  // Swipe / Tab Görsel İndeksi (0 = Ön Yüz, 1 = Arka Yüz)
  int _activeFaceIndex = 0;
  late final PageController _pageController;

  // Kondisyon Kaydırıcısı (Slider)
  // 0 = Poor, 1 = Fine, 2 = VF, 3 = XF, 4 = UNC
  double _conditionSliderValue = 2.0; // Varsayılan VF
  final List<String> _conditionGrades = ['Poor', 'Fine', 'VF', 'XF', 'UNC'];

  bool _isAddingToCollection = false;

  @override
  void initState() {
    super.initState();
    _pageController = PageController();
    _loadDetail();
  }

  Future<void> _loadDetail() async {
    setState(() {
      _isLoading = true;
      _errorMessage = null;
    });

    try {
      final detail = await _coinService.fetchCoinDetail(widget.slug);
      if (detail != null) {
        setState(() {
          _coin = detail;
        });
      } else {
        setState(() => _errorMessage = 'Sikke bilgisi yüklenemedi.');
      }
    } catch (e) {
      setState(() => _errorMessage = 'Bağlantı hatası: $e');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  Valuation? _getCurrentValuation() {
    if (_coin == null || _coin!.valuations.isEmpty) return null;
    final currentGrade = _conditionGrades[_conditionSliderValue.round()];
    try {
      return _coin!.valuations.firstWhere(
        (v) => v.conditionGrade.toUpperCase() == currentGrade.toUpperCase(),
      );
    } catch (_) {
      return null;
    }
  }

  Future<void> _handleAddToCollection() async {
    if (_coin == null || _isAddingToCollection) return;

    setState(() => _isAddingToCollection = true);
    final selectedGrade = _conditionGrades[_conditionSliderValue.round()];

    final isOnlineSuccess = await _collectionService.addToCollection(
      coinId: _coin!.id,
      condition: selectedGrade,
      notes: 'Mobil envanter üzerinden eklendi.',
    );

    if (!mounted) return;
    setState(() => _isAddingToCollection = false);

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          isOnlineSuccess
              ? 'Sikke koleksiyon defterinize başarıyla eklendi!'
              : 'Çevrimdışı kaydedildi. İnternet bağlandığında eşitlenecek.',
        ),
        backgroundColor: isOnlineSuccess ? Colors.green[700] : Colors.orange[800],
        duration: const Duration(seconds: 3),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        backgroundColor: Color(0xFF090D16),
        body: Center(child: CircularProgressIndicator(color: Colors.amber)),
      );
    }

    if (_errorMessage != null || _coin == null) {
      return Scaffold(
        backgroundColor: const Color(0xFF090D16),
        appBar: AppBar(backgroundColor: Colors.transparent, elevation: 0),
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Text(_errorMessage ?? 'Hata oluştu', style: const TextStyle(color: Colors.white70)),
              const SizedBox(height: 12),
              ElevatedButton(
                onPressed: _loadDetail,
                style: ElevatedButton.styleFrom(backgroundColor: Colors.amber),
                child: const Text('Tekrar Dene', style: TextStyle(color: Colors.black)),
              ),
            ],
          ),
        ),
      );
    }

    final coin = _coin!;
    final currentValuation = _getCurrentValuation();
    final activeGrade = _conditionGrades[_conditionSliderValue.round()];

    final obverseImage = coin.images.where((i) => i.imageType == 'obverse').firstOrNull?.imageUrl ?? coin.primaryImageUrl;
    final reverseImage = coin.images.where((i) => i.imageType == 'reverse').firstOrNull?.imageUrl ?? coin.primaryImageUrl;

    return Scaffold(
      backgroundColor: const Color(0xFF090D16),
      body: CustomScrollView(
        slivers: [
          // 1. Üst Başlık & Swipe Edilebilir Avers / Revers Galerisi
          SliverAppBar(
            expandedHeight: 340,
            pinned: true,
            backgroundColor: const Color(0xFF0F172A),
            leading: IconButton(
              icon: const Icon(Icons.arrow_back, color: Colors.white),
              onPressed: () => Navigator.pop(context),
            ),
            actions: [
              Container(
                margin: const EdgeInsets.only(right: 16),
                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                decoration: BoxDecoration(
                  color: Colors.black87,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.Border.all(color: Colors.amber.withOpacity(0.4)),
                ),
                child: Center(
                  child: Text(
                    coin.rarityGrade,
                    style: const TextStyle(color: Colors.amber, fontWeight: FontWeight.bold, fontSize: 12),
                  ),
                ),
              ),
            ],
            flexibleSpace: FlexibleSpaceBar(
              background: Stack(
                fit: StackFit.expand,
                children: [
                  // Avers / Revers Swipe Sayfaları
                  PageView(
                    controller: _pageController,
                    onPageChanged: (index) {
                      setState(() => _activeFaceIndex = index);
                    },
                    children: [
                      _buildFaceImageView(obverseImage, 'ÖN YÜZ (AVERS)'),
                      _buildFaceImageView(reverseImage, 'ARKA YÜZ (REVERS)'),
                    ],
                  ),

                  // Avers / Revers Geçiş Sekmeleri (Pills)
                  Positioned(
                    bottom: 16,
                    left: 0,
                    right: 0,
                    child: Center(
                      child: Container(
                        padding: const EdgeInsets.all(4),
                        decoration: BoxDecoration(
                          color: Colors.black87.withOpacity(0.8),
                          borderRadius: BorderRadius.circular(24),
                          border: Border.Border.all(color: Colors.white12),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            _buildFaceToggleTab('Ön Yüz (Avers)', 0),
                            const SizedBox(width: 4),
                            _buildFaceToggleTab('Arka Yüz (Revers)', 1),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),

          // 2. İçerik Alanı
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(20),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // 2863 Sayılı Kanun Rozeti
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.amber.withOpacity(0.08),
                      borderRadius: BorderRadius.circular(12),
                      border: Border.Border.all(color: Colors.amber.withOpacity(0.3)),
                    ),
                    child: const Row(
                      children: [
                        Icon(Icons.shield_outlined, color: Colors.amber, size: 20),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            '2863 Sayılı Kanun Uyarınca: Yalnızca akademik/eğitim amaçlı referans arşividir. Alım-satım yapılmaz.',
                            style: TextStyle(color: Colors.amber, fontSize: 11, height: 1.3),
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 20),

                  // Sikke Başlığı & Katalog No
                  Text(
                    coin.catalogReference,
                    style: const TextStyle(
                      fontFamily: 'monospace',
                      color: Colors.amber,
                      fontSize: 13,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    coin.title,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      height: 1.25,
                    ),
                  ),
                  const SizedBox(height: 16),

                  // Temel Özellikler Rozet Şeridi
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [
                      _buildInfoBadge('Maden', coin.metal.toUpperCase()),
                      _buildInfoBadge('Birim', coin.denomination),
                      if (coin.weightG != null) _buildInfoBadge('Ağırlık', '${coin.weightG} g'),
                      if (coin.diameterMm != null) _buildInfoBadge('Çap', '${coin.diameterMm} mm'),
                      if (coin.ruler != null) _buildInfoBadge('Hükümdar', coin.ruler!.name),
                    ],
                  ),

                  const SizedBox(height: 24),

                  // 3. İNTERAKTİF KONDİSYON VE DEĞERLEME KAYDIRICISI (SLIDER)
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: const Color(0xFF131D31),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.Border.all(color: Colors.white10),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text(
                              'PİYASA REFERANS DEĞERİ',
                              style: TextStyle(
                                color: Colors.grey,
                                fontSize: 11,
                                fontWeight: FontWeight.bold,
                                letterSpacing: 1.1,
                              ),
                            ),
                            Container(
                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                              decoration: BoxDecoration(
                                color: Colors.amber.withOpacity(0.2),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: Text(
                                activeGrade,
                                style: const TextStyle(color: Colors.amber, fontWeight: FontWeight.bold, fontSize: 12),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 12),

                        // Dinamik Fiyat Gösterimi
                        Text(
                          currentValuation != null ? currentValuation.priceRangeFormatted : 'Referans Verisi Yok',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            fontFamily: 'monospace',
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Uluslararası müzayede çekiç fiyatları baz alınmıştır (USD).',
                          style: TextStyle(color: Colors.grey, fontSize: 10),
                        ),

                        const SizedBox(height: 16),

                        // Slider
                        SliderTheme(
                          data: SliderTheme.of(context).copyWith(
                            activeTrackColor: Colors.amber,
                            inactiveTrackColor: Colors.grey[800],
                            thumbColor: Colors.amber,
                            overlayColor: Colors.amber.withOpacity(0.2),
                            trackHeight: 4,
                          ),
                          child: Slider(
                            value: _conditionSliderValue,
                            min: 0,
                            max: 4,
                            divisions: 4,
                            onChanged: (val) {
                              setState(() => _conditionSliderValue = val);
                            },
                          ),
                        ),

                        // Slider Alt Etiketleri
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: _conditionGrades.map((grade) {
                            final isCurrent = grade == activeGrade;
                            return Text(
                              grade,
                              style: TextStyle(
                                color: isCurrent ? Colors.amber : Colors.grey[600],
                                fontWeight: isCurrent ? FontWeight.bold : FontWeight.normal,
                                fontSize: 11,
                              ),
                            );
                          }).toList(),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),

                  // 4. LEJANT VE TASVİR AKORDİYONU (EXPANDABLE ACCORDION)
                  _buildLegendAccordion(
                    title: 'ÖN YÜZ LEJANTI VE TASVİRİ (AVERS)',
                    legend: coin.obverseLegend,
                    translation: coin.obverseLegendTrans,
                    description: coin.obverseDesc,
                  ),

                  const SizedBox(height: 12),

                  _buildLegendAccordion(
                    title: 'ARKA YÜZ LEJANTI VE TASVİRİ (REVERS)',
                    legend: coin.reverseLegend,
                    translation: coin.reverseLegendTrans,
                    description: coin.reverseDesc,
                  ),

                  const SizedBox(height: 32),

                  // 5. "KOLEKSİYONUMA EKLE" BUTONU
                  SizedBox(
                    width: double.infinity,
                    height: 52,
                    child: ElevatedButton.icon(
                      onPressed: _isAddingToCollection ? null : _handleAddToCollection,
                      icon: _isAddingToCollection
                          ? const SizedBox(
                              width: 20,
                              height: 20,
                              child: CircularProgressIndicator(strokeWidth: 2, color: Colors.black),
                            )
                          : const Icon(Icons.bookmark_add, color: Colors.black),
                      label: Text(
                        _isAddingToCollection ? 'Kaydediliyor...' : 'Koleksiyonuma Ekle ($activeGrade)',
                        style: const TextStyle(
                          color: Colors.black,
                          fontWeight: FontWeight.bold,
                          fontSize: 15,
                        ),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.amber,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                        elevation: 4,
                      ),
                    ),
                  ),

                  const SizedBox(height: 40),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFaceImageView(String? imageUrl, String faceName) {
    return Container(
      color: const Color(0xFF0A0F1D),
      padding: const EdgeInsets.fromLTRB(20, 40, 20, 60),
      child: Center(
        child: imageUrl != null
            ? Image.network(
                imageUrl,
                fit: BoxFit.contain,
                errorBuilder: (_, __, ___) => const Icon(Icons.circle, size: 80, color: Colors.white24),
              )
            : const Icon(Icons.circle, size: 80, color: Colors.white24),
      ),
    );
  }

  Widget _buildFaceToggleTab(String title, int index) {
    final isActive = _activeFaceIndex == index;
    return GestureDetector(
      onTap: () {
        _pageController.animateToPage(
          index,
          duration: const Duration(milliseconds: 300),
          curve: Curves.easeInOut,
        );
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 6),
        decoration: BoxDecoration(
          color: isActive ? Colors.amber : Colors.transparent,
          borderRadius: BorderRadius.circular(18),
        ),
        child: Text(
          title,
          style: TextStyle(
            color: isActive ? Colors.black : Colors.white70,
            fontSize: 11,
            fontWeight: FontWeight.bold,
          ),
        ),
      ),
    );
  }

  Widget _buildInfoBadge(String label, String value) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: const Color(0xFF1E293B),
        borderRadius: BorderRadius.circular(8),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text('$label: ', style: const TextStyle(color: Colors.grey, fontSize: 11)),
          Text(value, style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildLegendAccordion({
    required String title,
    required String? legend,
    required String? translation,
    required String? description,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: const Color(0xFF131D31),
        borderRadius: BorderRadius.circular(14),
        border: Border.Border.all(color: Colors.white10),
      ),
      child: Theme(
        data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
        child: ExpansionTile(
          initiallyExpanded: true,
          iconColor: Colors.amber,
          collapsedIconColor: Colors.grey,
          title: Text(
            title,
            style: const TextStyle(
              color: Colors.amber,
              fontSize: 12,
              fontWeight: FontWeight.bold,
              letterSpacing: 1.1,
            ),
          ),
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 0, 16, 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Orijinal Lejant
                  const Text('ORİJİNAL LEJANT (YAZI):', style: TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 4),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: const Color(0xFF0A0F1D),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(
                      legend ?? 'YAZISIZ (ANEPIGRAPHIC)',
                      style: const TextStyle(
                        fontFamily: 'serif',
                        color: Colors.white,
                        fontSize: 14,
                        letterSpacing: 1.5,
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Türkçe Çözümleme
                  if (translation != null && translation.isNotEmpty) ...[
                    const Text('TÜRKÇE ÇÖZÜMLEMESİ:', style: TextStyle(color: Colors.emerald, fontSize: 10, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 4),
                    Text(
                      '"$translation"',
                      style: const TextStyle(color: Colors.white70, fontSize: 13, fontStyle: FontStyle.italic, height: 1.4),
                    ),
                    const SizedBox(height: 12),
                  ],

                  // Tasvir
                  if (description != null && description.isNotEmpty) ...[
                    const Text('TASVİR VE İKONOGRAFİ:', style: TextStyle(color: Colors.grey, fontSize: 10, fontWeight: FontWeight.bold)),
                    const SizedBox(height: 4),
                    Text(
                      description,
                      style: const TextStyle(color: Colors.white70, fontSize: 12, height: 1.4),
                    ),
                  ],
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

