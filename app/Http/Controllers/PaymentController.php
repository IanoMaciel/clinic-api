<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\JsonResponse;
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
     * @return JsonResponse
     */
    public function index() {
        $payment_methods = $this->payment->query()->paginate(10);
        return response()->json($payment_methods, Response::HTTP_OK);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) {
        // 1. checks if the user has authorization
        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
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
     * @param  Integer $id
     * @return JsonResponse
     */
    public function show($id) {

        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $payment_method = $this->payment->query()->find($id);
        if (!$payment_method) return response()->json(['error' => 'Payment not found'], Response::HTTP_NOT_FOUND);
        return response()->json($payment_method, Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  Integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id) {

        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $payment_method = $this->payment->query()->find($id);
        if (!$payment_method) return response()->json(['error' => 'Payment not found'], Response::HTTP_NOT_FOUND);

        $request->validate($this->payment->rules());

        $payment_method->update($request->all());

        return response()->json($payment_method, Response::HTTP_CREATED );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer $id
     * @return JsonResponse
     */
    public function destroy($id) {

        if ($this->isAuthorized()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $payment_method = $this->payment->query()->find($id);
        if (!$payment_method) return response()->json(['error' => 'Payment not found'], Response::HTTP_NOT_FOUND);

        $payment_method->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
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
