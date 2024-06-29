<?php

namespace App\Http\Controllers;

use App\Models\Local;
use Illuminate\Http\Request;

class LocalController extends Controller {
    protected $local;

    public function __construct(Local $local) {
        $this->local = $local;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $locals = $this->local->all();
        return response()->json($locals, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        $local = $this->local->create($request->all());
        return response()->json($local, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $local = $this->local->find($id);
        if (!$local) return response()->json(['error' => 'Service not found'], 404);
        return response()->json($local, 200);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Integer $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        $local = $this->local->query()->find($id);
        if (!$local) return response()->json(['error' => 'Local not found'], 404);

        $local->update($request->all());

        return response()->json($local, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $local = $this->local->query()->find($id);
        if (!$local) return response()->json(['error' => 'Local not found'], 404);
        $local->delete();
        return response()->json(null, 204);
    }
}
