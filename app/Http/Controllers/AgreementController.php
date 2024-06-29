<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgreementController extends Controller
{
    protected $agreement;
    public function __construct(Agreement $agreement) {
        $this->agreement = $agreement;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index() {
        $agreement = $this->agreement->query()->paginate(10);
        return response()->json($agreement, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request) {
        // 1. checks if the user has authorization
        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        // 2. validated
        $request->validate($this->agreement->rules());

        // 3. create a new payment method
        $agreement = $this->agreement->query()->create($request->all());

        return response()->json($agreement, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return JsonResponse
     */
    public function show($id) {
        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $agreement = $this->agreement->query()->find($id);
        if (!$agreement) return response()->json(['error' => 'Agreement not found'], Response::HTTP_NOT_FOUND);
        return response()->json($agreement, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  Integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id) {
        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $agreement = $this->agreement->query()->find($id);
        if (!$agreement) return response()->json(['error' => 'Agreement not found'], Response::HTTP_NOT_FOUND);

        $request->validate($this->agreement->rules());

        $agreement->update($request->all());

        return response()->json($agreement, Response::HTTP_CREATED );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return JsonResponse
     */
    public function destroy($id) {
        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $agreement = $this->agreement->query()->find($id);
        if (!$agreement) return response()->json(['error' => 'Agreement not found'], Response::HTTP_NOT_FOUND);

        $agreement->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    private function isAuthorized(): bool {
        $authUser = auth()->user();
        $isAdmin = $authUser->getAttribute('is_admin');
        $isCommon = $authUser->getAttribute('is_common');

        // if you not user admin and common, it should return 401 (Unauthorized)
        if (!$isAdmin && !$isCommon) return true;
        else return false;
    }
}
