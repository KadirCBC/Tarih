<?php

namespace App\Http\Controllers;

use App\Services\DailyCoinQuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function __construct(
        protected DailyCoinQuizService $quizService
    ) {}

    /**
     * Günün Sikkesi ve Nümizmatik Bilgi Yarışması Arayüzü.
     * GET /gunun-sikkesi (veya /quiz)
     */
    public function index(Request $request)
    {
        $challenge = $this->quizService->getTodayChallenge();
        $user = $request->user();
        $streak = $user ? $user->streak : null;
        $achievements = $user ? $user->achievements : collect();

        return view('quiz.index', compact('challenge', 'user', 'streak', 'achievements'));
    }

    /**
     * Soru Cevaplama Ucu (Web / AJAX).
     * POST /gunun-sikkesi/cevapla
     */
    public function answer(Request $request)
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return redirect()->route('quiz.index')->with('quiz_result', $result);
    }

    /**
     * Sosyal Medya Paylaşım Kartı Görseli (1200x630).
     * GET /gunun-sikkesi/paylasim-karti.png
     */
    public function shareCard(Request $request)
    {
        $isCorrect = (bool)$request->query('correct', 1);
        $streak = (int)$request->query('streak', 1);
        $points = (int)$request->query('points', 50);

        return $this->quizService->generateShareImage($isCorrect, $streak, $points);
    }
}

