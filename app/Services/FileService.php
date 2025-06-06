<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileService {
    public function uploadAvatar(UploadedFile $file, int $userId): string {
        $testContent = "✅ FileService working\nUser ID: $userId\nFile: " . $file->getClientOriginalName();
        $fileName = 'debug_' . $userId . '.txt';

        // Try write to storage/app/public/debug/
        $path = storage_path("app/public/avatar_debug");
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        file_put_contents("$path/$fileName", $testContent);

        return "public/debug/$fileName";

        // $ext = $file->getClientOriginalExtension();
        // Log::info('FileService@uploadAvatar called', [
        //     'user_id' => $userId,
        //     'file' => $file->getClientOriginalName(),
        //     'extension' => $ext,
        // ]);

        // if (!$ext) {
        //     throw new \RuntimeException('Invalid file extension.');
        // }

        // $fileName = 'avatar_' . $userId . '.' . $ext;
        // $path = 'avatars/' . $fileName;

        // // if (Storage::disk('public')->exists($path)) {
        // //     Storage::disk('public')->delete($path);
        // // }

        // return $file->storeAs('avatars', $fileName, 'public');
    }
}
