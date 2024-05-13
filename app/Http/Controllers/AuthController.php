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
        auth('api')->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh() {
        $token = auth('api')->refresh();
        return response()->json(['token' => $token]);

    }

    public function me() {
        $user = auth()->user();
        return response()->json($user);
    }
}
