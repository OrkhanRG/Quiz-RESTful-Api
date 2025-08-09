<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'slug' => $this->slug,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'quizzes' => QuizResource::collection($this->whenLoaded('quizzes')),
            'quizzes_count' => $this->when($this->relationLoaded('quizzes'), $this->quizzes->count())
        ];
    }
}
