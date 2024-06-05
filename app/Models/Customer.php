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
            'cpf' => 'required|cpf|formato_cpf',
            'birth_date' => 'required|date|before_or_equal:today',
            'phone_primary' => 'required|string|celular_com_ddd',
            'phone_secondary' => 'string|celular_com_ddd',
            'email' => 'email|unique:customers,email'
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

    // deleting related addresses and histories
    protected static function boot(){
        parent::boot();
        static::deleting(function ($customer){
            $customer->address()->delete();
            $customer->history()->delete();
        });
    }

}
