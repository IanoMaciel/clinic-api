<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $payment;
    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        // 1. checks if the user has authorization
        if ($this->isAuthorized()) {
            return response([
                'error' => 'Unauthorized',
                Response::HTTP_UNAUTHORIZED
            ]);
        }

        // 2. validated
        $request->validate($this->payment->rules());

        // 3. create a new payment method
        $payment = $this->payment->query()->create($request->all());

        return response()->json($payment, Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Payment  $payments
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Payment  $payments
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Payment  $payments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Payment $payments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Payment  $payments
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payments)
    {
        //
    }

    private function isAuthorized(): bool {
        $authUser = auth()->user();
        $isAdmin = $authUser->getAttribute('is_admin');
        $isCommon = $authUser->getAttribute('is_common');

        // if you not user admin and common, it should return 401 (Unauthorized)
        if (!$isAdmin && !$isCommon) return true;
        else return false;
    }
}
