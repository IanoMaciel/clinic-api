<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller {
    public function __construct(User $user) {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $users = $this->user->query()->paginate(10);
        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        $request->validate($this->user->rules());

        // is admin?
        $idAdmin = auth()->user();
        $admin = $idAdmin->getAttribute('is_admin');

        if (!$admin) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // create a new user
        $user = $this->user->query()->create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'is_admin' => $request->get('is_admin'),
            'is_common' => $request->get('is_common'),
            'is_esp' => $request->get('is_esp'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password'))
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $idAdmin = auth()->user();
        $admin = $idAdmin->getAttribute('is_admin');

        if (!$admin) return response()->json(['error' => 'Unauthorized'], 401);

        $user = $this->user->query()->find($id);

        return response()->json($user, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        // 1 - verifica se é um usuário admin e se possui autorização
        $idAdmin = auth()->user();
        $admin = $idAdmin->getAttribute('is_admin');

        if (!$admin) return response()->json(['error' => 'Unauthorized'], 401);

        // 2 - verifica se existe um id de usuário válido para atualizar
        $user = $this->user->query()->find($id);
        if (!$user) return response()->json(['error' => 'User not found', 404]);

        // 3 - inícia o método de atualização de registro do usuário
        // verifica se o email fornecido já existe para outro user no banco de dados
        $email = $request->get('email');
        $emailExists = $this->user->query()->where('email', $email)->where('id', '<>', $id)->exists();

        if ($emailExists) return response()->json(['message' => 'There is already a customer with this address email'], 400);

        // 4 - se tudo deu certo, atualiza o usuário.
        $user->update($request->all());
        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        // 1 - verifica se é um usuário admin e se possui autorização
        $idAdmin = auth()->user();
        $admin = $idAdmin->getAttribute('is_admin');

        if (!$admin) return response()->json(['error' => 'Unauthorized'], 401);

        // 2 - verifica se existe um id de usuário válido
        $user = $this->user->query()->find($id);
        if (!$user) return response()->json(['error' => 'User not found', 404]);

        // 3 - deleta o usuário
        $user->delete();
        return response()->json('null', 204);
    }

    private function isAdmin() {

    }
}
