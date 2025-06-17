<?php

namespace App\Http\Controllers\API;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\UserService;

class FileController extends BaseController {
    protected FileService $fileService;
    protected UserService $userService;

    public function __construct(
        FileService $fileService,
        UserService $userService
    ) {
        $this->fileService = $fileService;
        $this->userService = $userService;
    }

    public function uploadAvatar(Request $request): JsonResponse {
        $user = $request->user();
        $userId = $user->id;

        

        $file = $request->file('file');
        
        $avatarPath = 'avatars/' . $user->avatar;
    
        if (!$file || !$file->isValid()) {
            return $this->sendError(
                'File upload error',
                [],
                422,
                'File is missing or unreadable.'
            );
        }

        try {
            $path = $this->fileService->uploadAvatar($file, $userId);
            $this->userService->updateAvatar($userId, $path);

            return $this->sendResponse(['path' => $path], 'Avatar uploaded successfully.');
        } catch (\Throwable $e) {
            return $this->sendError('Upload failed', [], 500, $e->getMessage());
        }
    }
}
