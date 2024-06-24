<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'local_id',
        'service_id',
        'value_opt',
        'value'
    ];

    public function rules() {
        return [
            'name' => 'required|string',
            'description' => 'nullable|string',
            'local_id' => 'required|exists:locals,id',
            'service_id' => 'required|exists:services,id',
            'value_opt' => 'nullable|numeric',
            'value' => 'required|numeric',
        ];
    }

    public function schedule() {
        return $this->hasOne('App\Models\Scheduling');
    }

    public function orders() {
        return $this->belongsToMany('App\Models\Order', 'order_product');
    }
}
