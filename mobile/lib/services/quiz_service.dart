import 'package:dio/dio.dart';
import '../core/api_client.dart';

class DailyQuizModel {
  final int questionId;
  final String questionType;
  final String questionText;
  final List<String> options;
  final String? coinName;
  final String? obverseImage;
  final int currentStreak;
  final int longestStreak;
  final int totalPoints;
  final List<dynamic> badges;

  DailyQuizModel({
    required this.questionId,
    required this.questionType,
    required this.questionText,
    required this.options,
    this.coinName,
    this.obverseImage,
    required this.currentStreak,
    required this.longestStreak,
    required this.totalPoints,
    required this.badges,
  });

  factory DailyQuizModel.fromJson(Map<String, dynamic> json) {
    final question = json['question'] as Map<String, dynamic>? ?? {};
    final progress = json['user_progress'] as Map<String, dynamic>? ?? {};
    final coin = question['coin'] as Map<String, dynamic>? ?? {};

    return DailyQuizModel(
      questionId: question['id'] ?? 0,
      questionType: question['question_type'] ?? '',
      questionText: question['question_text'] ?? '',
      options: (question['options'] as List<dynamic>?)
              ?.map((e) => e.toString())
              .toList() ??
          [],
      coinName: coin['name'],
      obverseImage: coin['obverse_image'],
      currentStreak: progress['streak_days'] ?? 0,
      longestStreak: progress['longest_streak'] ?? 0,
      totalPoints: progress['total_points'] ?? 0,
      badges: progress['badges'] as List<dynamic>? ?? [],
    );
  }
}

class QuizAnswerResult {
  final bool isCorrect;
  final int correctOptionIndex;
  final String explanation;
  final int pointsAwarded;
  final int streakDays;
  final int totalPoints;
  final String? badgeAwarded;
  final String? shareCardUrl;

  QuizAnswerResult({
    required this.isCorrect,
    required this.correctOptionIndex,
    required this.explanation,
    required this.pointsAwarded,
    required this.streakDays,
    required this.totalPoints,
    this.badgeAwarded,
    this.shareCardUrl,
  });

  factory QuizAnswerResult.fromJson(Map<String, dynamic> json) {
    return QuizAnswerResult(
      isCorrect: json['is_correct'] ?? false,
      correctOptionIndex: json['correct_option_index'] ?? 0,
      explanation: json['explanation'] ?? '',
      pointsAwarded: json['points_awarded'] ?? 0,
      streakDays: json['streak_days'] ?? 0,
      totalPoints: json['total_points'] ?? 0,
      badgeAwarded: json['badge_awarded'],
      shareCardUrl: json['share_card_url'],
    );
  }
}

class QuizService {
  final ApiClient _apiClient = ApiClient();

  Future<DailyQuizModel?> getDailyChallenge() async {
    try {
      final response = await _apiClient.dio.get('/quiz/daily');
      if (response.statusCode == 200 && response.data['success'] == true) {
        return DailyQuizModel.fromJson(response.data['data']);
      }
    } on DioException catch (e) {
      // Fallback dummy when offline or testing
      return null;
    }
    return null;
  }

  Future<QuizAnswerResult?> submitAnswer(int questionId, int selectedIndex) async {
    try {
      final response = await _apiClient.dio.post(
        '/quiz/answer',
        data: {
          'question_id': questionId,
          'selected_option_index': selectedIndex,
        },
      );

      if (response.statusCode == 200 && response.data['success'] == true) {
        return QuizAnswerResult.fromJson(response.data['data']);
      }
    } on DioException catch (e) {
      return null;
    }
    return null;
  }
}

