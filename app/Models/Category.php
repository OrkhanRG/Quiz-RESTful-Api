<?php

namespace App\Models;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function isActive(): bool
    {
        return $this->status === CategoryStatus::ACTIVE;
    }

    public function getStatusLabelAttribute(): string
    {
        return CategoryStatus::getLabel($this->status);
    }
}
