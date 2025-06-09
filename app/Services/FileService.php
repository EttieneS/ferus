<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FileService {
    public function uploadAvatar(UploadedFile $file, int $userId): string {
        $ext = $file->getClientOriginalExtension();

        if (!$ext) {
            throw new RuntimeException('Invalid file extension.');
        }

        $uuid = Str::uuid()->toString();

        $fileName = 'avatar_' . $userId . '_' . $uuid . '.' . $ext;
        $path = 'avatars/' . $fileName;

        Storage::disk('public')->put($path, $file->getContent());
        return $fileName;
    }

    public function exists(string $path): bool {
        return Storage::disk('public')->exists($path);
    }

    public function delete(string $path): void {
        Storage::disk('public')->delete($path);
    }
}
