<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;

class InventoryController extends Controller {
    protected $inventory;

    public function __construct(Inventory $inventory) {
        $this->inventory = $inventory;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse {
        $query = $this->inventory->query()->with('category');

        // filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('category', function ($q) use ($search) {
                    $q->where('category', 'like', '%' . $search . '%');
                });
            });
        }

        $per_page = $request->get('per_page', 10);
        $inventory = $query->paginate($per_page);

        return response()->json($inventory, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store (Request $request): JsonResponse {
        $request->validate($this->inventory->rules(), $this->inventory->feedback());

        // recupera o id da categoria
        $category_id = $request->get('category_id');
        $category = Category::find($category_id);

        // Gere o hash de 5 dígitos
        $hash = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $reference = $category->category . '_' . $hash;

        try {
            $inventory = $this->inventory->create($request->all());
            $inventory->reference = $reference;
            $inventory->save();
            return response()->json($inventory, 201);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse {
        $inventory = $this->inventory->with('category')->find($id);
        if (!$inventory) response()->json(['Inventory not found'], 404);
        return response()->json($inventory, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     * @param  Integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse {
        $request->validate($this->inventory->rules(), $this->inventory->feedback());

        $inventory = $this->inventory->find($id);
        if (!$inventory) response()->json(['Inventory not found'], 404);

        $originalCategoryId = $inventory->category_id; // Supondo que `category_id` é a coluna que armazena a categoria atual
        $newCategoryId = $request->get('category_id');

        $reference = null;
        if ($newCategoryId) {
            $category = Category::find($newCategoryId);
            if ($category) {
                $hash = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $reference = $category->category . '_' . $hash;
            }
        }

        // Atualiza o registro
        try {
            $inventory->fill($request->all());
            if ($originalCategoryId != $newCategoryId && $reference) {
                $inventory->reference = $reference;
            }
            $inventory->save();
            return response()->json($inventory, 200); // Código 200 é mais apropriado para sucesso
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse {
        $inventory = $this->inventory->find($id);

        if (!$inventory) {
            return response()->json(['error' => 'Inventory not found'], 404);
        }

        try {
            $inventory->delete();
            return response()->json(null, 204);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }

}
