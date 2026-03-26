<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    public function storePublic(?UploadedFile $file, string $directory): ?string
    {
        if ($file === null) {
            return null;
        }

        return $file->store($directory, 'public');
    }

    public function deletePublic(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
