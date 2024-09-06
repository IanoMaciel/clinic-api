<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'user_id',
        'agreement_id',
        'payment_id',
        'discount',
        'total',
        'number_payments'
    ];

    public function rules() {
        return [
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array',
            'products.*' => 'exists:products,id',

            'user_id' => 'required|exists:users,id',
            'agreement_id' => 'nullable|exists:agreements,id',
            'payment_id' => 'required|exists:payments,id',
            'discount' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'number_payments' => 'nullable|numeric'
        ];
    }

    public function feedback()
    {
        return [
            'customer_id.required' => 'O campo do cliente é obrigatório.',
            'customer_id.exists' => 'O cliente selecionado não existe.',
            'products.required' => 'Os produtos são obrigatórios.',
            'products.array' => 'Os produtos devem estar em formato de array.',
            'products.*.exists' => 'Um ou mais produtos selecionados não existem.',
            'user_id.required' => 'O campo do usuário vendedor é obrigatório.',
            'user_id.exists' => 'O usuário vendedor selecionado não existe.',
            'agreement_id.exists' => 'O convênio selecionado não existe.',
            'payment_id.required' => 'O campo do pagamento é obrigatório.',
            'payment_id.exists' => 'O pagamento selecionado não existe.',
            'discount.numeric' => 'O desconto deve ser um campo numérico.',
            'total.numeric' => 'O total deve ser um campo numérico',
            'number_payments.numeric' => 'O numero de parcelas deve ser do tipo numérico.'
        ];
    }

    // relationships
    public function customer () {
        return $this->belongsTo('App\Models\Customer');
    }

    public function user () {
        return $this->belongsTo('App\Models\User');
    }

    public function agreement () {
        return $this->belongsTo('App\Models\Agreement');
    }

    public function payment () {
        return $this->belongsTo('App\Models\Payment');
    }

    public function products () {
        return $this->belongsToMany('App\Models\Product', 'order_product');
    }
}
