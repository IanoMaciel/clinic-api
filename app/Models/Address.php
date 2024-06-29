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
            'customer_id' => 'required:customers,id',
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

    // relationship -> belongsTo
    public function customer() {
        return $this->belongsTo('App\Models\Customer');
    }
}
