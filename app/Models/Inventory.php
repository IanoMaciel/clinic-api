<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model {
    use HasFactory;

    // inserção de forma massiva
    protected $fillable = [
        'name_item',
        'description_item',
        'reference',
        'category_id'
    ];

    // regras
    public function rules () {
        return [
            'name_item' => 'required|string',
            'description_item' => 'required|string',
            'reference' => 'nullable|string',
            'category_id' => 'required|integer|exists:categories,id'
        ];
    }

    // reposta das regras
    public function feedback() {
        return [
            'name_item.required' => 'O nome do item é obrigatório.',
            'name_item.string' => 'O nome do item deve ser uma string.',

            'description_item.required' => 'A descrição do item é obrigatória.',
            'description_item.string' => 'A descrição do item deve ser uma string.',

            'reference.string' => 'A referência deve ser uma string.',

            'category_id.required' => 'A categoria do item é obrigatória.',
            'category_id.integer' => 'A categoria deve ser um número inteiro.',
            'category_id.exists' => 'A categoria selecionada não é válida.',
        ];
    }

    // relacionamentos
    public function category () {
        return $this->belongsTo('App\Models\Category');
    }

}
