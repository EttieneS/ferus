<?php

namespace App\Http\Controllers\API;

use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class FileController extends BaseController {
    protected FileService $fileService;

    public function __construct(FileService $fileService) {
        $this->fileService = $fileService;
    }

    public function uploadAvatar(Request $request): JsonResponse {
        $request->validate([
            'id' => 'required|integer|exists:users,id',
            'file' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);


        // Log::info('FileController@uploadAvatar called', [
        //     'user_id' => $request->input('id'),
        //     'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : null,
        // ]);

        // $path = $this->fileService->uploadAvatar($request->file('file'), $request->input('id'));

        // return response()->json([
        //     'message' => 'Avatar uploaded successfully.',
        //     'path' => $path,
        // ]);

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

        return response()->json(['path' => $path]);
    }
}
