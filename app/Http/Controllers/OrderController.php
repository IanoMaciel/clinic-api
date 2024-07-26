<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller {

    protected $order;
    public function __construct(Order $order) {
        $this->order = $order;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse {
        try {
            $query = $this->order->query()->with(['customer', 'user', 'agreement', 'payment', 'products']);

            // Filtragem
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->whereHas('customer', function ($q) use ($search) {
                        $q->where('full_name', 'like', '%' . $search . '%');
                    })->orWhere('id', $search); // Filtrar por id do pedido
                });
            }

            $per_page = $request->get('per_page', 10);
            $histories = $query->paginate($per_page);

            return response()->json($histories, 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse {
        $request->validate($this->order->rules(), $this->order->feedback());

        $discount = $request->input('discount', null);

        // create a new order
        $order = $this->order->create($request->all());
        $order->products()->attach($request->products);

        $total = $order->products->sum(function ($product) {
            return $product->value;
        });

        if($discount) $total -= $discount;

        // update order with total vaçue
        $order->total = $total;
        $order->save();

        return response()->json([
            'message' => 'Order created successfully!',
            'order' => $order->load('products'),
        ]);
    }

    /**
     * @param Integer $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse {
        try {
            $order = $this->order->query()->find($id);

            if (!$order) return response()->json(['error' => 'Order not found'], 404);

            return response()->json($order, 200);
        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }


    /**
     * @param  Request  $request
     * @param  Integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse {
        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * * @param Integer $id
     * * @return JsonResponse
    */
    public function destroy($id): JsonResponse {
        try {
            $idAdmin = auth()->user();
            $admin = $idAdmin->getAttribute('is_admin');

            if (!$admin) return response()->json(['error' => 'Unauthorized'], 401);

            $order = $this->order->query()->find($id);

            if (!$order) return response()->json(['error' => 'Order not found'], 404);

            $order->delete();
            return response()->json(null, 204);

        } catch (\Exception $error) {
            return response()->json([
                'message' => 'Error processing request',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
