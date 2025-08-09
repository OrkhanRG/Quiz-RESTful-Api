<?php

namespace App\Services;

use App\Repositories\Contracts\QuizRepositoryInterface;
use App\Models\QuizAttempt;
use App\Exceptions\QuizException;

use App\Repositories\QuizAttemptRepository;
use App\Enums\{
    QuizStatus,
    QuizAttemptStatus,
    QuestionType
};

use App\Models\{
    Question,
    Quiz,
    QuizAttemptAnswer
};

class QuizService
{
    protected $quizRepository;
    protected $attemptRepository;

    public function __construct(
        QuizRepositoryInterface $quizRepository,
        QuizAttemptRepository $attemptRepository
    )
    {
        $this->quizRepository = $quizRepository;
        $this->attemptRepository = $attemptRepository;
    }

    public function createQuiz(array $data, int $teacherId): Quiz
    {
        $data['teacher_id'] = $teacherId;
        $data['status'] = $data['status'] ?? QuizStatus::DRAFT;
        if (array_key_exists("show_results_immediately", $data)) {
            $data["show_results_immediately"] = $data["show_results_immediately"] ? "1" : "0";
        }

        if (array_key_exists("shuffle_questions", $data)) {
            $data["shuffle_questions"] = $data["shuffle_questions"] ? "1" : "0";
        }

        $quiz = $this->quizRepository->create($data);

        if (isset($data['questions'])) {
            $this->attachQuestionsToQuiz($quiz, $data['questions']);
        }

        return $quiz;
    }

    public function publishQuiz(int $quizId): Quiz
    {
        $quiz = $this->quizRepository->findOrFail($quizId);

        if ($quiz->questions()->count() === 0) {
            throw new QuizException('Testdə ən azı bir sual olmalıdır.');
        }

        return $this->quizRepository->update($quizId, ['status' => QuizStatus::ACTIVE]);
    }

    public function startQuizAttempt(int $quizId, int $studentId): QuizAttempt
    {
        $quiz = $this->quizRepository->getQuizWithQuestions($quizId);

        if (!$quiz->isActive()) {
            throw new QuizException('Test aktiv deyil və ya vaxtı keçmişdir.');
        }

        $existingAttempts = $quiz->attempts()
            ->where('student_id', $studentId)
            ->where('status', QuizAttemptStatus::COMPLETED)
            ->count();

        if ($existingAttempts >= $quiz->max_attempts) {
            throw new QuizException('Maksimum cəhd sayı aşılmışdır.');
        }

        $ongoingAttempt = $quiz->attempts()
            ->where('student_id', $studentId)
            ->where('status', QuizAttemptStatus::IN_PROGRESS)
            ->first();

        if ($ongoingAttempt) {
            return $ongoingAttempt;
        }

        return QuizAttempt::create([
            'quiz_id' => $quizId,
            'student_id' => $studentId,
            'started_at' => now(),
            'total_questions' => $quiz->questions->count(),
            'status' => QuizAttemptStatus::IN_PROGRESS
        ]);
    }

    public function submitQuizAnswer(int $attemptId, int $questionId, array $answerData): QuizAttemptAnswer
    {
        $attempt = QuizAttempt::findOrFail($attemptId);

        if (!$attempt->isInProgress()) {
            throw new QuizException('Test cəhdi davam etmir.');
        }

        $question = Question::with('options')->findOrFail($questionId);

        $isCorrect = $this->evaluateAnswer($question, $answerData);
        $pointsEarned = $isCorrect ? $question->points : 0;

        return QuizAttemptAnswer::updateOrCreate(
            [
                'attempt_id' => $attemptId,
                'question_id' => $questionId
            ],
            [
                'selected_options' => $answerData['selected_options'] ?? null,
                'text_answer' => $answerData['text_answer'] ?? null,
                'is_correct' => $isCorrect ? '1' : '0',
                'points_earned' => $pointsEarned,
                'answered_at' => now()
            ]
        );
    }

    public function completeQuizAttempt(int $attemptId): QuizAttempt
    {
        $attempt = QuizAttempt::with('attemptAnswers')->findOrFail($attemptId);

        $correctAnswers = $attempt->attemptAnswers->where('is_correct', '1')->count();
        $totalScore = $attempt->attemptAnswers->sum('points_earned');

        $attempt->update([
            'completed_at' => now(),
            'correct_answers' => $correctAnswers,
            'score' => $totalScore,
            'status' => QuizAttemptStatus::COMPLETED
        ]);

        return $attempt;
    }

    protected function attachQuestionsToQuiz(Quiz $quiz, array $questionIds): void
    {
        $questionsWithOrder = [];
        foreach ($questionIds as $index => $questionId) {
            $questionsWithOrder[$questionId] = ['order' => $index + 1];
        }

        $quiz->questions()->sync($questionsWithOrder);
    }

    protected function evaluateAnswer(Question $question, array $answerData): ?bool
    {
        switch ($question->type) {
            case QuestionType::MULTIPLE_CHOICE:
                $selectedOptions = $answerData['selected_options'] ?? [];
                $correctOptions = $question->options->where('is_correct', '1')->pluck('id')->toArray();

                return empty(array_diff($selectedOptions, $correctOptions)) &&
                    empty(array_diff($correctOptions, $selectedOptions));

            case QuestionType::TRUE_FALSE:
                $selectedOption = $answerData['selected_options'][0] ?? null;
                return $question->options->where('id', $selectedOption)->first()?->isCorrect() ?? false;

            case QuestionType::TEXT:
                $userAnswer = strtolower(trim($answerData['text_answer'] ?? ''));
                $correctAnswers = $question->options->where('is_correct', '1');

                foreach ($correctAnswers as $option) {
                    if (strtolower(trim($option->option_text)) === $userAnswer) {
                        return true;
                    }
                }
                return false;

            case QuestionType::ESSAY:
                return null;
            default:
                return false;
        }
    }

    public function getQuizzesByTeacher(int $teacherId)
    {
        return $this->quizRepository->getQuizzesByTeacher($teacherId);
    }

    public function getActiveQuizzes()
    {
        return $this->quizRepository->getActiveQuizzes();
    }

    public function getQuizWithQuestions(int $quizId): Quiz
    {
        return $this->quizRepository->getQuizWithQuestions($quizId);
    }

    public function updateQuiz(int $quizId, array $data): Quiz
    {
        if (array_key_exists("show_results_immediately", $data)) {
            $data["show_results_immediately"] = $data["show_results_immediately"] ? "1" : "0";
        }

        if (array_key_exists("shuffle_questions", $data)) {
            $data["shuffle_questions"] = $data["shuffle_questions"] ? "1" : "0";
        }

        $quiz = $this->quizRepository->update($quizId, $data);

        if (isset($data['questions'])) {
            $this->attachQuestionsToQuiz($quiz, $data['questions']);
        }

        return $quiz;
    }

    public function deleteQuiz(int $quizId): bool
    {
        return $this->quizRepository->delete($quizId);
    }

    public function archiveQuiz(int $quizId): Quiz
    {
        return $this->quizRepository->update($quizId, ['status' => QuizStatus::ARCHIVED]);
    }

    public function abandonQuizAttempt(int $attemptId): QuizAttempt
    {
        $attempt = QuizAttempt::findOrFail($attemptId);

        $attempt->update([
            'completed_at' => now(),
            'status' => QuizAttemptStatus::ABANDONED
        ]);

        return $attempt;
    }

    public function getAttemptWithResults(int $attemptId): QuizAttempt
    {
        return QuizAttempt::with([
            'quiz',
            'student',
            'attemptAnswers.question.options'
        ])->findOrFail($attemptId);
    }

    public function getAttemptStatistics(int $attemptId): array
    {
        return $this->attemptRepository->getAttemptStatistics($attemptId);
    }

    public function getQuizAttempts(int $quizId)
    {
        return $this->quizRepository->getQuizAttempts($quizId);
    }

    public function getQuizStatistics(int $quizId): array
    {
        return $this->quizRepository->getQuizStatistics($quizId);
    }

    public function searchQuizzes(string $search)
    {
        return $this->quizRepository->searchQuizzes($search);
    }

    public function getQuizzesByCategory(int $categoryId)
    {
        return $this->quizRepository->getQuizzesByCategory($categoryId);
    }

    public function getPopularQuizzes(int $limit = 10)
    {
        return $this->quizRepository->getPopularQuizzes($limit);
    }

    public function getDraftQuizzes()
    {
        return $this->quizRepository->getDraftQuizzes();
    }
}
