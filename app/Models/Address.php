<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'cep',
        'uf',
        'city',
        'street',
        'number',
        'neighborhood',
        'complement',
        'reference',
    ];

    public function rules() {
        return [
            'customer_id' => 'required|exists:customers,id',
            'cep' => 'required|string|formato_cep',
            'uf' => 'required|string|uf',
            'city' => 'required|string',
            'street' => 'required|string',
            'number' => 'nullable|string',
            'neighborhood' => 'required|string',
            'complement' => 'nullable|string',
            'reference' => 'nullable|string',
        ];
    }

    public function feedbacks() {
        return [
            'customer_id.required' => 'O campo cliente é obrigatório.',
            'customer_id.exists' => 'O cliente selecionado é inválido.',
            'cep.required' => 'O campo CEP é obrigatório.',
            'cep.formato_cep' => 'O formato do CEP é inválido.',
            'uf.required' => 'O campo UF é obrigatório.',
            'uf.uf' => 'O campo UF deve ser válido.',
            'city.required' => 'O campo cidade é obrigatório.',
            'street.required' => 'O campo rua é obrigatório.',
            'neighborhood.required' => 'O campo bairro é obrigatório.',
        ];
    }

    // relationship -> belongsTo
    public function customer() {
        return $this->belongsTo('App\Models\Customer');
    }
}
