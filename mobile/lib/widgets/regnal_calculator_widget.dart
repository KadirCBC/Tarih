import 'package:flutter/material.dart';
import '../services/regnal_calculator_service.dart';

class RegnalCalculatorWidget extends StatefulWidget {
  final String? initialRulerSlug;
  final String? initialSystem;

  const RegnalCalculatorWidget({
    super.key,
    this.initialRulerSlug,
    this.initialSystem,
  });

  @override
  State<RegnalCalculatorWidget> createState() => _RegnalCalculatorWidgetState();
}

class _RegnalCalculatorWidgetState extends State<RegnalCalculatorWidget> {
  final RegnalCalculatorService _service = RegnalCalculatorService();

  String _selectedSystem = 'roman'; // 'roman', 'seleucid', 'islamic'
  String _selectedRomanRuler = 'trajan';
  int _trP = 9;
  int _cos = 5;

  // Seleukos
  final TextEditingController _seleucidController = TextEditingController(text: '112');

  // Osmanlı
  String _selectedSultan = 'mahmud-ii';
  int _accessionHijri = 1223;
  int _regnalYear = 15;

  RegnalCalculationResult? _result;
  bool _isLoading = false;

  final List<Map<String, String>> _romanRulers = [
    {'slug': 'trajan', 'name': 'Trajan (M.S. 98-117)'},
    {'slug': 'hadrian', 'name': 'Hadrian (M.S. 117-138)'},
    {'slug': 'augustus', 'name': 'Augustus (M.Ö. 27 - M.S. 14)'},
    {'slug': 'marcus-aurelius', 'name': 'Marcus Aurelius (M.S. 161-180)'},
    {'slug': 'antoninus-pius', 'name': 'Antoninus Pius (M.S. 138-161)'},
    {'slug': 'septimius-severus', 'name': 'Septimius Severus (M.S. 193-211)'},
    {'slug': 'caracalla', 'name': 'Caracalla (M.S. 198-217)'},
  ];

  final List<Map<String, dynamic>> _ottomanSultans = [
    {'slug': 'mahmud-ii', 'name': 'II. Mahmud', 'accession': 1223},
    {'slug': 'mehmed-ii', 'name': 'II. Mehmed (Fatih)', 'accession': 855},
    {'slug': 'suleyman-i', 'name': 'I. Süleyman (Kanuni)', 'accession': 926},
    {'slug': 'selim-iii', 'name': 'III. Selim', 'accession': 1203},
    {'slug': 'abdulmecid', 'name': 'Abdülmecid', 'accession': 1255},
    {'slug': 'abdulhamid-ii', 'name': 'II. Abdülhamid', 'accession': 1293},
  ];

  @override
  void initState() {
    super.initState();
    if (widget.initialSystem != null) {
      _selectedSystem = widget.initialSystem!;
    }
    if (widget.initialRulerSlug != null) {
      _selectedRomanRuler = widget.initialRulerSlug!;
    }
    _runCalculation();
  }

  Future<void> _runCalculation() async {
    setState(() => _isLoading = true);

    try {
      final res = await _service.convert(
        system: _selectedSystem,
        ruler: _selectedRomanRuler,
        trP: _trP,
        cos: _cos,
        asYear: _seleucidController.text.trim(),
        accessionHijri: _accessionHijri,
        regnalYear: _regnalYear,
        sultan: _selectedSultan,
      );

      if (mounted) {
        setState(() {
          _result = res;
          _isLoading = false;
        });
      }
    } catch (_) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF131D31),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.amber.withOpacity(0.35)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Başlık ve İkon
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: Colors.amber.withOpacity(0.15),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: const Icon(Icons.history_toggle_drop, color: Colors.amber, size: 20),
              ),
              const SizedBox(width: 8),
              const Expanded(
                child: Text(
                  'TARİH VE REGNAL YIL HESAPLAYICI',
                  style: TextStyle(
                    color: Colors.amber,
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 1.1,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),

          // Sistem Seçici (Segmented / Tabs)
          Container(
            padding: const EdgeInsets.all(3),
            decoration: BoxDecoration(
              color: const Color(0xFF0B111E),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Row(
              children: [
                _buildSystemTab('Roma', 'roman'),
                _buildSystemTab('Seleukos', 'seleucid'),
                _buildSystemTab('Osmanlı', 'islamic'),
              ],
            ),
          ),
          const SizedBox(height: 16),

          // Giriş Alanları (Sisteme Göre)
          if (_selectedSystem == 'roman') _buildRomanInputs(),
          if (_selectedSystem == 'seleucid') _buildSeleucidInputs(),
          if (_selectedSystem == 'islamic') _buildIslamicInputs(),

          const SizedBox(height: 16),

          // İnteraktif Sonuç Kartı
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  const Color(0xFF1E293B),
                  const Color(0xFF0F172A),
                ],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: Colors.amber.withOpacity(0.4)),
            ),
            child: _isLoading
                ? const Center(
                    child: Padding(
                      padding: EdgeInsets.all(12),
                      child: CircularProgressIndicator(color: Colors.amber, strokeWidth: 2),
                    ),
                  )
                : Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'HESAPLANAN KESİN TARİH',
                            style: TextStyle(
                              color: Colors.amber,
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 1.0,
                            ),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                            decoration: BoxDecoration(
                              color: Colors.white10,
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              _result?.formattedDate ?? 'M.S. 104 - 105',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                                fontFamily: 'monospace',
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 8),

                      // Büyük Sonuç Cümlesi (Verdict)
                      Text(
                        _result?.verdict ?? 'Bu sikke tam olarak M.S. 104-105 kışında basılmıştır.',
                        style: const TextStyle(
                          color: Colors.amberAccent,
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          height: 1.3,
                        ),
                      ),
                      const SizedBox(height: 8),

                      // Bilimsel Açıklama
                      Text(
                        _result?.explanation ?? 'Roma unvanları kesiştirildiğinde bu tarihe ulaşılır.',
                        style: const TextStyle(
                          color: Colors.white70,
                          fontSize: 11,
                          height: 1.4,
                        ),
                      ),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildSystemTab(String label, String systemKey) {
    final isSelected = _selectedSystem == systemKey;
    return Expanded(
      child: GestureDetector(
        onTap: () {
          setState(() => _selectedSystem = systemKey);
          _runCalculation();
        },
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 6),
          decoration: BoxDecoration(
            color: isSelected ? Colors.amber : Colors.transparent,
            borderRadius: BorderRadius.circular(8),
          ),
          alignment: Alignment.center,
          child: Text(
            label,
            style: TextStyle(
              color: isSelected ? Colors.black : Colors.white60,
              fontSize: 12,
              fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRomanInputs() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // İmparator Açılır Kutusu
        DropdownButtonFormField<String>(
          value: _selectedRomanRuler,
          dropdownColor: const Color(0xFF0F172A),
          style: const TextStyle(color: Colors.white, fontSize: 13),
          decoration: InputDecoration(
            labelText: 'İmparator Seçin',
            labelStyle: const TextStyle(color: Colors.grey, fontSize: 11),
            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
          ),
          items: _romanRulers.map((r) {
            return DropdownMenuItem(value: r['slug'], child: Text(r['name']!));
          }).toList(),
          onChanged: (val) {
            if (val != null) {
              setState(() => _selectedRomanRuler = val);
              _runCalculation();
            }
          },
        ),
        const SizedBox(height: 12),

        // TR P ve COS Seçicileri
        Row(
          children: [
            Expanded(
              child: DropdownButtonFormField<int>(
                value: _trP,
                dropdownColor: const Color(0xFF0F172A),
                style: const TextStyle(color: Colors.white, fontSize: 13),
                decoration: InputDecoration(
                  labelText: 'TR P (Tribünlük)',
                  labelStyle: const TextStyle(color: Colors.grey, fontSize: 11),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                ),
                items: List.generate(20, (i) => i + 1).map((num) {
                  return DropdownMenuItem(value: num, child: Text('TR P $num'));
                }).toList(),
                onChanged: (val) {
                  if (val != null) {
                    setState(() => _trP = val);
                    _runCalculation();
                  }
                },
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: DropdownButtonFormField<int>(
                value: _cos,
                dropdownColor: const Color(0xFF0F172A),
                style: const TextStyle(color: Colors.white, fontSize: 13),
                decoration: InputDecoration(
                  labelText: 'COS (Konsüllük)',
                  labelStyle: const TextStyle(color: Colors.grey, fontSize: 11),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                ),
                items: List.generate(10, (i) => i + 1).map((num) {
                  return DropdownMenuItem(value: num, child: Text('COS $num'));
                }).toList(),
                onChanged: (val) {
                  if (val != null) {
                    setState(() => _cos = val);
                    _runCalculation();
                  }
                },
              ),
            ),
          ],
        ),
      ],
    );
  }

  Widget _buildSeleucidInputs() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        TextField(
          controller: _seleucidController,
          style: const TextStyle(color: Colors.white, fontSize: 14, fontFamily: 'monospace'),
          keyboardType: TextInputType.text,
          decoration: InputDecoration(
            labelText: 'Seleukos Yılı (A.S. veya Grek Harfi)',
            labelStyle: const TextStyle(color: Colors.grey, fontSize: 11),
            hintText: 'Örn: 112 veya ΒΙΡ',
            hintStyle: const TextStyle(color: Colors.white24),
            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
          ),
          onChanged: (_) => _runCalculation(),
        ),
      ],
    );
  }

  Widget _buildIslamicInputs() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        DropdownButtonFormField<String>(
          value: _selectedSultan,
          dropdownColor: const Color(0xFF0F172A),
          style: const TextStyle(color: Colors.white, fontSize: 13),
          decoration: InputDecoration(
            labelText: 'Osmanlı Padişahı Seçin',
            labelStyle: const TextStyle(color: Colors.grey, fontSize: 11),
            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
          ),
          items: _ottomanSultans.map((s) {
            return DropdownMenuItem<String>(
              value: s['slug'] as String,
              child: Text('${s['name']} (${s['accession']} H.)'),
            );
          }).toList(),
          onChanged: (val) {
            if (val != null) {
              final sultan = _ottomanSultans.firstWhere((s) => s['slug'] == val);
              setState(() {
                _selectedSultan = val;
                _accessionHijri = sultan['accession'] as int;
              });
              _runCalculation();
            }
          },
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            Expanded(
              child: TextFormField(
                initialValue: _accessionHijri.toString(),
                style: const TextStyle(color: Colors.white, fontSize: 13, fontFamily: 'monospace'),
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  labelText: 'Cülus Yılı (Hicri)',
                  labelStyle: const TextStyle(color: Colors.grey, fontSize: 11),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                ),
                onChanged: (val) {
                  final parsed = int.tryParse(val);
                  if (parsed != null) {
                    setState(() => _accessionHijri = parsed);
                    _runCalculation();
                  }
                },
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: TextFormField(
                initialValue: _regnalYear.toString(),
                style: const TextStyle(color: Colors.amber, fontSize: 13, fontWeight: FontWeight.bold, fontFamily: 'monospace'),
                keyboardType: TextInputType.number,
                decoration: InputDecoration(
                  labelText: 'Sene-i Cülus',
                  labelStyle: const TextStyle(color: Colors.grey, fontSize: 11),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(10)),
                ),
                onChanged: (val) {
                  final parsed = int.tryParse(val);
                  if (parsed != null) {
                    setState(() => _regnalYear = parsed);
                    _runCalculation();
                  }
                },
              ),
            ),
          ],
        ),
      ],
    );
  }
}

