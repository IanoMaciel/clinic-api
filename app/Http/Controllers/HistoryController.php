<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\History;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HistoryController extends Controller {
    protected $history;
    protected $customer;

    public function __construct(History $history, Customer $customer) {
        $this->history = $history;
        $this->customer = $customer;
    }

    /**
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function findById(Request $request, int $id): JsonResponse {
        $customer = $this->customer->query()->find($id);
        if (!$customer) return response()->json(['error' => 'Usuário inválido'], 404);

        $histories = $this->history->query()
            ->where('customer_id', $id)
            ->orderBy('date_attachment', 'desc')
            ->get();

        return response()->json($histories);
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse {
        // Obtenha todos os IDs de clientes associados às histórias
        $historyCustomerIds = $this->history->query()->distinct()->pluck('customer_id');

        // Filtre os clientes com base nesses IDs
        $query =  $this->customer->query()->whereIn('id', $historyCustomerIds);

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('cpf', 'like', '%' . $search . '%');
            });
        }

        $per_page = $request->get('per_page', 10);
        $customers = $query->paginate($per_page);

        return response()->json($customers);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {
        $request->validate($this->history->rules(), $this->history->feedbacks());

        $histories = [];
        if ($request->hasFile('history')) {
            foreach ($request->file('history') as $file) {
                $image_urn = $file->store('images', 'public');

                // Cria uma entrada de histórico para cada arquivo
                $history = $this->history->query()->create([
                    'customer_id' => $request->get('customer_id'),
                    'date_attachment' => $request->get('date_attachment'),
                    'history' => $image_urn,
                ]);

                $histories[] = $history;
            }
        }

        return response()->json($histories, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse {
        $history = $this->history->query()->with('customer')->find($id);
        if (!$history) return response()->json(['error' => 'History not found'], 404);
        return response()->json($history);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse {
        // Busca o registro do histórico pelo ID
        $history = $this->history->query()->find($id);

        // Verifica se o histórico foi encontrado
        if (!$history) {
            return response()->json(['error' => 'History not found'], 404);
        }

        // Verifica se um novo arquivo foi enviado no request
        if ($request->hasFile('history')) {
            // Deleta o arquivo antigo, se existir
            if ($history->history) {
                Storage::disk('public')->delete($history->history);
            }

            // Armazena o novo arquivo e atualiza o caminho no banco
            $image = $request->file('history');
            $image_urn = $image->store('images', 'public');

            // Atualiza o campo 'history' com o novo caminho
            $history->history = $image_urn;
        }

        // Atualiza os outros campos do registro
        $history->customer_id = $request->get('customer_id');
        $history->date_attachment = $request->get('date_attachment');

        // Salva as alterações no banco de dados
        $history->save();

        // Retorna o histórico atualizado como resposta JSON
        return response()->json($history);
    }


    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function destroy(Request $request, $id): JsonResponse{
        $history = $this->history->query()->find($id);

        if (!$history) return response()->json(['error' => 'History not found'], 404);

        if ($request->file('history')) Storage::disk('public')->delete($history->get('history'));

        // remove o arquivo antigo caso um novo arquivo tenha sido enviado no request
        $history->delete();

        return response()->json(null,204);
    }
}
