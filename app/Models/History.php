<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'history'
    ];

    public function rules() {
        return [
            'customer_id' => 'required|exists:customers,id',
            'history' => 'required|file|mimes:png,pdf|max:2048' // aceita aquivos do tipo png e pdf no máximo 2MB
        ];
    }

    public function customer() {
        return $this->belongsTo('App\Models\Address');
    }

}
