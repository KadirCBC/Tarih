import 'dart:io';
import 'package:flutter/material.dart';
import 'package:camera/camera.dart';
import '../../services/coin_service.dart';
import '../detail/coin_detail_screen.dart';

enum ScanStep { obverse, reverse, analyzing }

class CoinScannerScreen extends StatefulWidget {
  const CoinScannerScreen({super.key});

  @override
  State<CoinScannerScreen> createState() => _CoinScannerScreenState();
}

class _CoinScannerScreenState extends State<CoinScannerScreen> {
  CameraController? _cameraController;
  List<CameraDescription>? _cameras;
  bool _isCameraReady = false;

  ScanStep _currentStep = ScanStep.obverse;
  XFile? _obverseFile;
  XFile? _reverseFile;
  bool _isProcessing = false;

  final CoinService _coinService = CoinService();

  @override
  void initState() {
    super.initState();
    _initializeCamera();
  }

  Future<void> _initializeCamera() async {
    try {
      _cameras = await availableCameras();
      if (_cameras != null && _cameras!.isNotEmpty) {
        _cameraController = CameraController(
          _cameras![0],
          ResolutionPreset.high,
          enableAudio: false,
        );
        await _cameraController!.initialize();
        if (mounted) {
          setState(() {
            _isCameraReady = true;
          });
        }
      }
    } catch (e) {
      debugPrint('Kamera başlatma hatası: $e');
    }
  }

  @override
  void dispose() {
    _cameraController?.dispose();
    super.dispose();
  }

  Future<void> _captureImage() async {
    if (_cameraController == null || !_cameraController!.value.isInitialized || _isProcessing) {
      return;
    }

    setState(() => _isProcessing = true);

    try {
      final image = await _cameraController!.takePicture();

      if (_currentStep == ScanStep.obverse) {
        setState(() {
          _obverseFile = image;
          _currentStep = ScanStep.reverse;
          _isProcessing = false;
        });

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('Ön yüz çekildi! Şimdi sikkeyi çevirip arka yüzü hizalayın.'),
              backgroundColor: Colors.amber,
              duration: Duration(seconds: 2),
            ),
          );
        }
      } else if (_currentStep == ScanStep.reverse) {
        setState(() {
          _reverseFile = image;
          _currentStep = ScanStep.analyzing;
        });

        // Her iki görseli tek seferde API'ye gönder
        await _sendToIdentifyApi();
      }
    } catch (e) {
      setState(() => _isProcessing = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Fotoğraf çekilemedi: $e')),
        );
      }
    }
  }

  Future<void> _sendToIdentifyApi() async {
    if (_obverseFile == null || _reverseFile == null) return;

    try {
      final result = await _coinService.identifyCoin(
        obversePath: _obverseFile!.path,
        reversePath: _reverseFile!.path,
      );

      if (!mounted) return;

      _showIdentificationResults(result);
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Tanımlama hatası: $e')),
      );
      _resetScan();
    }
  }

  void _resetScan() {
    setState(() {
      _currentStep = ScanStep.obverse;
      _obverseFile = null;
      _reverseFile = null;
      _isProcessing = false;
    });
  }

  void _showIdentificationResults(Map<String, dynamic> result) {
    showModalBottomSheet(
      context: context,
      backgroundColor: const Color(0xFF0F172A),
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder: (context) {
        final candidates = result['candidates'] as List<dynamic>? ?? [];
        return DraggableScrollableSheet(
          initialChildSize: 0.65,
          minChildSize: 0.4,
          maxChildSize: 0.9,
          expand: false,
          builder: (context, scrollController) {
            return Padding(
              padding: const EdgeInsets.all(20),
              child: ListView(
                controller: scrollController,
                children: [
                  Center(
                    child: Container(
                      width: 40,
                      height: 4,
                      decoration: BoxDecoration(
                        color: Colors.slate[700],
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'EŞLEŞEN SİKKE ADAYLARI',
                    style: TextStyle(
                      fontFamily: 'Cinzel',
                      color: Colors.amber,
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      letterSpacing: 1.2,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    result['disclaimer'] ?? '',
                    style: const TextStyle(color: Colors.grey, fontSize: 11),
                  ),
                  const Divider(color: Colors.white10, height: 24),
                  ...candidates.map((cand) {
                    final coinData = cand['coin'];
                    final matchPercentage = cand['match_percentage'] ?? '%0';
                    return Card(
                      color: const Color(0xFF1E293B),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      margin: const EdgeInsets.only(bottom: 12),
                      child: ListTile(
                        contentPadding: const EdgeInsets.all(12),
                        leading: coinData['primary_image']?['image_url'] != null
                            ? ClipRRect(
                                borderRadius: BorderRadius.circular(8),
                                child: Image.network(
                                  coinData['primary_image']['image_url'],
                                  width: 50,
                                  height: 50,
                                  fit: BoxFit.cover,
                                ),
                              )
                            : Container(
                                width: 50,
                                height: 50,
                                decoration: BoxDecoration(
                                  color: Colors.black26,
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: const Icon(Icons.circle, color: Colors.amber),
                              ),
                        title: Text(
                          coinData['title'] ?? '',
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 14),
                        ),
                        subtitle: Text(
                          '${coinData['catalog_reference']} • ${coinData['denomination']}',
                          style: const TextStyle(color: Colors.grey, fontSize: 12),
                        ),
                        trailing: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                          decoration: BoxDecoration(
                            color: Colors.amber.withOpacity(0.15),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.Border.all(color: Colors.amber.withOpacity(0.4)),
                          ),
                          child: Text(
                            matchPercentage,
                            style: const TextStyle(color: Colors.amber, fontWeight: FontWeight.bold),
                          ),
                        ),
                        onTap: () {
                          Navigator.pop(context);
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (_) => CoinDetailScreen(slug: coinData['slug']),
                            ),
                          );
                        },
                      ),
                    );
                  }),
                ],
              ),
            );
          },
        );
      },
    ).whenComplete(() => _resetScan());
  }

  @override
  Widget build(BuildContext context) {
    if (!_isCameraReady || _cameraController == null) {
      return const Scaffold(
        backgroundColor: Colors.black,
        body: Center(child: CircularProgressIndicator(color: Colors.amber)),
      );
    }

    return Scaffold(
      backgroundColor: Colors.black,
      body: Stack(
        fit: StackFit.expand,
        children: [
          // 1. Kamera Önizlemesi
          CameraPreview(_cameraController!),

          // 2. Kılavuz Daire Şablonu (Coin Overlay)
          CustomPaint(
            painter: CoinGuideOverlayPainter(),
          ),

          // 3. Üst Bar: Bilgi & Çekilen Ön Yüz Küçük Resmi
          SafeArea(
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
              child: Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      IconButton(
                        icon: const Icon(Icons.close, color: Colors.white),
                        onPressed: () => Navigator.pop(context),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        decoration: BoxDecoration(
                          color: Colors.black.withOpacity(0.7),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.Border.all(color: Colors.amber.withOpacity(0.5)),
                        ),
                        child: Text(
                          _currentStep == ScanStep.obverse
                              ? 'ADIM 1: ÖN YÜZ (AVERS)'
                              : _currentStep == ScanStep.reverse
                                  ? 'ADIM 2: ARKA YÜZ (REVERS)'
                                  : 'ANALİZ EDİLİYOR...',
                          style: const TextStyle(
                            color: Colors.amber,
                            fontWeight: FontWeight.bold,
                            fontSize: 12,
                            letterSpacing: 1.1,
                          ),
                        ),
                      ),
                      if (_obverseFile != null)
                        Container(
                          width: 44,
                          height: 44,
                          decoration: BoxDecoration(
                            shape: BoxShape.circle,
                            border: Border.Border.all(color: Colors.amber, width: 2),
                            image: DecorationImage(
                              image: FileImage(File(_obverseFile!.path)),
                              fit: BoxFit.cover,
                            ),
                          ),
                        )
                      else
                        const SizedBox(width: 44),
                    ],
                  ),
                  const SizedBox(height: 16),
                  Text(
                    _currentStep == ScanStep.obverse
                        ? 'Sikkenin ön yüzünü kılavuz daireye hizalayın'
                        : 'Şimdi sikkeyi çevirin ve arka yüzü hizalayın',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 14,
                      shadows: [Shadow(color: Colors.black, blurRadius: 4)],
                    ),
                  ),
                ],
              ),
            ),
          ),

          // 4. Alt Deklanşör Çubuğu
          Positioned(
            bottom: 40,
            left: 0,
            right: 0,
            child: Column(
              children: [
                if (_currentStep == ScanStep.analyzing)
                  const Column(
                    children: [
                      CircularProgressIndicator(color: Colors.amber),
                      SizedBox(height: 12),
                      Text('AI Nümizmatik Eşleştirme Yapılıyor...', style: TextStyle(color: Colors.white)),
                    ],
                  )
                else
                  GestureDetector(
                    onTap: _captureImage,
                    child: Container(
                      width: 80,
                      height: 80,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        border: Border.Border.all(color: Colors.amber, width: 4),
                        color: Colors.white.withOpacity(0.2),
                      ),
                      child: Center(
                        child: Container(
                          width: 62,
                          height: 62,
                          decoration: const BoxDecoration(
                            shape: BoxShape.circle,
                            color: Colors.amber,
                          ),
                          child: Icon(
                            _currentStep == ScanStep.obverse ? Icons.camera_alt : Icons.flip_camera_android,
                            color: Colors.black,
                            size: 30,
                          ),
                        ),
                      ),
                    ),
                  ),
                const SizedBox(height: 12),
                Text(
                  _currentStep == ScanStep.obverse ? 'Ön Yüzü Çek' : 'Şimdi Arka Yüzü Çek',
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 0.8,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

/// Sikke Kılavuz Dairesi Özel Çizimi
class CoinGuideOverlayPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2 - 30);
    final radius = size.width * 0.40; // Ekran genişliğinin %80'i çap

    // Karartılmış dış katman
    final backgroundPaint = Paint()..color = Colors.black.withOpacity(0.65);
    final path = Path()
      ..addRect(Rect.fromLTWH(0, 0, size.width, size.height))
      ..addOval(Rect.fromCircle(center: center, radius: radius))
      ..fillType = PathFillType.evenOdd;
    canvas.drawPath(path, backgroundPaint);

    // Kılavuz Daire Altın Çerçevesi
    final borderPaint = Paint()
      ..color = const Color(0xFFF59E0B)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 3;
    canvas.drawCircle(center, radius, borderPaint);

    // Çapraz Hizalama Artı Çizgileri (Crosshairs)
    final crosshairPaint = Paint()
      ..color = Colors.amber.withOpacity(0.4)
      ..strokeWidth = 1.5;

    // Yatay ufak çentikler
    canvas.drawLine(Offset(center.dx - radius - 10, center.dy), Offset(center.dx - radius + 10, center.dy), crosshairPaint);
    canvas.drawLine(Offset(center.dx + radius - 10, center.dy), Offset(center.dx + radius + 10, center.dy), crosshairPaint);

    // Dikey ufak çentikler
    canvas.drawLine(Offset(center.dx, center.dy - radius - 10), Offset(center.dx, center.dy - radius + 10), crosshairPaint);
    canvas.drawLine(Offset(center.dx, center.dy + radius - 10), Offset(center.dx, center.dy + radius + 10), crosshairPaint);
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

