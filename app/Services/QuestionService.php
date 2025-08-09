<?php

namespace App\Services;

use App\Models\Question;
use App\Repositories\Contracts\QuestionRepositoryInterface;

class QuestionService
{
    protected $questionRepository;

    public function __construct(QuestionRepositoryInterface $questionRepository)
    {
        $this->questionRepository = $questionRepository;
    }

    public function getAllQuestions()
    {
        return $this->questionRepository->all();
    }

    public function getActiveQuestions()
    {
        return $this->questionRepository->getActiveQuestions();
    }

    public function getQuestionsByCreator(int $userId)
    {
        return $this->questionRepository->getQuestionsByCreator($userId);
    }

    public function getQuestionWithOptions(int $questionId)
    {
        return $this->questionRepository->getQuestionWithOptions($questionId);
    }

    public function createQuestion(array $data, int $creatorId): Question
    {
        $data['created_by'] = $creatorId;

        $question = $this->questionRepository->create($data);

        if (isset($data['options'])) {
            $this->createQuestionOptions($question, $data['options']);
        }

        return $question;
    }

    public function updateQuestion(int $questionId, array $data): Question
    {
        $question = $this->questionRepository->update($questionId, $data);

        if (isset($data['options'])) {
            $question->options()->delete();
            $this->createQuestionOptions($question, $data['options']);
        }

        return $question;
    }

    public function deleteQuestion(int $questionId): bool
    {
        return $this->questionRepository->delete($questionId);
    }

    public function searchQuestions(string $search)
    {
        return $this->questionRepository->searchQuestions($search);
    }

    public function getQuestionsByType(int $type)
    {
        return $this->questionRepository->getQuestionsByType($type);
    }

    public function getQuestionsByDifficulty(int $difficulty)
    {
        return $this->questionRepository->getQuestionsByDifficulty($difficulty);
    }

    public function getRandomQuestions(int $count)
    {
        return $this->questionRepository->getRandomQuestions($count);
    }

    protected function createQuestionOptions(Question $question, array $options): void
    {
        foreach ($options as $index => $optionData) {
            $question->options()->create([
                'option_text' => $optionData['text'],
                'is_correct' => $optionData['is_correct'] ? "1" : "0",
                'order' => $index + 1
            ]);
        }
    }
}
