<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'formacoes';
    protected $primaryKey = 'id_formacoes';

    protected $fillable = [
        'id_usuario',
        'instituicao',
        'curso',
        'data_inicio',
        'data_fim',
        'logo',
        'nivel',
        'situacao'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuarios');
    }
} 