<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    protected $disk = 'public';
    protected $path = 'images';

    public function uploadQuestionImage(UploadedFile $file): string
    {
        $filename = $this->generateUniqueFilename($file);
        $path = $this->path . '/questions/' . $filename;

        Storage::disk($this->disk)->put($path, file_get_contents($file));

        return $path;
    }

    public function deleteImage(string $path): bool
    {
        if (Storage::disk($this->disk)->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }

        return false;
    }

    protected function generateUniqueFilename(UploadedFile $file): string
    {
        return Str::uuid() . '.' . $file->getClientOriginalExtension();
    }

    public function getImageUrl(string $path): string
    {
        return Storage::disk($this->disk)->url($path);
    }
}
