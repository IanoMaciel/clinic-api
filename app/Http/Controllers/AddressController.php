<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Customer;
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $addresses = $this->address->all();
        return response()->json($addresses, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        $customer_id = $request->get('customer_id');
        $customer = $this->customer->find($customer_id);

        if (!$customer) abort(404, 'Customer not found');

        $request->validate($this->address->rules());
        $address = $this->address->create($request->all());
        return response()->json($address, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $address = $this->address->find($id);
        if (!$address) abort(404, 'Address not found');
        return response()->json($address, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        $address = $this->address->find($id);
        if (!$address) abort(404, 'Address not found');

        $request->validate($this->address->rules());

        $address->update($request->all());
        return response()->json($address, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $address = $this->address->find($id);
        if (!$address) abort(404, 'Address not found');
        $address->delete();
        return response()->json(null, 204);
    }
}
