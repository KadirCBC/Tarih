<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DailyCoinQuizService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuizApiController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected DailyCoinQuizService $quizService
    ) {}

    /**
     * Günün Sikkesi Sorusunu Getirir.
     * GET /api/v1/quiz/daily
     */
    public function daily(Request $request): JsonResponse
    {
        $challenge = $this->quizService->getTodayChallenge();
        $user = $request->user();

        return $this->success([
            'challenge' => $challenge,
            'user_stats' => [
                'points' => $user ? $user->numismatic_points : 0,
                'streak_days' => $user ? $user->streak->current_streak_days : 0,
                'badges_count' => $user ? $user->achievements()->count() : 0,
            ],
        ]);
    }

    /**
     * Günün Sorusuna Cevap Gönderir.
     * POST /api/v1/quiz/answer
     */
    public function answer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question_id' => 'required|integer',
            'selected_option' => 'required|integer',
        ]);

        $result = $this->quizService->answerChallenge(
            questionId: $validated['question_id'],
            selectedOptionIndex: $validated['selected_option'],
            user: $request->user()
        );

        return $this->success($result);
    }
}

