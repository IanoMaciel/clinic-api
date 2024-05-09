<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller {
    /**
     * @param Request $request
     */
    public function login(Request $request) {
        $credentials = $request->all(['email', 'password']);
        $token = auth('api')->attempt($credentials);

        if ($token) {
            return response()->json(['token' => $token]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout() {
        return 'ok';
    }

    public function refresh() {
        return 'ok';
    }

    public function me() {
        return 'ok';
    }
}
