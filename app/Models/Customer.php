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
            'cpf' => 'required|cpf|formato_cpf|unique:customers,cpf',
            'birth_date' => 'nullable|date|before_or_equal:today',
            'phone_primary' => 'nullable|string|celular_com_ddd',
            'phone_secondary' => 'nullable|string|celular_com_ddd',
            'email' => 'nullable|email|unique:customers,email'
        ];
    }

    public function feedbacks() {
        return [
            // Mensagens para 'full_name'
            'full_name.required' => 'O campo nome completo é obrigatório.',
            'full_name.string' => 'O nome completo deve ser um texto.',
            'full_name.min' => 'O nome completo deve ter pelo menos 3 caracteres.',
            'full_name.max' => 'O nome completo não pode ter mais de 100 caracteres.',

            // Mensagens para 'cpf'
            'cpf.required' => 'O campo CPF é obrigatório.',
            'cpf.cpf' => 'O CPF informado não é válido.',
            'cpf.formato_cpf' => 'O formato do CPF é inválido. Use o formato 000.000.000-00.',
            'cpf.unique' => 'O CPF informado já está cadastrado no sistema.',

            // Mensagens para 'birth_date'
            'birth_date.date' => 'A data de nascimento deve ser uma data válida.',
            'birth_date.before_or_equal' => 'A data de nascimento deve ser uma data anterior ou igual a hoje.',

            // Mensagens para 'phone_primary'
            'phone_primary.string' => 'O telefone principal deve ser um texto.',
            'phone_primary.celular_com_ddd' => 'O telefone principal deve estar no formato válido com DDD.',

            // Mensagens para 'phone_secondary'
            'phone_secondary.string' => 'O telefone secundário deve ser um texto.',
            'phone_secondary.celular_com_ddd' => 'O telefone secundário deve estar no formato válido com DDD.',

            // Mensagens para 'email'
            'email.email' => 'O e-mail informado deve ser um endereço de e-mail válido.',
            'email.unique' => 'O e-mail informado já está cadastrado no sistema.',
        ];
    }

    // relationship -> hasMany with address
    public function address()
    {
        return $this->hasMany('App\Models\Address');
    }

    public function history()
    {
        return $this->hasMany('App\Models\History') ;
    }

    public function scheduling () {
        return $this->hasMany('App\Models\Scheduling');
    }

    public function orders() {
        return $this->hasMany('App\Models\Order');
    }

    // deleting related addresses and histories
    protected static function boot(){
        parent::boot();
        static::deleting(function ($customer){
            $customer->address()->delete();
            $customer->history()->delete();
        });
    }

}
