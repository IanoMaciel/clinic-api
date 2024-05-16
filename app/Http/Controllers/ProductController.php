<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class ProductController extends Controller {
    protected $product;

    public function __construct(Product $product) {
        $this->product = $product;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = $this->product->query();
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', '%' . $search . '%');
        }

        $products = $query->paginate(10);

        return response()->json($products, 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse {
        // verificar se o nome existe
        $nameExists = $this->product->where('name', $request->name)->exists();
        if($nameExists) return response()->json(['error' => 'Product name must be unique'], 422);

        $request->validate($this->product->rules());

        $product = $this->product->create($request->all());
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Integer $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): \Illuminate\Http\JsonResponse {
        $product = $this->product->find($id);
        if (!$product) abort(404, 'Product not found');
        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Integer $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse {
        $product = $this->product->find($id);

        if (!$product) abort(404, 'Product not found');

        $request->validate($this->product->rules());

        // Verifica se o nome fornecido no request já existe para outro produto
        if ($request->has('name') && $request->name !== $product->name) {
            $existingProduct = $this->product->where('name', $request->name)->first();
            if ($existingProduct && $existingProduct->id !== $product->id) {
                return response()->json(['error' => 'Product name must be unique.'], 422);
            }
        }

        // Se o nome não estiver presente no pedido ou for o mesmo que o atual, não o atualize
        if (!$request->has('name') || $request->name === $product->name) $request->request->remove('name');

        $product->update($request->all());

        return response()->json($product, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $product = $this->product->find($id);
        if (!$product) abort(404, 'Product not found');
        $product->delete();
        return response()->json(null, 204);
    }
}
