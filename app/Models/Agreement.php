<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    protected $fillable = ['agreement'];

    public function rules () {
        return [
            'agreement' => 'required|string|max:50',
        ];
    }

}
