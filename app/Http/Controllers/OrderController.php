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
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request) {
        $query = $this->order->query()->with([
            'customer:id,full_name,cpf',
            'user:id,first_name,last_name',
            'agreement:id,agreement',
            'payment:id,payment_method',
            'products:id,name,value'
        ]);

        $per_page = $request->get('per_page', 10);
        $histories = $query->paginate($per_page);

        return response()->json($histories, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request) {
        $request->validate($this->order->rules(), $this->order->feedback());

        $discount = $request->input('discount', null);

        // cria orders
        $order = $this->order->create($request->all());
        $order->products()->attach($request->products);

        $total = $order->products->sum(function ($product) {
            return $product->value;
        });

        if($discount) $total -= $discount;

        // atualiza a order adicionado o valor do total;
        $order->total = $total;
        $order->save();

        return response()->json([
            'message' => 'Order created successfully!',
            'order' => $order->load('products'),
        ]);
    }

    /**
     * Display the specified resource.
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
//        $order = $this->order->
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
