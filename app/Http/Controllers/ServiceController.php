<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class ServiceController extends Controller {
    protected $service;

    public function __construct(Service $service) {
        $this->service = $service;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $services = $this->service->all();
        return response()->json($services, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $service = $this->service->create($request->all());
        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): \Illuminate\Http\JsonResponse {
        $service = $this->service->find($id);
        if (!$service) return response()->json(['error' => 'Service not found'], 404);
        return response()->json($service, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        $service = $this->service->query()->find($id);
        if (!$service) return response()->json(['error' => 'Type service not found'], 404);

        $service->update($request->all());

        return response()->json($service, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $service = $this->service->query()->find($id);
        if (!$service) return response()->json(['error' => 'Local not found'], 404);
        $service->delete();
        return response()->json(null, 204);
    }
}
