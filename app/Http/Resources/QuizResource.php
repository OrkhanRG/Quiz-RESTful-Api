<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'teacher_id' => $this->teacher_id,
            'time_limit' => $this->time_limit,
            'max_attempts' => $this->max_attempts,
            'shuffle_questions' => $this->shuffle_questions,
            'show_results_immediately' => $this->show_results_immediately,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'teacher' => new UserResource($this->whenLoaded('teacher')),
            'questions' => QuestionResource::collection($this->whenLoaded('questions')),
            'attempts_count' => $this->when(isset($this->attempts_count), $this->attempts_count),
            'questions_count' => $this->when($this->relationLoaded('questions'), $this->questions->count())
        ];
    }
}
