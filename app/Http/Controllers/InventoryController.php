<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $query = $this->inventory->query();

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
        $inventory = $this->inventory->find($id);
        if (!$inventory) response()->json(['Inventory not found'], 404);
        return response()->json($inventory, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
