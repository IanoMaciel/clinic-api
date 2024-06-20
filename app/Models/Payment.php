<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;
    protected $fillable = ['payment_method'];

    public function rules () {
        return [
            'payment_method' => 'required|string|max:50',
        ];
    }
}
