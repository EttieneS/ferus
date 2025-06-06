<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Illuminate\Support\Str;

class FileService {
    public function uploadAvatar(UploadedFile $file, int $userId): string {
        $ext = $file->getClientOriginalExtension();
        Log::info('FileService@uploadAvatar called', [
            'user_id' => $userId,
            'file' => $file->getClientOriginalName(),
            'extension' => $ext,
        ]);

        if (!$ext) {
            throw new RuntimeException('Invalid file extension.');
        }

        $uuid = Str::uuid()->toString();

        $fileName = 'avatar_'. $userId .'_'. $uuid .'.'. $ext;
        $path = 'app/public/avatars/' . $fileName;
        
        $file->storeAs('avatars', $path, 'public');
        return $fileName;
    }
}
