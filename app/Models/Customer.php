<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    use HasFactory;

    protected $fillable = [
        'full_name',
        'cpf',
        'birth_date',
        'phone_primary',
        'phone_secondary',
        'email'
    ];

    public function rules() {
        return [
            'full_name' => 'required|string|min:3|max:100',
            'cpf' => 'required',
//            'cpf' => 'required|cpf',
            'birth_date' => 'required|date|before_or_equal:today',
            'phone_primary' => 'required|string|regex:/^\d{10,11}$/',
            'phone_secondary' => 'string|regex:/^\d{10,11}$/',
            'email' => 'required|email|unique:customers,email'
        ];
    }
}
