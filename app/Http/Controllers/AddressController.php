<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class AddressController extends Controller {
    protected $address;
    protected $customer;

    public function __construct(Address $address, Customer $customer) {
        $this->address = $address;
        $this->customer = $customer;
    }

    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        $addresses = $this->address->all();
        return response()->json($addresses);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {
        $customer_id = $request->get('customer_id');
        $customer = $this->customer->query()->find($customer_id);

        if (!$customer) return response()->json(['error' => 'Cliente não existe'], 404);

        $request->validate($this->address->rules(), $this->address->feedbacks());
        $address = $this->address->query()->create($request->all());
        return response()->json($address, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $id
     * @return JsonResponse
     */
    public function show($id) {
        $address = $this->address->query()->find($id);
        if (!$address) abort(404, 'Endereço não existe.');
        return response()->json($address);
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse {
        $address = $this->address->query()->find($id);
        if (!$address) abort(404, 'Endereço não existe.');

        $request->validate($this->address->rules(), $this->address->feedbacks());

        $address->update($request->all());
        return response()->json($address);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse {
        $address = $this->address->query()->find($id);
        if (!$address) abort(404, 'Endereço não existe.');
        $address->delete();
        return response()->json(null, 204);
    }
}
