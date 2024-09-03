<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'history',
        'date_attachment'
    ];

    public function rules() {
        return [
            'customer_id' => 'required|exists:customers,id',
            'history' => 'required|array',
            'history.*' => 'required|file|mimes:png,jpeg|max:2048', // aceita aquivos do tipo png e pdf no máximo 2MB
            'date_attachment' => 'required|date'
        ];
    }

    public function feedbacks() {
        return [
            // Mensagens para 'customer_id'
            'customer_id.required' => 'O campo cliente é obrigatório.',
            'customer_id.exists' => 'O cliente selecionado não existe no sistema.',

            // Mensagens para 'history'
            'history.required' => 'É necessário enviar pelo menos um anexo.',
            'history.array' => 'Os anexos devem ser enviados como um conjunto de arquivos.',
            'history.*.required' => 'Cada anexo deve ser um arquivo válido.',
            'history.*.file' => 'Cada item deve ser um arquivo válido.',
            'history.*.mimes' => 'Os anexos devem ser dos tipos: PNG ou JPEG.',
            'history.*.max' => 'Cada arquivo não pode ultrapassar 2MB.',

            // Mensagens para 'date_attachment'
            'date_attachment.required' => 'O campo data de anexo é obrigatório.',
            'date_attachment.date' => 'A data de anexo deve ser uma data válida.',
        ];
    }

    public function customer() {
        return $this->belongsTo('App\Models\Customer');
    }
}
