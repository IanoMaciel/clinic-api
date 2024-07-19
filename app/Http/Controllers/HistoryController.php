<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HistoryController extends Controller {
    protected $history;
    public function __construct(History $history) {
        $this->history = $history;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse {
        $query = $this->history->query()->with('customer');

        if ($request->has('search')) {
            $search = $request->get('search');
            // Adiciona um filtro para buscar pelo nome completo ou CPF do paciente na tabela 'customers'
            $query->whereHas('customer', function($q) use ($search) {
                $q->where('customers.full_name', 'LIKE', "%{$search}%")
                    ->orWhere('customers.cpf', 'LIKE', "%{$search}%");
            });
        }

        $per_page = $request->get('per_page', 10);
        $histories = $query->paginate($per_page);

        return response()->json($histories, 200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate($this->history->rules());
        $image = $request->file('history');
        $image_urn = $image->store('images', 'public');

        $history = $this->history->create([
            'customer_id' => $request->get('customer_id'),
            'history' => $image_urn,
        ]);

        return response()->json($history, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): \Illuminate\Http\JsonResponse {
        $history = $this->history->query()->with('customer')->find($id);
        if (!$history) return response()->json(['error' => 'History not found'], 404);
        return response()->json($history, 200);
    }

    /**
     * Regra de Negócio: somente ususário admin deverá atualizar
     * o histórico de paciente
     *
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Integer $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $history = $this->history->query()->find($id);

        if (!$history) return response()->json(['error' => 'History not found', 404]);

        if ($request->file('history')) Storage::disk('public')->delete($history->get('history'));

        $image = $request->file('history');
        $image_urn = $image->store('images', 'public');


        $history->update([
            'customer_id' => $request->get('customer_id'),
            'history' => $image_urn,
        ]);


//        if (!$history)
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id): \Illuminate\Http\JsonResponse {
        $history = $this->history->query()->find($id);

        if (!$history) return response()->json(['error' => 'History not found'], 404);

        if ($request->file('history')) Storage::disk('public')->delete($history->get('history'));

        // remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        $history->delete();

        return response()->json(['message' => 'History successfully removed'], 200);
    }
}
