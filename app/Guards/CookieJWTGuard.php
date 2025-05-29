<?php

namespace App\Guards;

use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTGuard;

class CookieJWTGuard extends JWTGuard {
    public function __construct($jwt, $provider, Request $request) {        
        if (!$request->bearerToken() && $request->hasCookie('token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->cookie('token'));
        }

        parent::__construct($jwt, $provider, $request);
    }
}
