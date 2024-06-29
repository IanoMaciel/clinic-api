<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;
    protected $fillable = ['customer_id'];

    public function rules () {
        return [
            'customer_id' => 'required|exists:customers,id',
            'products' => 'required|array',
            'products.*' => 'exists:products,id'
        ];
    }

    public function customer () {
        return $this->belongsTo('App\Models\Customer');
    }

    public function products () {
        return $this->belongsToMany('App\Models\Product', 'order_product');
    }
}
