<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'question_text' => $this->question_text,
            'type' => $this->type,
            'type_label' => $this->type_label,
            'points' => $this->points,
            'explanation' => $this->explanation,
            'image' => $this->image,
            'difficulty' => $this->difficulty,
            'difficulty_label' => $this->difficulty_label,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'options' => QuestionOptionResource::collection($this->whenLoaded('options')),
            'creator' => new UserResource($this->whenLoaded('creator'))
        ];
    }
}
