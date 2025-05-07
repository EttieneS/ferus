<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class AuthService {
    public function getToken(): ?string {
        $guard = Auth::guard('api');
        return $guard('api')->getToken()?->get();
    }

    public function getUserId(): ?int {
        return Auth::guard('api')->id();
    }
}
