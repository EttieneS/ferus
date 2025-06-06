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
        $request->validate([
            'id' => 'required|integer|exists:users,id',
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);
                
        $userId = $request->input('id');
        $file = $request->file('file');

        if (!$file || !$file->isValid()) {
            Log::error('File not valid or missing', ['userId' => $userId]);
            return response()->json(['error' => 'File missing or invalid'], 422);
        }

        Log::info('FileController@uploadAvatar called', [
            'user_id' => $userId,
            'file' => $file->getClientOriginalName()
        ]);

        $path = $this->fileService->uploadAvatar($file, $userId);

        
        $this->userService->updateAvatar($userId, $path);
        return response()->json(['path' => $path]);
    }
}
