<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'quiz_id' => $this->quiz_id,
            'student_id' => $this->student_id,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'score' => $this->score,
            'total_questions' => $this->total_questions,
            'correct_answers' => $this->correct_answers,
            'percentage' => $this->percentage,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'quiz' => new QuizResource($this->whenLoaded('quiz')),
            'student' => new UserResource($this->whenLoaded('student')),
            'answers' => QuizAttemptAnswerResource::collection($this->whenLoaded('attemptAnswers'))
        ];
    }
}
