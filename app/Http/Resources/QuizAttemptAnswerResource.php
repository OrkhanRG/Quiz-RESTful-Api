<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizAttemptAnswerResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'attempt_id' => $this->attempt_id,
            'question_id' => $this->question_id,
            'selected_options' => $this->selected_options,
            'text_answer' => $this->text_answer,
            'is_correct' => $this->is_correct,
            'points_earned' => $this->points_earned,
            'answered_at' => $this->answered_at,
            'question' => new QuestionResource($this->whenLoaded('question'))
        ];
    }
}

