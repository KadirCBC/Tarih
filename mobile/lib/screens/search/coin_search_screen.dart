import 'dart:async';
import 'package:flutter/material.dart';
import '../../models/coin.dart';
import '../../services/coin_service.dart';
import '../detail/coin_detail_screen.dart';
import '../scanner/coin_scanner_screen.dart';

class CoinSearchScreen extends StatefulWidget {
  const CoinSearchScreen({super.key});

  @override
  State<CoinSearchScreen> createState() => _CoinSearchScreenState();
}

class _CoinSearchScreenState extends State<CoinSearchScreen> {
  final CoinService _coinService = CoinService();

  List<Coin> _coins = [];
  List<Period> _periods = [];
  bool _isLoading = true;

  String? _selectedPeriodSlug;
  String? _selectedMetal;
  String _searchQuery = '';
  Timer? _debounceTimer;

  final TextEditingController _searchController = TextEditingController();

  final List<Map<String, String>> _metals = [
    {'label': 'Tümü', 'value': ''},
    {'label': 'Altın', 'value': 'gold'},
    {'label': 'Gümüş', 'value': 'silver'},
    {'label': 'Bronz', 'value': 'bronze'},
    {'label': 'Elektrum', 'value': 'electrum'},
  ];

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  Future<void> _loadInitialData() async {
    setState(() => _isLoading = true);
    try {
      final periodsFuture = _coinService.fetchPeriods();
      final coinsFuture = _coinService.fetchCoins();

      final results = await Future.wait([periodsFuture, coinsFuture]);
      _periods = results[0] as List<Period>;
      _coins = results[1] as List<Coin>;
    } catch (e) {
      debugPrint('Veri yükleme hatası: $e');
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _onSearchChanged(String query) {
    _debounceTimer?.cancel();
    _debounceTimer = Timer(const Duration(milliseconds: 350), () {
      setState(() {
        _searchQuery = query;
      });
      _filterCoins();
    });
  }

  Future<void> _filterCoins() async {
    setState(() => _isLoading = true);
    try {
      final coins = await _coinService.fetchCoins(
        query: _searchQuery,
        period: _selectedPeriodSlug,
        metal: _selectedMetal,
      );
      if (mounted) {
        setState(() {
          _coins = coins;
        });
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  void dispose() {
    _debounceTimer?.cancel();
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF090D16),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        elevation: 0,
        title: const Text(
          'NÜMİZMATİK ANSİKLOPEDİSİ',
          style: TextStyle(
            fontFamily: 'Cinzel',
            fontSize: 16,
            fontWeight: FontWeight.bold,
            letterSpacing: 1.2,
            color: Colors.amber,
          ),
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.document_scanner, color: Colors.amber),
            tooltip: 'Sikke Tara (Kamera)',
            onPressed: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const CoinScannerScreen()),
              );
            },
          ),
        ],
      ),
      body: Column(
        children: [
          // 1. Arama Çubuğu
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
            child: TextField(
              controller: _searchController,
              onChanged: _onSearchChanged,
              style: const TextStyle(color: Colors.white, fontSize: 14),
              decoration: InputDecoration(
                filled: true,
                fillColor: const Color(0xFF1E293B),
                hintText: 'Lejant veya İmparator ara (örn: AVG, TRAIAN)...',
                hintStyle: TextStyle(color: Colors.grey[500], fontSize: 13),
                prefixIcon: const Icon(Icons.search, color: Colors.amber),
                suffixIcon: _searchController.text.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear, color: Colors.grey),
                        onPressed: () {
                          _searchController.clear();
                          _onSearchChanged('');
                        },
                      )
                    : null,
                contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide.none,
                ),
              ),
            ),
          ),

          // 2. Dönemler Yatay Kaydırmalı Çipler (Horizontal Chips)
          SizedBox(
            height: 44,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: [
                _buildPeriodChip('Tümü', null),
                ..._periods.map((p) => _buildPeriodChip(p.name, p.slug)),
              ],
            ),
          ),

          const SizedBox(height: 8),

          // 3. Metal Seçim Çipleri
          SizedBox(
            height: 36,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: _metals.map((m) {
                final isSelected = (_selectedMetal ?? '') == m['value'];
                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: FilterChip(
                    label: Text(
                      m['label']!,
                      style: TextStyle(
                        fontSize: 11,
                        color: isSelected ? Colors.black : Colors.grey[300],
                        fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                      ),
                    ),
                    selected: isSelected,
                    selectedColor: Colors.amber[400],
                    backgroundColor: const Color(0xFF1E293B),
                    checkmarkColor: Colors.black,
                    padding: const EdgeInsets.symmetric(horizontal: 8),
                    onSelected: (selected) {
                      setState(() {
                        _selectedMetal = selected ? m['value'] : '';
                      });
                      _filterCoins();
                    },
                  ),
                );
              }).toList(),
            ),
          ),

          const SizedBox(height: 12),

          // 4. Sikke Listesi Gridi
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator(color: Colors.amber))
                : _coins.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.search_off, size: 54, color: Colors.grey[700]),
                            const SizedBox(height: 12),
                            const Text(
                              'Eşleşen sikke kaydı bulunamadı.',
                              style: TextStyle(color: Colors.grey),
                            ),
                          ],
                        ),
                      )
                    : RefreshIndicator(
                        color: Colors.amber,
                        onRefresh: _filterCoins,
                        child: GridView.builder(
                          padding: const EdgeInsets.all(16),
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            childAspectRatio: 0.68,
                            crossAxisSpacing: 12,
                            mainAxisSpacing: 12,
                          ),
                          itemCount: _coins.length,
                          itemBuilder: (context, index) {
                            final coin = _coins[index];
                            return _buildCoinCard(coin);
                          },
                        ),
                      ),
          ),
        ],
      ),
    );
  }

  Widget _buildPeriodChip(String label, String? slug) {
    final isSelected = _selectedPeriodSlug == slug;
    return Padding(
      padding: const EdgeInsets.only(right: 8),
      child: ChoiceChip(
        label: Text(
          label,
          style: TextStyle(
            fontSize: 12,
            color: isSelected ? Colors.black : Colors.white70,
            fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
          ),
        ),
        selected: isSelected,
        selectedColor: Colors.amber,
        backgroundColor: const Color(0xFF1E293B),
        onSelected: (selected) {
          setState(() {
            _selectedPeriodSlug = selected ? slug : null;
          });
          _filterCoins();
        },
      ),
    );
  }

  Widget _buildCoinCard(Coin coin) {
    return GestureDetector(
      onTap: () {
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => CoinDetailScreen(slug: coin.slug),
          ),
        );
      },
      child: Container(
        decoration: BoxDecoration(
          color: const Color(0xFF131D31),
          borderRadius: BorderRadius.circular(16),
          border: Border.Border.all(color: Colors.white.withOpacity(0.08)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Resim Alanı
            Expanded(
              child: Stack(
                children: [
                  Container(
                    width: double.infinity,
                    decoration: const BoxDecoration(
                      color: Color(0xFF0A0F1D),
                      borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
                    ),
                    padding: const EdgeInsets.all(8),
                    child: coin.primaryImageUrl != null
                        ? Image.network(
                            coin.primaryImageUrl!,
                            fit: BoxFit.contain,
                            errorBuilder: (_, __, ___) => const Icon(Icons.broken_image, color: Colors.grey),
                          )
                        : const Icon(Icons.circle_outlined, size: 48, color: Colors.white24),
                  ),
                  Positioned(
                    top: 8,
                    left: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: Colors.black87,
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        coin.metal.toUpperCase(),
                        style: const TextStyle(color: Colors.amber, fontSize: 9, fontWeight: FontWeight.bold),
                      ),
                    ),
                  ),
                  Positioned(
                    bottom: 8,
                    right: 8,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                      decoration: BoxDecoration(
                        color: Colors.purple.withOpacity(0.8),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        coin.rarityGrade,
                        style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold),
                      ),
                    ),
                  ),
                ],
              ),
            ),

            // Bilgi Alanı
            Padding(
              padding: const EdgeInsets.all(10),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    coin.catalogReference,
                    style: const TextStyle(color: Colors.amber, fontSize: 10, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    coin.title,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w600),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '${coin.ruler?.name ?? coin.period?.name ?? ''} • ${coin.denomination}',
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(color: Colors.grey[400], fontSize: 10),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

