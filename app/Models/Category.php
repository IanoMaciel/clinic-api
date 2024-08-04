<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use HasFactory;
    protected $fillable = ['category'];

    public function rules () {
        return [
            'category' => 'required|string'
        ];
    }

    public function feedback () {
       return [
           'category.required' => 'Categoria é um campo obrigatório.',
           'category.string' => 'Categoria deve ser do tipo string.'
       ];
    }

    // relationship
    public function Inventory () {
        return $this->hasOne('App\Models\Inventory');
    }
}
