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
     * @OA\Get(
     *     path="/api/customer",
     *     summary="Lista os clientes cadastrados",
     *     @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=true,
     *          @OA\Schema(type="string", default="application/json")
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $query = $this->customer->query()->with('address'); // Inicializa a consulta

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('cpf', 'like', '%' . $search . '%');
            });
        }


        if ($request->has('attributes')) {
            $attributes = $request->get('attributes');
            $query->selectRaw($attributes);
        }

        // Obtém os resultados paginados
        $per_page = $request->get('per_page', 10);
        $customers = $query->orderBy('full_name')->paginate($per_page);

        // Retorna os resultados em formato JSON
        return response()->json($customers, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/customer",
     *     summary="Cria novos clientes (pacientes)",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="full_name", type="string", description="Nome completo do cliente"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do cliente"),
     *                 @OA\Property(property="birth_date", type="string", description="Data de nascimento do cliente"),
     *                 @OA\Property(property="phone_primary", type="string", description="Telefone principal do cliente"),
     *                 @OA\Property(property="phone_secondary", type="string", description="Telefone secundário do cliente", nullable=true),
     *                 @OA\Property(property="email", type="string", description="Email do cliente", nullable=true),
     *                 example={
     *                     "full_name": "Iano de Benedito Maciel",
     *                     "cpf": "999.999.999-00",
     *                     "birth_date": "2000-04-07",
     *                     "phone_primary": "(99) 99999-9999",
     *                     "phone_secondary": "(99) 88888-8888",
     *                     "email": "email@email.com"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="created")
     * )
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
     * @OA\Get(
     *      path="/api/customer/{id}",
     *      summary="Mostra os detalhes de um cliente (paciente)",
     *     @OA\Parameter (
     *         name="id",
     *         in="path",
     *         description="ID do cliente",
     *         required=true,
     *         @OA\Schema (type="integer")
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="OK"
     *      ),
     *     @OA\Response (
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     *  )
     * @param Integer $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id) {
        $customer = $this->customer->query()->with('address')->find($id);

        if ($customer === null)  return response()->json(['message' => 'Customer not found'], 404);

        return response()->json($customer, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/customer/{id}",
     *     summary="Atualiza um cliente existente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do cliente",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="full_name", type="string", description="Nome completo do cliente"),
     *                 @OA\Property(property="cpf", type="string", description="CPF do cliente"),
     *                 @OA\Property(property="birth_date", type="string", description="Data de nascimento do cliente"),
     *                 @OA\Property(property="phone_primary", type="string", description="Telefone principal do cliente"),
     *                 @OA\Property(property="phone_secondary", type="string", description="Telefone secundário do cliente", nullable=true),
     *                 @OA\Property(property="email", type="string", description="Email do cliente", nullable=true),
     *                 example={
     *                     "full_name": "Iano de Benedito Maciel",
     *                     "cpf": "999.999.999-00",
     *                     "birth_date": "2000-04-07",
     *                     "phone_primary": "(99) 99999-9999",
     *                     "phone_secondary": "(99) 88888-8888",
     *                     "email": "email@email.com"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Já existe um cpf cadastrado no banco de dados"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/customer/{id}",
     *     summary="Remove um cliente",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID do cliente",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No Content"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cliente não encontrado"
     *     )
     * )
     *
     * @param Integer $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy ($id)
    {
        $customer = $this->customer->find($id);

        if (!$customer) return response()->json(['message' => 'Customer not found'], 404);

        $customer->delete();

        return response()->json(null, 204);
    }
}
