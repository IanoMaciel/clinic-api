<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method'];

    public function rules () {
        return [
            'payment_method' => 'required|string|max:50',
        ];
    }

    // relationship
    public function Order () {
        return $this->hasOne('App\Models\Order');
    }
}
