<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'service_id',
        'local_id',
        'date_time',
        'description'
    ];

    public function rules () {
        return [
            'customer_id' => 'required|exists:customers,id',
            'service_id' => 'required|exists:services,id',
            'local_id' => 'required|exists:locals,id',
            'date_time' => 'required|date_format:Y-m-d H:i:s|after:now',
            'description' => 'nullable|string'
        ];
    }
}
