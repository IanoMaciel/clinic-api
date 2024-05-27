<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller {
    protected $customer;

    public function __construct(Customer $customer) {
        $this->customer = $customer;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $query = $this->customer->query()->with('address'); // Inicializa a consulta

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('full_name', 'like', '%' . $search . '%');
        }

        if ($request->has('attributes')) {
            $attributes = $request->get('attributes');
            $query->selectRaw($attributes);
        }

        // Obtém os resultados paginados
        $customers = $query->orderBy('full_name')->paginate(10);

        // Retorna os resultados em formato JSON
        return response()->json($customers, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate($this->customer->rules());
        $customer = $this->customer->create($request->all());
        return response()->json($customer, 201);
    }

    /**
     * Display the specified resource.
     * @param Integer $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id) {
        $customer = $this->customer->query()->with('address')->find($id);

        if ($customer === null)  return response()->json(['message' => 'Customer not found'], 404);

        return response()->json($customer, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Integer $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        $customer = $this->customer->find($id);
        $cpf = $request->get('cpf');
        $email = $request->get('email');

        if ($customer === null)  {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // verifica se o CPF fornecido já existe para outro cliente no banco de dados
        $cpfExists = $this->customer->where('cpf', $cpf)->where('id', '<>', $id)->exists();

        if ($cpfExists) {
            return response()->json(['message' => 'There is already a customer with this CPF number'], 400);
        }

        // verifica se o email fornecido já eixste para outro cliente no banco de dados
        $emailExists = $this->customer->where('email', $email)->where('id', '<>', $id)->exists();

        if ($emailExists) {
            return response()->json(['message' => 'There is already a customer with this address email'], 400);
        }

        $customer->update($request->all());
        return response()->json($customer, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $customer = $this->customer->find($id);

        if ($customer === null) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

//        // deletar os endereços associar se houver
//        $customer->address()->delete();
//
//        // agora deleta o registro do cliente
//        $customer->delete();
        $customer->delete();

        return response()->json(null, 204);
    }
}
