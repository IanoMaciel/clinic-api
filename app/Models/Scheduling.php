<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'date_time',
        'description'
    ];

    public function rules ($update = false) {
        return [
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'date_time' => $update ? 'nullable|date_format:Y-m-d H:i:s|after:now' : 'required|date_format:Y-m-d H:i:s|after:now',
            'description' => 'nullable|string'
        ];
    }

    // Add relationship with customer
    public function customer () {
        return $this->belongsTo('App\Models\Customer');
    }

    public function product () {
        return $this->belongsTo('App\Models\Product');
    }
}
