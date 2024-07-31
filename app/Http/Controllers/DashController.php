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
    public function gains(Request $request): JsonResponse {
        // get year and month
        $year = date('Y');
        $month = date('m');

        // filter by current month and year
        $totalGains = $this->order->whereYear('created_at', $year)->whereMonth('created_at', $month)->sum('total');

        return response()->json([
            'totalGains' => $totalGains
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function totalCustomers(): JsonResponse {
        $customers = $this->customer->all();
        $total = $customers->count();

        return response()->json(['totalCustomers' => $total], 200);
    }

    public function totalServices(): JsonResponse {
        $services = $this->product->all();
        $total = $services->count();

        return response()->json(['totalServices' => $total], 200);
    }

}
