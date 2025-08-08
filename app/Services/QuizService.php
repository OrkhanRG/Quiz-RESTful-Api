<?php

namespace App\Services;

use App\Enums\QuestionType;
use App\Enums\QuizAttemptStatus;
use App\Enums\QuizStatus;
use App\Exceptions\QuizException;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAttemptAnswer;
use App\Repositories\Contracts\QuizRepositoryInterface;

class QuizService
{
    protected $quizRepository;

    public function __construct(QuizRepositoryInterface $quizRepository)
    {
        $this->quizRepository = $quizRepository;
    }

    public function createQuiz(array $data, int $teacherId): Quiz
    {
        $data['teacher_id'] = $teacherId;
        $data['status'] = $data['status'] ?? QuizStatus::DRAFT;

        $quiz = $this->quizRepository->create($data);

        if (isset($data['questions'])) {
            $this->attachQuestionsToQuiz($quiz, $data['questions']);
        }

        return $quiz;
    }

    public function publishQuiz(int $quizId): Quiz
    {
        $quiz = $this->quizRepository->find($quizId);

        if ($quiz->questions()->count() === 0) {
            throw new QuizException('Testdə ən azı bir sual olmalıdır.');
        }

        return $this->quizRepository->update($quizId, ['status' => QuizStatus::ACTIVE]);
    }

    public function startQuizAttempt(int $quizId, int $studentId): QuizAttempt
    {
        $quiz = $this->quizRepository->getQuizWithQuestions($quizId);

        // Check if quiz is active
        if (!$quiz->isActive()) {
            throw new QuizException('Test aktiv deyil və ya vaxtı keçmişdir.');
        }

        // Check attempt limits
        $existingAttempts = $quiz->attempts()
            ->where('student_id', $studentId)
            ->where('status', QuizAttemptStatus::COMPLETED)
            ->count();

        if ($existingAttempts >= $quiz->max_attempts) {
            throw new QuizException('Maksimum cəhd sayı aşılmışdır.');
        }

        // Check if user has an ongoing attempt
        $ongoingAttempt = $quiz->attempts()
            ->where('student_id', $studentId)
            ->where('status', QuizAttemptStatus::IN_PROGRESS)
            ->first();

        if ($ongoingAttempt) {
            return $ongoingAttempt;
        }

        // Create new attempt
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
                'is_correct' => $isCorrect ? 1 : 0,
                'points_earned' => $pointsEarned,
                'answered_at' => now()
            ]
        );
    }

    public function completeQuizAttempt(int $attemptId): QuizAttempt
    {
        $attempt = QuizAttempt::with('attemptAnswers')->findOrFail($attemptId);

        $correctAnswers = $attempt->attemptAnswers->where('is_correct', 1)->count();
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

    protected function evaluateAnswer(Question $question, array $answerData): bool|null
    {
        switch ($question->type) {
            case QuestionType::MULTIPLE_CHOICE:
                $selectedOptions = $answerData['selected_options'] ?? [];
                $correctOptions = $question->options->where('is_correct', 1)->pluck('id')->toArray();

                return empty(array_diff($selectedOptions, $correctOptions)) &&
                    empty(array_diff($correctOptions, $selectedOptions));

            case QuestionType::TRUE_FALSE:
                $selectedOption = $answerData['selected_options'][0] ?? null;
                return $question->options->where('id', $selectedOption)->first()?->isCorrect() ?? false;

            case QuestionType::TEXT:
                $userAnswer = strtolower(trim($answerData['text_answer'] ?? ''));
                $correctAnswers = $question->options->where('is_correct', 1);

                foreach ($correctAnswers as $option) {
                    if (strtolower(trim($option->option_text)) === $userAnswer) {
                        return true;
                    }
                }
                return false;

            case QuestionType::ESSAY:
                // Essay questions need manual grading
                return null;

            default:
                return false;
        }
    }
}
