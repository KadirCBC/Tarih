import 'package:flutter/material.dart';
import '../../services/quiz_service.dart';

class DailyQuizScreen extends StatefulWidget {
  const DailyQuizScreen({super.key});

  @override
  State<DailyQuizScreen> createState() => _DailyQuizScreenState();
}

class _DailyQuizScreenState extends State<DailyQuizScreen> {
  final QuizService _quizService = QuizService();

  bool _isLoading = true;
  DailyQuizModel? _quiz;
  QuizAnswerResult? _answerResult;
  int? _selectedOptionIndex;
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _fetchDailyChallenge();
  }

  Future<void> _fetchDailyChallenge() async {
    setState(() => _isLoading = true);
    final quiz = await _quizService.getDailyChallenge();
    if (mounted) {
      setState(() {
        _quiz = quiz;
        _isLoading = false;
      });
    }
  }

  Future<void> _onOptionSelected(int index) async {
    if (_quiz == null || _answerResult != null || _isSubmitting) return;

    setState(() {
      _selectedOptionIndex = index;
      _isSubmitting = true;
    });

    final result = await _quizService.submitAnswer(_quiz!.questionId, index);

    if (mounted) {
      setState(() {
        _answerResult = result;
        _isSubmitting = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF090D16),
      appBar: AppBar(
        backgroundColor: const Color(0xFF0F172A),
        title: const Row(
          children: [
            Icon(Icons.workspace_premium, color: Colors.amber, size: 22),
            SizedBox(width: 8),
            Text(
              'Günün Sikkesi Bilgi Yarışması',
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
          ],
        ),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh, color: Colors.white70),
            onPressed: _fetchDailyChallenge,
          ),
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator(color: Colors.amber))
          : _quiz == null
              ? _buildErrorState()
              : SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      _buildStreakHeader(),
                      const SizedBox(height: 16),
                      _buildMysteryCoinCard(),
                      const SizedBox(height: 16),
                      _buildQuestionCard(),
                      const SizedBox(height: 16),
                      _buildOptionsList(),
                      if (_answerResult != null) ...[
                        const SizedBox(height: 20),
                        _buildResultSection(),
                      ],
                      const SizedBox(height: 24),
                      _buildLawDisclaimer(),
                    ],
                  ),
                ),
    );
  }

  Widget _buildStreakHeader() {
    final streak = _answerResult != null
        ? _answerResult!.streakDays
        : (_quiz?.currentStreak ?? 0);
    final points = _answerResult != null
        ? _answerResult!.totalPoints
        : (_quiz?.totalPoints ?? 0);

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.amber.withOpacity(0.3)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          Row(
            children: [
              const Text('🔥', style: TextStyle(fontSize: 20)),
              const SizedBox(width: 6),
              Text(
                '$streak Gün Serisi',
                style: const TextStyle(
                  color: Colors.amber,
                  fontWeight: FontWeight.bold,
                  fontSize: 15,
                ),
              ),
            ],
          ),
          Container(height: 24, width: 1, color: Colors.white24),
          Row(
            children: [
              const Icon(Icons.stars, color: Colors.amberAccent, size: 20),
              const SizedBox(width: 6),
              Text(
                '$points Puan',
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: 15,
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildMysteryCoinCard() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            const Color(0xFF1E293B),
            const Color(0xFF0F172A),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white12),
      ),
      child: Column(
        children: [
          Container(
            width: 140,
            height: 140,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              border: Border.all(color: Colors.amber.withOpacity(0.6), width: 3),
              boxShadow: [
                BoxShadow(
                  color: Colors.amber.withOpacity(0.15),
                  blurRadius: 16,
                  spreadRadius: 2,
                ),
              ],
            ),
            child: ClipOval(
              child: _quiz?.obverseImage != null &&
                      _quiz!.obverseImage!.isNotEmpty
                  ? Image.network(
                      _quiz!.obverseImage!,
                      fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => _buildCoinPlaceholder(),
                    )
                  : _buildCoinPlaceholder(),
            ),
          ),
          const SizedBox(height: 12),
          Text(
            _quiz?.coinName ?? 'Günün Gizemli Sikkesi',
            style: const TextStyle(
              color: Colors.amber,
              fontSize: 14,
              fontWeight: FontWeight.w600,
              letterSpacing: 0.5,
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _buildCoinPlaceholder() {
    return Container(
      color: const Color(0xFF1E293B),
      child: const Center(
        child: Icon(Icons.monetization_on, color: Colors.amber, size: 64),
      ),
    );
  }

  Widget _buildQuestionCard() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white10),
      ),
      child: Text(
        _quiz!.questionText,
        style: const TextStyle(
          color: Colors.white,
          fontSize: 16,
          fontWeight: FontWeight.w600,
          height: 1.4,
        ),
      ),
    );
  }

  Widget _buildOptionsList() {
    return Column(
      children: List.generate(_quiz!.options.length, (index) {
        final optionText = _quiz!.options[index];
        final isSelected = _selectedOptionIndex == index;

        Color bgColor = const Color(0xFF1E293B);
        Color borderColor = Colors.white12;
        Color textColor = Colors.white;

        if (_answerResult != null) {
          if (index == _answerResult!.correctOptionIndex) {
            bgColor = Colors.green.withOpacity(0.2);
            borderColor = Colors.greenAccent;
            textColor = Colors.greenAccent;
          } else if (isSelected && !_answerResult!.isCorrect) {
            bgColor = Colors.red.withOpacity(0.2);
            borderColor = Colors.redAccent;
            textColor = Colors.redAccent;
          }
        } else if (isSelected) {
          borderColor = Colors.amber;
          bgColor = Colors.amber.withOpacity(0.1);
        }

        return Container(
          margin: const EdgeInsets.only(bottom: 10),
          child: InkWell(
            onTap: _answerResult == null && !_isSubmitting
                ? () => _onOptionSelected(index)
                : null,
            borderRadius: BorderRadius.circular(14),
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
              decoration: BoxDecoration(
                color: bgColor,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: borderColor, width: 1.5),
              ),
              child: Row(
                children: [
                  Container(
                    width: 28,
                    height: 28,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.white.withOpacity(0.08),
                    ),
                    child: Center(
                      child: Text(
                        String.fromCharCode(65 + index), // A, B, C, D
                        style: const TextStyle(
                          color: Colors.amber,
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Text(
                      optionText,
                      style: TextStyle(
                        color: textColor,
                        fontSize: 15,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ),
                  if (_answerResult != null &&
                      index == _answerResult!.correctOptionIndex)
                    const Icon(Icons.check_circle, color: Colors.greenAccent)
                  else if (_answerResult != null &&
                      isSelected &&
                      !_answerResult!.isCorrect)
                    const Icon(Icons.cancel, color: Colors.redAccent),
                ],
              ),
            ),
          ),
        );
      }),
    );
  }

  Widget _buildResultSection() {
    final res = _answerResult!;
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: res.isCorrect
            ? Colors.green.withOpacity(0.12)
            : Colors.red.withOpacity(0.12),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: res.isCorrect ? Colors.greenAccent : Colors.redAccent,
          width: 1.5,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            children: [
              Icon(
                res.isCorrect ? Icons.emoji_events : Icons.info_outline,
                color: res.isCorrect ? Colors.greenAccent : Colors.redAccent,
                size: 26,
              ),
              const SizedBox(width: 10),
              Text(
                res.isCorrect ? 'Tebrikler! Doğru Cevap' : 'Maalesef Yanlış!',
                style: TextStyle(
                  color: res.isCorrect ? Colors.greenAccent : Colors.redAccent,
                  fontWeight: FontWeight.bold,
                  fontSize: 17,
                ),
              ),
              if (res.pointsAwarded > 0) ...[
                const Spacer(),
                Container(
                  padding:
                      const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.amber.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    '+${res.pointsAwarded} Puan',
                    style: const TextStyle(
                      color: Colors.amber,
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                    ),
                  ),
                ),
              ],
            ],
          ),
          const SizedBox(height: 12),
          Text(
            res.explanation,
            style: const TextStyle(
              color: Colors.white70,
              fontSize: 14,
              height: 1.4,
            ),
          ),
          if (res.badgeAwarded != null) ...[
            const SizedBox(height: 12),
            Container(
              padding: const EdgeInsets.all(10),
              decoration: BoxDecoration(
                color: Colors.amber.withOpacity(0.15),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: Colors.amberAccent),
              ),
              child: Row(
                children: [
                  const Text('🎖️', style: TextStyle(fontSize: 22)),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'Yeni Rozet Kazanıldı: ${res.badgeAwarded}',
                      style: const TextStyle(
                        color: Colors.amber,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
          if (res.shareCardUrl != null) ...[
            const SizedBox(height: 16),
            ElevatedButton.icon(
              onPressed: () {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Paylaşım kartı bağlantısı hazır!'),
                    backgroundColor: Colors.amber,
                  ),
                );
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.amber,
                foregroundColor: Colors.black87,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
              icon: const Icon(Icons.share, size: 18),
              label: const Text(
                'Başarını Sosyal Medyada Paylaş',
                style: TextStyle(fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildLawDisclaimer() {
    return const Text(
      '⚠️ T.C. 2863 sayılı Kültür ve Tabiat Varlıklarını Koruma Kanunu kapsamında platformumuzda sikke alım-satımı kesinlikle yasaktır. Yarışma içeriği nümizmatik eğitim ve kültür amaçlıdır.',
      textAlign: TextAlign.center,
      style: TextStyle(
        color: Colors.white38,
        fontSize: 11,
        height: 1.4,
      ),
    );
  }

  Widget _buildErrorState() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(Icons.quiz_outlined, color: Colors.white38, size: 64),
          const SizedBox(height: 12),
          const Text(
            'Bugünün sorusu yüklenemedi.',
            style: TextStyle(color: Colors.white70, fontSize: 16),
          ),
          const SizedBox(height: 12),
          ElevatedButton(
            onPressed: _fetchDailyChallenge,
            child: const Text('Tekrar Dene'),
          ),
        ],
      ),
    );
  }
}

