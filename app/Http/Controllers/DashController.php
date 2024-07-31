<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Scheduling;

class DashController extends Controller {
    protected $order;
    protected $customer;
    protected $product;
    protected $scheduling;

    public function __construct(Order $order, Customer $customer, Product $product, Scheduling $scheduling) {
        $this->order = $order;
        $this->customer = $customer;
        $this->product = $product;
        $this->scheduling = $scheduling;
    }

    /**
     * @return JsonResponse
     */
    public function totalCustomers(): JsonResponse {
        $customers = $this->customer->all();
        $total = $customers->count();

        return response()->json(['totalCustomers' => $total], 200);
    }

}
