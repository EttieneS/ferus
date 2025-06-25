<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;

class BaseController extends Controller {
    public function sendResponse($result, $message) {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $errorMessages = [], $code = 404, $hint = null) {
        $response = [
            'success' => false,
            'message' => $error,
            'code' => $code,
        ];

        if (!empty($errorMessages)) {
            $response['message'] = json_encode($errorMessages);
        }

        return response()->json($response, $code);
    }
}
