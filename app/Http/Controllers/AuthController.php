<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller {
    /**
     * @param Request $request
     */
    public function login(Request $request) {
        $credentials = $request->all(['email', 'password']);
        $token = auth('api')->attempt($credentials);

        if (!$token) {
            return response([
                'error' => 'Invalid credentials',
                Response::HTTP_UNAUTHORIZED
            ]);
        }

        $cookie = cookie('jwt', $token, 60 * 24); // 1 day

        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function logout() {
        // Invalida o token JWT
        auth('api')->logout();

        // Cria um cookie expirado para remover o cookie existente
        $cookie = Cookie::forget('jwt');

        // Retorna a resposta de logout com o cookie removido
        return response()->json(['message' => 'Successfully logged out'])->withCookie($cookie);
    }

    public function refresh() {
        $token = auth('api')->refresh();
        return response()->json(['token' => $token]);

    }

    public function me() {
        return auth()->user();
    }
}
