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

        dd($id);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
